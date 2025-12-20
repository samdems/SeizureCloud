<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Seizure;
use App\Models\Medication;
use App\Models\TrustedContact;
use App\Models\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        // Middleware is already applied via routes, no need to add it here
    }

    /**
     * Display the admin dashboard
     */
    public function index()
    {
        $stats = [
            "total_users" => User::count(),
            "admin_users" => User::where("is_admin", true)->count(),
            "patient_accounts" => User::where(
                "account_type",
                "patient",
            )->count(),
            "carer_accounts" => User::where("account_type", "carer")->count(),
            "medical_accounts" => User::where(
                "account_type",
                "medical",
            )->count(),
            "total_seizures" => Seizure::count(),
            "total_medications" => Medication::count(),
            "active_trusted_contacts" => TrustedContact::where(
                "is_active",
                true,
            )->count(),
            "pending_invitations" => UserInvitation::where(
                "status",
                "pending",
            )->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();
        $recentSeizures = Seizure::with("user")->latest()->take(5)->get();

        return view(
            "admin.dashboard",
            compact("stats", "recentUsers", "recentSeizures"),
        );
    }

    /**
     * Display user management page
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled("search")) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%")->orWhere(
                    "email",
                    "like",
                    "%{$search}%",
                );
            });
        }

        // Filter by account type
        if ($request->filled("account_type")) {
            $query->where("account_type", $request->account_type);
        }

        // Filter by admin status
        if ($request->filled("admin_filter")) {
            if ($request->admin_filter === "admin") {
                $query->where("is_admin", true);
            } elseif ($request->admin_filter === "regular") {
                $query->where("is_admin", false);
            }
        }

        $users = $query->orderBy("created_at", "desc")->paginate(20);

        return view("admin.users.index", compact("users"));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load([
            "seizures" => function ($query) {
                $query->latest()->take(10);
            },
            "medications",
            "trustedContacts" => function ($query) {
                $query->where("is_active", true);
            },
            "trustedAccounts" => function ($query) {
                $query->where("is_active", true);
            },
        ]);

        $stats = [
            "seizure_count" => $user->seizures()->count(),
            "medication_count" => $user->medications()->count(),
            "trusted_contacts_count" => $user
                ->trustedContacts()
                ->where("is_active", true)
                ->count(),
            "trusted_accounts_count" => $user
                ->trustedAccounts()
                ->where("is_active", true)
                ->count(),
        ];

        return view("admin.users.show", compact("user", "stats"));
    }

    /**
     * Edit user form
     */
    public function editUser(User $user)
    {
        return view("admin.users.edit", compact("user"));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => [
                "required",
                "email",
                "max:255",
                Rule::unique("users")->ignore($user->id),
            ],
            "account_type" => "required|in:patient,carer,medical",
            "is_admin" => "boolean",
            "password" => "nullable|string|min:8|confirmed",
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Only update password if provided
        if (empty($data["password"])) {
            unset($data["password"]);
        } else {
            $data["password"] = Hash::make($data["password"]);
        }

        $user->update($data);

        return redirect()
            ->route("admin.users.show", $user)
            ->with("success", "User updated successfully");
    }

    /**
     * Toggle admin status
     */
    public function toggleAdmin(User $user)
    {
        $user->setAdminStatus(!$user->is_admin);

        $status = $user->is_admin ? "promoted to admin" : "demoted from admin";

        return back()->with("success", "User {$status} successfully");
    }

    /**
     * Deactivate user account
     */
    public function deactivateUser(User $user)
    {
        // Prevent deactivating the current admin user
        if ($user->id === auth()->id()) {
            return back()->with(
                "error",
                "You cannot deactivate your own account",
            );
        }

        // For now, we'll just mark them as unverified
        // In a full implementation, you might want a separate 'active' field
        $user->email_verified_at = null;
        $user->save();

        return back()->with("success", "User account deactivated successfully");
    }

    /**
     * Activate user account
     */
    public function activateUser(User $user)
    {
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        return back()->with("success", "User account activated successfully");
    }

    /**
     * Delete user account
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting the current admin user
        if ($user->id === auth()->id()) {
            return back()->with("error", "You cannot delete your own account");
        }

        // Delete related data first
        $user->seizures()->delete();
        $user->medications()->delete();
        $user->trustedContacts()->delete();
        $user->trustedAccounts()->delete();
        $user->vitals()->delete();
        $user->observations()->delete();

        $user->delete();

        return redirect()
            ->route("admin.users.index")
            ->with("success", "User and all related data deleted successfully");
    }

    /**
     * Show system settings
     */
    public function settings()
    {
        return view("admin.settings");
    }

    /**
     * Show system logs/activity
     */
    public function logs()
    {
        // This would show system logs, user activities, etc.
        // For now, just show recent user registrations and admin actions
        $recentRegistrations = User::latest()->take(20)->get();

        return view("admin.logs", compact("recentRegistrations"));
    }

    /**
     * Export user data
     */
    public function exportUsers()
    {
        $users = User::all();

        $filename = "users_export_" . date("Y-m-d_H-i-s") . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($users) {
            $file = fopen("php://output", "w");

            // CSV headers
            fputcsv($file, [
                "ID",
                "Name",
                "Email",
                "Account Type",
                "Is Admin",
                "Email Verified",
                "Created At",
                "Last Login",
            ]);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->account_type,
                    $user->is_admin ? "Yes" : "No",
                    $user->email_verified_at ? "Yes" : "No",
                    $user->created_at->format("Y-m-d H:i:s"),
                    $user->updated_at->format("Y-m-d H:i:s"),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
