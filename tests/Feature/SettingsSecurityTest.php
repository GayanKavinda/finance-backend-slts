<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SettingsSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $procurement;
    protected $finance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        $this->procurement = User::factory()->create();
        $this->procurement->assignRole('Procurement');

        $this->finance = User::factory()->create();
        $this->finance->assignRole('Finance');
    }

    /* ── Notification preferences ────────────────────────────── */

    #[Test]
    public function notification_preferences_require_authentication(): void
    {
        $this->getJson('/api/notification-preferences')->assertStatus(401);
    }

    #[Test]
    public function notification_preferences_return_defaults_and_persist(): void
    {
        $res = $this->actingAs($this->procurement, 'sanctum')
            ->getJson('/api/notification-preferences')
            ->assertOk()
            ->assertJson([
                'email_alerts' => true,
                'browser_alerts' => true,
                'critical_alerts' => true,
            ]);

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $this->procurement->id,
        ]);

        $this->actingAs($this->procurement, 'sanctum')
            ->putJson('/api/notification-preferences', ['email_alerts' => false])
            ->assertOk()
            ->assertJsonPath('preferences.email_alerts', false);

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $this->procurement->id,
            'email_alerts' => false,
        ]);
    }

    #[Test]
    public function critical_alerts_cannot_be_disabled(): void
    {
        $this->actingAs($this->procurement, 'sanctum')
            ->putJson('/api/notification-preferences', ['critical_alerts' => false])
            ->assertOk()
            ->assertJsonPath('preferences.critical_alerts', true);

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $this->procurement->id,
            'critical_alerts' => true,
        ]);
    }

    #[Test]
    public function user_payload_exposes_two_factor_flag(): void
    {
        $this->actingAs($this->procurement, 'sanctum')
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('two_factor_enabled', false);
    }

    #[Test]
    public function unread_count_endpoint_reports_the_badge_number(): void
    {
        $this->actingAs($this->procurement, 'sanctum')
            ->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->procurement->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => \App\Notifications\ContractorActivityNotification::class,
            'data' => ['action' => 'approved', 'message' => 'Badge test notification'],
        ]);

        $this->actingAs($this->procurement, 'sanctum')
            ->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 1);

        // Other users are unaffected (per-user badge)
        $this->actingAs($this->finance, 'sanctum')
            ->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 0);
    }

    /* ── Two-factor authentication ───────────────────────────── */

    #[Test]
    public function enabling_2fa_requires_correct_password(): void
    {
        $this->actingAs($this->procurement, 'sanctum')
            ->postJson('/api/2fa/enable', ['password' => 'totally-wrong'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertFalse($this->procurement->fresh()->two_factor_enabled);

        $this->actingAs($this->procurement, 'sanctum')
            ->postJson('/api/2fa/enable', ['password' => 'password'])
            ->assertOk()
            ->assertJsonPath('two_factor_enabled', true);

        $this->assertTrue($this->procurement->fresh()->two_factor_enabled);
    }

    #[Test]
    public function login_with_2fa_enabled_challenges_before_granting_a_token(): void
    {
        $this->procurement->forceFill(['two_factor_enabled' => true])->save();
        Log::spy();

        $response = $this->postJson('/api/login', [
            'email' => $this->procurement->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('two_factor_required', true)
            ->assertJsonPath('email_hint', self::mask($this->procurement->email));

        // No session/token is granted before the code is verified.
        $this->assertGuest();
        $this->assertNull($response->json('token'));

        $token = $response->json('twofa_token');
        $this->assertNotEmpty($token);

        $cached = Cache::get('twofa:' . $token);
        $this->assertSame($this->procurement->id, $cached['user_id']);
        $this->assertNotEmpty($cached['otp']);
    }

    #[Test]
    public function wrong_2fa_code_is_rejected_and_counts_attempts(): void
    {
        $this->procurement->forceFill(['two_factor_enabled' => true])->save();

        $token = $this->postJson('/api/login', [
            'email' => $this->procurement->email,
            'password' => 'password',
        ])->json('twofa_token');

        $this->postJson('/api/verify-2fa', [
            'twofa_token' => $token,
            'otp' => '000000',
        ])->assertStatus(422)->assertJsonPath('message', 'Invalid verification code.');

        $payload = Cache::get('twofa:' . $token);
        $this->assertSame(1, $payload['attempts']);
    }

    #[Test]
    public function correct_2fa_code_completes_login(): void
    {
        $this->procurement->forceFill(['two_factor_enabled' => true])->save();

        $token = $this->postJson('/api/login', [
            'email' => $this->procurement->email,
            'password' => 'password',
        ])->json('twofa_token');

        $otp = Cache::get('twofa:' . $token)['otp'];

        $response = $this->postJson('/api/verify-2fa', [
            'twofa_token' => $token,
            'otp' => (string) $otp,
        ]);

        $response->assertOk()
            ->assertJsonPath('user.id', $this->procurement->id)
            ->assertJsonPath('message', 'Login successful');

        $this->assertNotEmpty($response->json('token'));
        $this->assertAuthenticatedAs($this->procurement, 'sanctum');

        // Challenge is single-use
        $this->postJson('/api/verify-2fa', [
            'twofa_token' => $token,
            'otp' => (string) $otp,
        ])->assertStatus(422);
    }

    #[Test]
    public function resend_2fa_replaces_the_code(): void
    {
        $this->procurement->forceFill(['two_factor_enabled' => true])->save();

        $token = $this->postJson('/api/login', [
            'email' => $this->procurement->email,
            'password' => 'password',
        ])->json('twofa_token');

        $this->postJson('/api/resend-2fa', ['twofa_token' => $token])
            ->assertOk()
            ->assertJsonPath('message', 'A new verification code has been sent to your email.');

        $payload = Cache::get('twofa:' . $token);
        $this->assertSame(0, $payload['attempts']);
        $this->assertNotEmpty($payload['otp']);
    }

    #[Test]
    public function disabling_2fa_requires_password(): void
    {
        $this->procurement->forceFill(['two_factor_enabled' => true])->save();

        $this->actingAs($this->procurement, 'sanctum')
            ->postJson('/api/2fa/disable', ['password' => 'nope'])
            ->assertStatus(422);

        $this->actingAs($this->procurement, 'sanctum')
            ->postJson('/api/2fa/disable', ['password' => 'password'])
            ->assertOk()
            ->assertJsonPath('two_factor_enabled', false);

        $this->assertFalse($this->procurement->fresh()->two_factor_enabled);

        // Normal login works again (restore the default guard after actingAs)
        $this->app['auth']->setDefaultDriver('web');
        $this->app['auth']->forgetGuards();
        $this->postJson('/api/login', [
            'email' => $this->procurement->email,
            'password' => 'password',
        ])->assertOk()->assertJsonPath('user.id', $this->procurement->id);
    }

    #[Test]
    public function users_without_2fa_login_normally(): void
    {
        $this->postJson('/api/login', [
            'email' => $this->finance->email,
            'password' => 'password',
        ])->assertOk()
            ->assertJsonPath('two_factor_required', null)
            ->assertJsonPath('user.id', $this->finance->id);
    }

    /* ── Maintenance mode ────────────────────────────────────── */

    #[Test]
    public function only_super_admins_can_toggle_maintenance_mode(): void
    {
        $this->actingAs($this->finance, 'sanctum')
            ->putJson('/api/system/maintenance-mode', ['maintenance_mode' => true])
            ->assertStatus(403);

        $this->assertFalse(SystemSetting::isMaintenanceMode());

        $this->actingAs($this->superAdmin, 'sanctum')
            ->putJson('/api/system/maintenance-mode', ['maintenance_mode' => true])
            ->assertOk()
            ->assertJsonPath('maintenance_mode', true);

        $this->assertTrue(SystemSetting::isMaintenanceMode());
    }

    #[Test]
    public function maintenance_mode_blocks_writes_but_allows_reads(): void
    {
        $this->actingAs($this->superAdmin, 'sanctum')
            ->putJson('/api/system/maintenance-mode', ['maintenance_mode' => true])
            ->assertOk();

        // Reads still work for everyone
        $this->actingAs($this->finance, 'sanctum')
            ->getJson('/api/notification-preferences')
            ->assertOk();

        // Writes are blocked for non-super-admins with a 503
        $this->actingAs($this->finance, 'sanctum')
            ->putJson('/api/notification-preferences', ['email_alerts' => false])
            ->assertStatus(503)
            ->assertJsonPath('maintenance_mode', true);

        // Preference was NOT changed
        $this->assertDatabaseMissing('notification_preferences', [
            'user_id' => $this->finance->id,
            'email_alerts' => false,
        ]);

        // Super admin writes are exempt
        $this->actingAs($this->superAdmin, 'sanctum')
            ->putJson('/api/notification-preferences', ['browser_alerts' => false])
            ->assertOk();

        // Turning maintenance off restores writes
        $this->actingAs($this->superAdmin, 'sanctum')
            ->putJson('/api/system/maintenance-mode', ['maintenance_mode' => false])
            ->assertOk();

        $this->actingAs($this->finance, 'sanctum')
            ->putJson('/api/notification-preferences', ['email_alerts' => false])
            ->assertOk();
    }

    #[Test]
    public function maintenance_status_is_readable_by_any_authenticated_user(): void
    {
        $this->actingAs($this->finance, 'sanctum')
            ->getJson('/api/system/maintenance-status')
            ->assertOk()
            ->assertJsonPath('maintenance_mode', false);
    }

    /* ── API auth hardening ──────────────────────────────────── */

    #[Test]
    public function unauthenticated_api_requests_return_401_json(): void
    {
        $this->getJson('/api/jobs')->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
        $this->postJson('/api/quotations', [])->assertStatus(401);
    }

    private static function mask(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email), 2, '');
        return substr($local, 0, 1) . str_repeat('*', max(strlen($local) - 1, 1)) . '@' . $domain;
    }
}
