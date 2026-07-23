<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase; 

class AuthenticationTest extends TestCase
{
    /**
     * A basic authentication test example.
     */
    public function test_authentication_routes(): void
    {
        // Test the registration page
        $response = $this->get('/register');
        $response->assertStatus(200);

        // Test the login page
        $response = $this->get('/login');
        $response->assertStatus(200);


        // Test the password reset request page
        $response = $this->get('/password/reset');
        $response->assertStatus(200);

        // Test the email verification notice page (if email verification is enabled)
        $response = $this->get('/email/verify');
        $response->assertStatus(200);

        // Test the email verification notice page (if email verification is enabled)
        $response = $this->get('/email/verify');
        $response->assertStatus(200);

    }
}
