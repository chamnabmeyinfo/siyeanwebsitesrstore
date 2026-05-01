<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The legacy storefront serves `/` without requiring staff login.
     */
    public function test_the_home_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    /**
     * Staff POS routes redirect anonymous users to the legacy login screen.
     */
    public function test_staff_dashboard_redirects_guests_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
