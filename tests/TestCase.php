<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Start session for CSRF token generation
        $this->startSession();
    }

    /**
     * Create a CSRF token for use in tests
     */
    protected function getCsrfToken(): string
    {
        return csrf_token();
    }

    /**
     * Make a POST request with CSRF token
     */
    public function postWithCsrf($uri, array $data = [], array $headers = [])
    {
        $data["_token"] = $this->getCsrfToken();
        return $this->post($uri, $data, $headers);
    }

    /**
     * Make a PUT request with CSRF token
     */
    public function putWithCsrf($uri, array $data = [], array $headers = [])
    {
        $data["_token"] = $this->getCsrfToken();
        return $this->put($uri, $data, $headers);
    }

    /**
     * Make a DELETE request with CSRF token
     */
    public function deleteWithCsrf($uri, array $data = [], array $headers = [])
    {
        $data["_token"] = $this->getCsrfToken();
        return $this->delete($uri, $data, $headers);
    }
}
