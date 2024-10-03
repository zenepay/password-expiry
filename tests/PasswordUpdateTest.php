<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    // use RefreshDatabase;
    private $profilePage;
    private $submitPasswordPage;
    public function setUp(): void
    {
        parent::setUp();
        $this->profilePage = '/profile';
        $this->submitPasswordPage = '/password';
    }
    public function test_password_must_be_complex(): void
    {
        User::find(300)?->delete();
        /** @var \App\Models\User $user */
        $user = User::factory()->create(
            [
                'id' => 300,
            ]
        );

        $response = $this
            ->actingAs($user)
            ->from($this->profilePage)
            ->put($this->submitPasswordPage, [
                'current_password' => 'password',
                'password' => 'password2',
                'password_confirmation' => 'password2',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'password')
            ->assertRedirect('/profile');
        // $user->delete();
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        // $user = User::factory()->create();
        $user = User::find(300);
        $response = $this
            ->actingAs($user)
            ->from($this->profilePage)
            ->put($this->submitPasswordPage, [
                'current_password' => 'wrong-password',
                'password' => 'new-Password123',
                'password_confirmation' => 'new-Password123',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect($this->profilePage);
        //$user->delete();
    }

    public function test_password_can_be_updated(): void
    {
        // $user = User::factory()->create();
        $user = User::find(300);
        $response = $this
            ->actingAs($user)
            ->from($this->profilePage)
            ->put($this->submitPasswordPage, [
                'current_password' => 'password',
                'password' => 'new-Password123',
                'password_confirmation' => 'new-Password123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect($this->profilePage);

        $this->assertTrue(Hash::check('new-Password123', $user->refresh()->password));
        //$user->delete();
    }

    public function test_create_previous_password_at_create(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password2'),
        ]);

        $this->assertDatabaseHas('previous_password_hashes', [
            'user_id' => $user->id,
            'password_hash' => $user->password,
        ]);

        $user->delete();
    }

    public function test_cannot_chanage_to_same_password(): void
    {
        $user = User::find(300);
        $chageDate = $user->passwordHistories->last()->created_at;
        $response = $this->actingAs($user)
            ->from($this->profilePage)
            ->put($this->submitPasswordPage, [
                'current_password' => 'new-Password123',
                'password' => 'new-Password123',
                'password_confirmation' => 'new-Password123',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'password');
        $this->assertTrue(Hash::check('new-Password123', $user->refresh()->password));
        $this->assertEquals($chageDate, $user->passwordHistories->last()->created_at);
    }

    public function test_cannot_chanage_to_previous_passwords(): void
    {
        $user = User::find(300);
        $this->actingAs($user)
            ->from($this->profilePage)
            ->put($this->submitPasswordPage, [
                'current_password' => 'new-Password123',
                'password' => 'new-Password124',
                'password_confirmation' => 'new-Password124',
            ]);

        $response = $this->actingAs($user)
            ->from($this->profilePage)
            ->put($this->submitPasswordPage, [
                'current_password' => 'new-Password124',
                'password' => 'new-Password123',
                'password_confirmation' => 'new-Password123',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'password');
        $this->assertTrue(Hash::check('new-Password124', $user->refresh()->password));
        $user->delete();
    }
}
