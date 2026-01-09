<?php

use App\Models\Seizure;
use App\Models\User;
use App\Services\VideoUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake("private");
    $this->user = User::factory()->create([
        "account_type" => "patient",
        "email_verified_at" => now(),
    ]);
    $this->actingAs($this->user);
});

test("can upload video when updating seizure", function () {
    $seizure = Seizure::factory()->create([
        "user_id" => $this->user->id,
        "start_time" => now()->subHour(),
        "severity" => 5,
    ]);

    $videoFile = UploadedFile::fake()->create(
        "seizure_video.mp4",
        1024,
        "video/mp4",
    );

    $response = $this->put(route("seizures.update", $seizure), [
        "start_time" => $seizure->start_time->format("Y-m-d\TH:i"),
        "severity" => $seizure->severity,
        "video_upload" => $videoFile,
    ]);

    $response->assertRedirect();

    $seizure->refresh();
    expect($seizure->video_file_path)->not()->toBeNull();
    expect($seizure->video_public_token)->not()->toBeNull();
    expect($seizure->video_expires_at)->toBeNull();
    expect($seizure->has_video_evidence)->toBeTrue();
});

test("can delete video from seizure", function () {
    $seizure = Seizure::factory()->create([
        "user_id" => $this->user->id,
        "start_time" => now()->subHour(),
        "severity" => 5,
        "video_file_path" => "seizure-videos/test_video.mp4",
        "video_public_token" => "test-token-12345",
        "video_expires_at" => null,
        "has_video_evidence" => true,
    ]);

    Storage::disk("private")->put(
        "seizure-videos/test_video.mp4",
        "fake video content",
    );

    $response = $this->delete(route("seizures.video.delete", $seizure));

    $response->assertRedirect();
    $response->assertSessionHas("success");

    $seizure->refresh();
    expect($seizure->video_file_path)->toBeNull();
    expect($seizure->video_public_token)->toBeNull();
    expect($seizure->video_expires_at)->toBeNull();
    expect($seizure->has_video_evidence)->toBeFalse();
});

test("can regenerate video token", function () {
    $seizure = Seizure::factory()->create([
        "user_id" => $this->user->id,
        "start_time" => now()->subHour(),
        "severity" => 5,
        "video_file_path" => "seizure-videos/test_video.mp4",
        "video_public_token" => "old-token-12345",
        "video_expires_at" => null,
        "has_video_evidence" => true,
    ]);

    $oldToken = $seizure->video_public_token;

    $response = $this->post(route("seizures.video.regenerate-token", $seizure));

    $response->assertRedirect();
    $response->assertSessionHas("success");

    $seizure->refresh();
    expect($seizure->video_public_token)->not()->toBe($oldToken);
    expect($seizure->video_expires_at)->toBeNull();
});

test("can view video with valid token", function () {
    $seizure = Seizure::factory()->create([
        "user_id" => $this->user->id,
        "start_time" => now()->subHour(),
        "severity" => 5,
        "video_file_path" => "seizure-videos/test_video.mp4",
        "video_public_token" => "valid-token-12345",
        "video_expires_at" => null,
        "has_video_evidence" => true,
    ]);

    Storage::disk("private")->put(
        "seizure-videos/test_video.mp4",
        "fake video content",
    );

    $response = $this->get(route("seizures.video.view", "valid-token-12345"));

    $response->assertStatus(200);
    $response->assertHeader("Content-Type");
});

test("cannot view video with invalid token", function () {
    $response = $this->get(route("seizures.video.view", "invalid-token"));

    $response->assertStatus(404);
});

test("videos are permanently accessible", function () {
    $seizure = Seizure::factory()->create([
        "user_id" => $this->user->id,
        "start_time" => now()->subHour(),
        "severity" => 5,
        "video_file_path" => "seizure-videos/test_video.mp4",
        "video_public_token" => "permanent-token-12345",
        "video_expires_at" => null,
        "has_video_evidence" => true,
    ]);

    Storage::disk("private")->put(
        "seizure-videos/test_video.mp4",
        "fake video content",
    );

    $response = $this->get(
        route("seizures.video.view", "permanent-token-12345"),
    );

    $response->assertStatus(200);
});

test("validates video file type and size", function () {
    $seizure = Seizure::factory()->create([
        "user_id" => $this->user->id,
        "start_time" => now()->subHour(),
        "severity" => 5,
    ]);

    // Test invalid file type
    $invalidFile = UploadedFile::fake()->create(
        "document.pdf",
        1024,
        "application/pdf",
    );

    $response = $this->put(route("seizures.update", $seizure), [
        "start_time" => $seizure->start_time->format("Y-m-d\TH:i"),
        "severity" => $seizure->severity,
        "video_upload" => $invalidFile,
    ]);

    $response->assertSessionHasErrors(["video_upload"]);

    // Test file too large (over 100MB)
    $largeFile = UploadedFile::fake()->create(
        "large_video.mp4",
        102400,
        "video/mp4",
    ); // 102400KB = ~100MB

    $response = $this->put(route("seizures.update", $seizure), [
        "start_time" => $seizure->start_time->format("Y-m-d\TH:i"),
        "severity" => $seizure->severity,
        "video_upload" => $largeFile,
    ]);

    $response->assertSessionHasErrors(["video_upload"]);
});

test("video upload during seizure creation works", function () {
    $videoFile = UploadedFile::fake()->create(
        "seizure_video.mp4",
        1024,
        "video/mp4",
    );

    $response = $this->post(route("seizures.store"), [
        "start_time" => now()->subHour()->format("Y-m-d\TH:i"),
        "severity" => 7,
        "has_video_evidence" => true,
        "video_upload" => $videoFile,
        "video_notes" => "This video shows the seizure clearly",
    ]);

    $response->assertRedirect(route("seizures.index"));

    $seizure = Seizure::where("user_id", $this->user->id)->first();
    expect($seizure)->not()->toBeNull();
    expect($seizure->video_file_path)->not()->toBeNull();
    expect($seizure->video_public_token)->not()->toBeNull();
    expect($seizure->has_video_evidence)->toBeTrue();
    expect($seizure->video_notes)->toBe("This video shows the seizure clearly");
});

test("video service generates secure tokens correctly", function () {
    $videoService = app(VideoUploadService::class);
    $seizure = Seizure::factory()->create([
        "user_id" => $this->user->id,
        "start_time" => now()->subHour(),
        "severity" => 5,
    ]);

    $videoFile = UploadedFile::fake()->create(
        "test_video.mp4",
        1024,
        "video/mp4",
    );

    $result = $videoService->uploadVideo($seizure, $videoFile);

    expect($result)->toBeTrue();

    $seizure->refresh();
    expect($seizure->video_public_token)->toHaveLength(64);
    expect($seizure->hasValidVideo())->toBeTrue();
    expect($seizure->getVideoPublicUrl())->toContain(
        $seizure->video_public_token,
    );
});

test("videos do not expire and cleanup returns zero", function () {
    $seizure = Seizure::factory()->create([
        "user_id" => $this->user->id,
        "start_time" => now()->subHour(),
        "severity" => 5,
        "video_file_path" => "seizure-videos/test_video.mp4",
        "video_public_token" => "permanent-token-12345",
        "video_expires_at" => null,
        "has_video_evidence" => true,
    ]);

    $videoService = app(VideoUploadService::class);
    $cleanedCount = $videoService->cleanupExpiredTokens();

    expect($cleanedCount)->toBe(0);

    $seizure->refresh();
    expect($seizure->video_public_token)->toBe("permanent-token-12345");
    expect($seizure->video_expires_at)->toBeNull();
});
