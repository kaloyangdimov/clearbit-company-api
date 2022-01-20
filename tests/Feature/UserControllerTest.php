<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private $signInRoute = '/api/v1/sign-in';
    private $loginRoute = '/api/v1/log-in';
    private $changePassRoute = '/api/v1/change-password';
    private $forgotPassRoute = '/api/v1/forgotten';
    private $forgotPassResetRoute = '/api/v1/forgotten/';

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_sign_in_success()
    {
        $response = $this->postJson($this->signInRoute, ['email' => 'test@test', 'password' => 'password']);
        $user = User::where('email', 'test@test')->first();

        $response->assertStatus(201)->assertJson(
             [
                 'data' => [
                    'email'          => $user->email,
                    'token'          => $user->token,
                    'token_valid_to' => $user->token_valid_to
                ]
             ]
        );
    }

    public function test_sign_in_failure_missing_password()
    {
        $response = $this->postJson($this->signInRoute, ['email' => 'test@test']);

        $response->assertStatus(422)->assertJson(
            [
                'message' => 'Validation error',
                'details' => [
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ]
        );
    }

    public function test_sign_in_failure_missing_email()
    {
        $response = $this->postJson($this->signInRoute, ['password' => '1234565123']);

        $response->assertStatus(422)->assertJson(
            [
                'message' => 'Validation error',
                'details' => [
                    'email' => [
                        'The email field is required.'
                    ]
                ]
            ]
        );
    }

    public function test_login_success()
    {
        $user = User::factory()->create();

        $response = $this->postJson($this->loginRoute, ['password' => 'password', 'email' => $user->email]);

        $response->assertStatus(200);
    }

    public function test_login_wrong_email()
    {
        $user = User::factory()->create();
        $response = $this->postJson($this->loginRoute, ['password' => 'password', 'email' => 'test@test.bg']);

        $response->assertStatus(401);
    }

    public function test_login_wrong_password()
    {
        $user = User::factory()->create();
        $response = $this->postJson($this->loginRoute, ['password' => 'keinPass', 'email' => $user->email]);

        $response->assertStatus(401);
    }

    public function test_change_password_success()
    {
        $user = User::factory()->create();
        $response = $this->withToken($user->token)->postJson($this->changePassRoute, ['password' => 'password2', 'password_confirm' => 'password2']);

        $response->assertStatus(200)->assertJson([
            "data" => [
                'token'          => $user->token,
                'token_valid_to' => $user->token_valid_to->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function test_change_password_failure_with_expired_token()
    {
        $user = User::factory()->create(['token_valid_to' => '2021-01-01 10:10:10']);
        $response = $this->withToken($user->token)->postJson($this->changePassRoute, ['password' => 'password2', 'password_confirm' => 'password2']);

        $response->assertStatus(401);
    }

    public function test_send_forgotten_password_link_success()
    {
        $user = User::factory()->create();
        $response = $this->postJson($this->forgotPassRoute, ['email' => $user->email]);

        $response->assertStatus(200)->assertJson(['message' => 'Reset link sent']);
    }

    public function test_send_forgotten_password_link_failure_wrong_email()
    {
        $user = User::factory()->create();
        $response = $this->postJson($this->forgotPassRoute, ['email' => 'wrong@email']);

        $response->assertStatus(422);
    }

    public function test_reset_password_success()
    {
        $user = User::factory()->create();
        $dummyToken = '123123asdawd123';

        DB::table('password_resets')->insert(['token' => Hash::make($dummyToken), 'email' => $user->email, 'created_at' => now()->format('Y-m-d H:i:s')]);

        $response = $this->postJson($this->forgotPassResetRoute.$dummyToken.'?email='.$user->email, ['password' => '123123123', 'password_confirm' => '123123123']);

        $response->assertStatus(200);
    }

    public function test_reset_password_failure_wrong_token()
    {
        $user = User::factory()->create();
        $dummyToken = '123123asdawd123';

        DB::table('password_resets')->insert(['token' => Hash::make($dummyToken), 'email' => $user->email, 'created_at' => now()->format('Y-m-d H:i:s')]);

        $response = $this->postJson($this->forgotPassResetRoute.$dummyToken.'11234?email='.$user->email, ['password' => '123123123', 'password_confirm' => '123123123']);

        $response->assertStatus(500);
    }
}
