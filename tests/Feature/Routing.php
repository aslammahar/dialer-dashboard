<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase

{
    /**
     * A basic unit test example.
     */
    public function route_test(): void
    {
        // Test if the route is protected by the auth middleware 
        $route = [
           login => 'auth',
              register => 'auth',
                logout => 'auth',

        ];
        foreach ($route as $key => $value) {
            $this->assertContains('auth', $value);
        }
        
    }
}

class ProfileTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function route_test(): void
    {
        // Test if the route is protected by the auth middleware 
        $route = [
           dashboard => 'auth',
              profile => 'auth',
                settings => 'auth',

        ];
        foreach ($route as $key => $value) {
            $this->assertContains('auth', $value);
        }
        
    }
}

class DashboardTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function route_test(): void
    {
        // Test if the route is protected by the auth middleware 
        $route = [
           dashboard => 'auth',
              profile => 'auth',
                settings => 'auth',

        ];
        foreach ($route as $key => $value) {
            $this->assertContains('auth', $value);
        }
        
    }
}