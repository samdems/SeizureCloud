<?php

// Legal pages don't require database access

test("privacy policy page can be accessed", function () {
    $response = $this->get(route("legal.privacy"));

    $response->assertStatus(200);
    $response->assertSee("Privacy Policy");
    $response->assertSee("Your privacy and health data protection");
    $response->assertSee("health information");
    $response->assertSee("personal information");
});

test("terms of service page can be accessed", function () {
    $response = $this->get(route("legal.terms"));

    $response->assertStatus(200);
    $response->assertSee("Terms of Service");
    $response->assertSee("Legal agreement for using Epilepsy Diary");
    $response->assertSee("Acceptance of Terms");
    $response->assertSee("Medical Disclaimer");
});

test("legal pages have proper navigation links", function () {
    $privacyResponse = $this->get(route("legal.privacy"));
    $termsResponse = $this->get(route("legal.terms"));

    // Privacy page should link to terms
    $privacyResponse->assertSee(route("legal.terms"));

    // Terms page should link to privacy
    $termsResponse->assertSee(route("legal.privacy"));

    // Both pages should have proper titles
    $privacyResponse->assertSee("Privacy Policy", false);
    $termsResponse->assertSee("Terms of Service", false);
});

test("legal routes are properly named", function () {
    expect(route("legal.privacy"))->toEndWith("/privacy");
    expect(route("legal.terms"))->toEndWith("/terms");
});

test("legal pages contain required sections", function () {
    $privacyResponse = $this->get(route("legal.privacy"));
    $termsResponse = $this->get(route("legal.terms"));

    // Privacy Policy required sections
    $privacyResponse->assertSee("Information We Collect");
    $privacyResponse->assertSee("Data Security");
    $privacyResponse->assertSee("Your Rights and Choices");
    $privacyResponse->assertSee("Contact Information");

    // Terms of Service required sections
    $termsResponse->assertSee("Medical Disclaimer");
    $termsResponse->assertSee("Acceptable Use");
    $termsResponse->assertSee("Limitation of Liability");
    $termsResponse->assertSee("Emergency Situations");
});

test("legal pages are accessible without authentication", function () {
    // Test that guests can access legal pages
    $privacyResponse = $this->get(route("legal.privacy"));
    $termsResponse = $this->get(route("legal.terms"));

    $privacyResponse->assertStatus(200);
    $termsResponse->assertStatus(200);
});
