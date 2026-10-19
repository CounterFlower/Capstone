<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_form_actions_use_https_behind_a_tls_terminating_proxy(): void
    {
        $response = $this->withServerVariables([
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])->get('/admin/login');

        $response->assertOk();
        $response->assertSee('action="https://localhost:8000/admin/login"', false);
    }
}
