<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inviter_id')->constrained('users')->onDelete('cascade');
            $table->string('email');
            $table->string('token')->unique();
            $table->string('nickname')->nullable();
            $table->text('access_note')->nullable();
            $table->timestamp('expires_at')->nullable(); // For trusted contact expiration
            $table->timestamp('invitation_expires_at'); // For invitation expiration
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'accepted', 'expired', 'cancelled'])->default('pending');
            $table->timestamps();

            // Prevent duplicate pending invitations
            $table->unique(['inviter_id', 'email', 'status']);

            // Add indexes for performance
            $table->index(['token']);
            $table->index(['email', 'status']);
            $table->index(['inviter_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_invitations');
    }
};
