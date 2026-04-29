<?php

namespace Tests\Feature;

use App\Models\DeletedUsername;
use App\Models\Recipient;
use App\Models\User;
use App\Models\Username;
use App\Notifications\Account\CustomVerifyEmail;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[Test]
    public function user_can_register_successfully()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'username' => 'mrunknown',
            'email' => 'mrunknown@example.com',
            'email_confirmation' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response
            ->assertRedirect('/')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('usernames', [
            'username' => 'mrunknown',
        ]);

        $user = Username::where('username', 'mrunknown')->first()->user;

        Notification::assertSentTo(
            $user,
            CustomVerifyEmail::class
        );
    }

    #[Test]
    public function user_cannot_register_with_invalid_characters()
    {
        $response = $this->post('/register', [
            'username' => 'Ω',
            'email' => 'mrunknown@example.com',
            'email_confirmation' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['username']);

        $this->assertDatabaseMissing('users', [
            'username' => 'Ω',
        ]);
    }

    #[Test]
    public function user_can_verify_email_successfully()
    {
        $user = User::factory()->create()->fresh();
        $user->email_verified_at = null;
        $user->save();

        $this->assertNull($user->refresh()->email_verified_at);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => base64_encode(Hash::make($user->getEmailForVerification())),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response
            ->assertRedirect('/')
            ->assertSessionHas('verified');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    #[Test]
    public function user_must_use_valid_username()
    {
        $response = $this->post('/register', [
            'username' => 'john_doe',
            'email' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['username']);

        $this->assertDatabaseMissing('users', [
            'username' => 'john_doe',
        ]);
    }

    #[Test]
    public function user_must_confirm_email()
    {
        $response = $this->post('/register', [
            'username' => 'mrunknown',
            'email' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', [
            'username' => 'mrunknown',
        ]);
    }

    #[Test]
    public function user_cannot_register_with_existing_email()
    {
        $user = User::factory()->create()->fresh();

        Recipient::factory()->create([
            'user_id' => $user->id,
            'email' => 'mrunknown@example.com',
        ]);

        Username::factory()->create([
            'user_id' => $user->id,
            'username' => 'mrunknown',
        ]);

        $response = $this->post('/register', [
            'username' => 'janedoe',
            'email' => 'mrunknown@example.com',
            'email_confirmation' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function user_cannot_register_with_existing_username()
    {
        $user = User::factory()->create()->fresh();

        Username::factory()->create([
            'user_id' => $user->id,
            'username' => 'mrunknown',
        ]);

        $response = $this->post('/register', [
            'username' => 'mrunknown',
            'email' => 'mrunknown@example.com',
            'email_confirmation' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    #[Test]
    public function user_cannot_register_with_blacklisted_username()
    {
        $response = $this->post('/register', [
            'username' => 'www',
            'email' => 'mrunknown@example.com',
            'email_confirmation' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['username']);

        $this->assertDatabaseMissing('users', [
            'username' => 'www',
        ]);
    }

    #[Test]
    public function user_cannot_register_with_uppercase_blacklisted_username()
    {
        $response = $this->post('/register', [
            'username' => 'Www',
            'email' => 'mrunknown@example.com',
            'email_confirmation' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['username']);

        $this->assertDatabaseMissing('users', [
            'username' => 'www',
        ]);
    }

    #[Test]
    public function user_cannot_register_with_deleted_username()
    {
        DeletedUsername::create(['username' => 'mrunknown']);

        $response = $this->post('/register', [
            'username' => 'mrunknown',
            'email' => 'mrunknown@example.com',
            'email_confirmation' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['username']);

        $this->assertDatabaseMissing('users', [
            'username' => 'mrunknown',
        ]);
    }

    #[Test]
    public function user_cannot_register_with_uppercase_deleted_username()
    {
        DeletedUsername::create(['username' => 'mrunknown']);

        $response = $this->post('/register', [
            'username' => 'mRunknown',
            'email' => 'mrunknown@example.com',
            'email_confirmation' => 'mrunknown@example.com',
            'password' => 'mypassword',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['username']);

        $this->assertDatabaseMissing('users', [
            'username' => 'mrunknown',
        ]);
    }
}
