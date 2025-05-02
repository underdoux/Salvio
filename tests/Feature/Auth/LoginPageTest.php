<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_login_page_has_required_elements(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertViewIs('auth.login');
        $response->assertSeeInOrder([
            'Welcome back',
            'Sign in to your account',
            'Email address',
            'Password',
            'Remember me',
            'Forgot password?',
            'Sign in',
            'Create one'
        ]);
    }

    public function test_login_page_has_proper_form_attributes(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('method="POST"', false);
        $response->assertSee('action="' . route('login') . '"', false);
        $response->assertSee('type="email"', false);
        $response->assertSee('type="password"', false);
        $response->assertSee('type="checkbox"', false);
        $response->assertSee('type="submit"', false);
    }

    public function test_login_page_has_dark_mode_classes(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('dark:bg-');
        $response->assertSee('dark:text-');
        $response->assertSee('dark:border-');
    }

    public function test_login_page_has_responsive_design_classes(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('min-h-screen');
        $response->assertSee('w-full');
        $response->assertSee('max-w-md');
        $response->assertSee('p-8');
    }

    public function test_login_page_has_proper_input_styling(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('rounded-xl');
        $response->assertSee('border');
        $response->assertSee('focus:ring-2');
        $response->assertSee('transition-all');
    }

    public function test_login_page_has_proper_links(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee(route('password.request'));
        $response->assertSee(route('register'));
    }
}
