<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserInvitation;
use App\Models\TrustedContact;
use App\Notifications\TrustedContactInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function user_can_invite_non_existing_user_as_trusted_contact()
    {
        $user = User::factory()->create();
        $email = "newuser@example.com";

        $response = $this->actingAs($user)->post("/settings/trusted-contacts", [
            "email" => $email,
            "nickname" => "Emergency Contact",
            "access_note" => "Family member for emergencies",
        ]);

        $response->assertRedirect("/settings/trusted-contacts");
        $response->assertSessionHas("success");

        // Check invitation was created
        $this->assertDatabaseHas("user_invitations", [
            "inviter_id" => $user->id,
            "email" => $email,
            "nickname" => "Emergency Contact",
            "status" => "pending",
        ]);

        // Check notification was sent
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable(),
            TrustedContactInvitation::class,
        );
    }

    /** @test */
    public function user_can_invite_existing_user_directly()
    {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create();

        $response = $this->actingAs($inviter)->post(
            "/settings/trusted-contacts",
            [
                "email" => $invitee->email,
                "nickname" => "Family Member",
                "access_note" => "My spouse",
            ],
        );

        $response->assertRedirect("/settings/trusted-contacts");
        $response->assertSessionHas("success");

        // Check trusted contact was created directly
        $this->assertDatabaseHas("trusted_contacts", [
            "user_id" => $inviter->id,
            "trusted_user_id" => $invitee->id,
            "nickname" => "Family Member",
            "is_active" => true,
        ]);

        // No invitation should be created for existing users
        $this->assertDatabaseMissing("user_invitations", [
            "inviter_id" => $inviter->id,
            "email" => $invitee->email,
        ]);
    }

    /** @test */
    public function user_cannot_invite_themselves()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/settings/trusted-contacts", [
            "email" => $user->email,
            "nickname" => "Myself",
        ]);

        $response->assertSessionHasErrors(["email"]);
        $this->assertDatabaseMissing("user_invitations", [
            "inviter_id" => $user->id,
            "email" => $user->email,
        ]);
    }

    /** @test */
    public function user_cannot_create_duplicate_invitation()
    {
        $user = User::factory()->create();
        $email = "test@example.com";

        // Create first invitation
        UserInvitation::create([
            "inviter_id" => $user->id,
            "email" => $email,
            "nickname" => "Contact 1",
            "status" => "pending",
            "invitation_expires_at" => now()->addDays(7),
        ]);

        // Try to create duplicate
        $response = $this->actingAs($user)->post("/settings/trusted-contacts", [
            "email" => $email,
            "nickname" => "Contact 2",
        ]);

        $response->assertSessionHasErrors(["email"]);

        // Should only have one invitation
        $this->assertEquals(1, UserInvitation::where("email", $email)->count());
    }

    /** @test */
    public function valid_invitation_can_be_accepted()
    {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $inviter->id,
            "email" => "invitee@example.com",
            "status" => "pending",
            "invitation_expires_at" => now()->addDays(7),
        ]);

        // Register new user with invitation token
        $response = $this->post("/register", [
            "name" => "New User",
            "email" => "invitee@example.com",
            "password" => "password123",
            "password_confirmation" => "password123",
            "account_type" => "carer",
            "invitation_token" => $invitation->token,
            "terms" => true,
        ]);

        $response->assertRedirect("/dashboard");

        // Check user was created
        $newUser = User::where("email", "invitee@example.com")->first();
        $this->assertNotNull($newUser);

        // Check email was automatically verified since they clicked the invitation link
        $this->assertNotNull($newUser->email_verified_at);

        // Check invitation was marked as accepted
        $invitation->refresh();
        $this->assertEquals("accepted", $invitation->status);
        $this->assertEquals($newUser->id, $invitation->accepted_user_id);

        // Check trusted contact was created
        $this->assertDatabaseHas("trusted_contacts", [
            "user_id" => $inviter->id,
            "trusted_user_id" => $newUser->id,
            "is_active" => true,
        ]);
    }

    /** @test */
    public function expired_invitation_cannot_be_accepted()
    {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $inviter->id,
            "email" => "invitee@example.com",
            "status" => "pending",
            "invitation_expires_at" => now()->subDay(), // Expired
        ]);

        $response = $this->get("/invitations/" . $invitation->token);

        $response->assertOk();
        $response->assertViewIs("invitations.expired");
    }

    /** @test */
    public function invitation_can_be_cancelled()
    {
        $user = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $user->id,
            "status" => "pending",
        ]);

        $response = $this->actingAs($user)->post(
            "/invitations/" . $invitation->id . "/cancel",
        );

        $response->assertRedirect();
        $response->assertSessionHas("success");

        $invitation->refresh();
        $this->assertEquals("cancelled", $invitation->status);
    }

    /** @test */
    public function invitation_can_be_resent_if_expired()
    {
        $user = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $user->id,
            "status" => "pending",
            "invitation_expires_at" => now()->subDay(), // Expired
        ]);

        $response = $this->actingAs($user)->post(
            "/invitations/" . $invitation->id . "/resend",
        );

        $response->assertRedirect();
        $response->assertSessionHas("success");

        $invitation->refresh();
        $this->assertEquals("pending", $invitation->status);
        $this->assertTrue($invitation->invitation_expires_at->isFuture());
    }

    /** @test */
    public function user_sees_pending_invitations_in_trusted_contacts_list()
    {
        $user = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $user->id,
            "email" => "pending@example.com",
            "nickname" => "Pending Contact",
            "status" => "pending",
        ]);

        $response = $this->actingAs($user)->get("/settings/trusted-contacts");

        $response->assertOk();
        $response->assertSee("Pending Invitations");
        $response->assertSee("pending@example.com");
        $response->assertSee("Pending Contact");
    }

    /** @test */
    public function existing_user_can_accept_invitation_after_login()
    {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create(["email" => "existing@example.com"]);

        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $inviter->id,
            "email" => "existing@example.com",
            "status" => "pending",
            "invitation_expires_at" => now()->addDays(7),
        ]);

        // User visits invitation link while logged in as the correct user
        $response = $this->actingAs($invitee)->get(
            "/invitations/" . $invitation->token,
        );

        $response->assertRedirect("/dashboard");
        $response->assertSessionHas("success");

        // Check invitation was accepted and trusted contact created
        $invitation->refresh();
        $this->assertEquals("accepted", $invitation->status);

        $this->assertDatabaseHas("trusted_contacts", [
            "user_id" => $inviter->id,
            "trusted_user_id" => $invitee->id,
            "is_active" => true,
        ]);
    }

    /** @test */
    public function wrong_user_sees_email_mismatch_page()
    {
        $inviter = User::factory()->create();
        $wrongUser = User::factory()->create(["email" => "wrong@example.com"]);

        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $inviter->id,
            "email" => "correct@example.com",
            "status" => "pending",
        ]);

        $response = $this->actingAs($wrongUser)->get(
            "/invitations/" . $invitation->token,
        );

        $response->assertOk();
        $response->assertViewIs("invitations.email-mismatch");
        $response->assertSee("Email Address Mismatch");
    }

    /** @test */
    public function invitation_shows_login_page_for_existing_user_not_logged_in()
    {
        $inviter = User::factory()->create();
        $existingUser = User::factory()->create([
            "email" => "existing@example.com",
        ]);

        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $inviter->id,
            "email" => "existing@example.com",
            "status" => "pending",
        ]);

        $response = $this->get("/invitations/" . $invitation->token);

        $response->assertOk();
        $response->assertViewIs("invitations.login-required");
        $response->assertSee("Login Required");
        $response->assertSee("existing@example.com");
    }

    /** @test */
    public function processed_invitation_shows_appropriate_message()
    {
        $inviter = User::factory()->create();
        $acceptedUser = User::factory()->create();

        $invitation = UserInvitation::factory()->create([
            "inviter_id" => $inviter->id,
            "email" => "accepted@example.com",
            "status" => "accepted",
            "accepted_user_id" => $acceptedUser->id,
            "accepted_at" => now()->subDay(),
        ]);

        $response = $this->get("/invitations/" . $invitation->token);

        $response->assertOk();
        $response->assertViewIs("invitations.already-processed");
        $response->assertSee("Invitation Already Accepted");
    }
}
