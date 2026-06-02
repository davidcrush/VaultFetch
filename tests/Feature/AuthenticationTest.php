<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_home(): void
    {
        $response = $this->get(route('home'));

        $response->assertRedirect(route('login'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'correct-password',
        ]);

        $response = $this->from(route('login'))
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_login_and_access_home(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'secret',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $this->get(route('home'))->assertOk();
    }

    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $this->authenticate();

        $response = $this->get(route('login'));

        $response->assertRedirect(route('home'));
    }

    public function test_user_can_logout(): void
    {
        $user = $this->authenticate();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();

        $this->get(route('home'))->assertRedirect(route('login'));
    }
}
