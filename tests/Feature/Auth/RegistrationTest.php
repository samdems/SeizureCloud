<?php

test("registration screen can be rendered", function () {
    $response = $this->get(route("register"));

    $response->assertStatus(200);
});

test("new users can register", function () {
    $response = $this->postWithCsrf(route("register.store"), [
        "name" => "John Doe",
        "email" => "test@example.com",
        "password" => "password",
        "password_confirmation" => "password",
        "account_type" => "patient",
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route("dashboard", absolute: false));

    $this->assertAuthenticated();
});
