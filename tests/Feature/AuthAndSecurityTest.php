<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;

class AuthAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    #[Test]
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'finance.officer@slt.lk',
            'password' => Hash::make('Password@123'),
        ]);
        $user->assignRole('Finance');

        $response = $this->postJson('/api/login', [
            'email' => 'finance.officer@slt.lk',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'email', 'name'],
                'token'
            ]);

        $this->assertAuthenticated();
    }

    #[Test]
    public function invalid_credentials_returns_401_and_increments_failed_attempts()
    {
        $user = User::factory()->create([
            'email' => 'target.user@slt.lk',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'target.user@slt.lk',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonFragment(['message' => 'Invalid credentials']);

        $this->assertEquals(1, $user->fresh()->login_attempts);
    }

    #[Test]
    public function repeated_failed_logins_triggers_account_lockout()
    {
        $user = User::factory()->create([
            'email' => 'locked.user@slt.lk',
            'password' => Hash::make('SecretPass123!'),
        ]);

        // Fail 5 consecutive times
        for ($i = 1; $i <= 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'locked.user@slt.lk',
                'password' => 'IncorrectAttempt',
            ]);
        }

        $user->refresh();
        $this->assertEquals(5, $user->login_attempts);
        $this->assertNotNull($user->lockout_until);

        // 6th attempt should be blocked with 423 (Locked)
        $lockoutResponse = $this->postJson('/api/login', [
            'email' => 'locked.user@slt.lk',
            'password' => 'SecretPass123!',
        ]);

        $lockoutResponse->assertStatus(423);
    }

    #[Test]
    public function full_otp_password_reset_flow()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'reset.user@slt.lk',
            'password' => Hash::make('OldPassword@123'),
        ]);

        // 1. Request OTP
        $forgotResponse = $this->postJson('/api/forgot-password-otp', [
            'email' => 'reset.user@slt.lk',
        ]);

        $forgotResponse->assertStatus(200);

        // Retrieve OTP stored in Cache
        $cachedOtp = Cache::get('password_reset_otp:reset.user@slt.lk');
        $this->assertNotNull($cachedOtp);
        $this->assertEquals(6, strlen((string)$cachedOtp));

        // 2. Verify OTP
        $verifyResponse = $this->postJson('/api/verify-otp', [
            'email' => 'reset.user@slt.lk',
            'otp' => (string)$cachedOtp,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJsonFragment(['valid' => true]);

        // 3. Reset Password with OTP
        $newPassword = 'NewSecretPassword@999';
        $resetResponse = $this->postJson('/api/reset-password-otp', [
            'email' => 'reset.user@slt.lk',
            'otp' => (string)$cachedOtp,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $resetResponse->assertStatus(200);

        // 4. Verify login with the new password
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'reset.user@slt.lk',
            'password' => $newPassword,
        ]);

        $loginResponse->assertStatus(200);
    }

    #[Test]
    public function unauthenticated_requests_to_protected_endpoints_are_denied()
    {
        $this->getJson('/api/user')->assertStatus(401);
        $this->getJson('/api/invoices')->assertStatus(401);
        $this->getJson('/api/contractor-bills')->assertStatus(401);
        $this->getJson('/api/purchase-orders')->assertStatus(401);
    }

    #[Test]
    public function logout_revokes_current_access_tokens()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertCount(0, $user->fresh()->tokens);
    }
}
