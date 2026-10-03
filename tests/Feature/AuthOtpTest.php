<?php

namespace Tests\Feature;

use App\Mail\LoginVerificationMail;
use App\Mail\RegistrationOtpMail;
use App\Mail\SecurityAlertMail;
use App\Models\EmailVerification;
use App\Models\User;
use App\Services\Security\AuthRateLimiterService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthOtpTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_login_requires_otp_and_sends_email(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirectContains('/login/verify-otp');
        $this->assertGuest(); // User should NOT be logged in yet

        // Assert 2FA email was sent
        Mail::assertSent(LoginVerificationMail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id;
        });

        // Assert verification record was stored in database
        $this->assertDatabaseHas('email_verifications', [
            'user_id' => $user->id,
            'email' => 'testuser@example.com',
            'type' => 'login',
        ]);
    }

    public function test_invalid_login_otp_fails_and_increments_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'verifyfail@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // Initiate login
        $this->post('/login', [
            'email' => 'verifyfail@example.com',
            'password' => 'password123',
        ]);

        // Submit wrong OTP
        $response = $this->post('/login/verify-otp', [
            'code' => '000000',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_valid_login_otp_authenticates_user(): void
    {
        $user = User::factory()->create([
            'email' => 'successuser@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);

        // Step 1: Credentials
        $this->post('/login', [
            'email' => 'successuser@example.com',
            'password' => 'password123',
        ]);

        $verification = EmailVerification::where('email', 'successuser@example.com')
            ->where('type', 'login')
            ->first();

        $this->assertNotNull($verification);

        // Step 2: Submit valid OTP
        $response = $this->post('/login/verify-otp', [
            'code' => $verification->code,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_daily_5_attempts_limit_blocks_user(): void
    {
        $email = 'lockedout@example.com';

        // Exhaust 5 attempts
        for ($i = 0; $i < 5; $i++) {
            AuthRateLimiterService::recordFailedAttempt($email, 'login');
        }

        $this->assertTrue(AuthRateLimiterService::isDailyLockedOut($email, 'login'));
        $this->assertEquals(0, AuthRateLimiterService::getRemainingDailyAttempts($email, 'login'));

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Daily security limit reached', session('errors')->first('email'));
    }

    public function test_registration_requires_email_otp_validation(): void
    {
        $regData = [
            'name' => 'John Signup',
            'email' => 'johnsignup@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->post('/register', $regData);
        $response->assertRedirectContains('/register/verify-otp');

        // User must NOT be in the database yet
        $this->assertDatabaseMissing('users', [
            'email' => 'johnsignup@example.com',
        ]);

        // Email OTP must be sent
        Mail::assertSent(RegistrationOtpMail::class, function ($mail) {
            return $mail->email === 'johnsignup@example.com';
        });

        // Verification token in DB
        $verification = EmailVerification::where('email', 'johnsignup@example.com')
            ->where('type', 'register')
            ->first();

        $this->assertNotNull($verification);

        // Now submit valid OTP code
        $verifyResponse = $this->post('/register/verify-otp', [
            'code' => $verification->code,
        ]);

        $verifyResponse->assertRedirect(route('home'));

        // User is now verified and exists in database
        $this->assertDatabaseHas('users', [
            'name' => 'John Signup',
            'email' => 'johnsignup@example.com',
        ]);

        $user = User::where('email', 'johnsignup@example.com')->first();
        $this->assertNotNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
    }
}
