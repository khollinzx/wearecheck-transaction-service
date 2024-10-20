<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function set_up(): void
    {
        // Create a test user record
        User::factory()->create([
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => 'john.doe@example.com',
            'password' => Hash::make('1234567'),  // Hash the password for comparison during login
        ]);
    }

    /**
     * A basic test example.
     */
    public function test_to_register_new_user(): void
    {
        // Send a POST request to the API endpoint
        $payload = [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => 'john.doe@example.com',
            'password' => '1234567',
            'confirmed_password' => '1234567',
        ];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/v1/onboard/register', $payload);

        // Assert the response is successful
        $response->assertStatus(201)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "id",
                    "first_name",
                    "last_name",
                    "email",
                ]
            ]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'first_name' => 'John'
        ]);
    }

    /**
     * A basic test example.
     */
    public function test_to_check_for_password_mismatch_on_registration(): void
    {
        // Send a POST request to the API endpoint
        $payload = [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => 'john.doe@example.com',
            'password' => '1234567',
            'confirmed_password' => '123567',
        ];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/v1/onboard/register', $payload);
        // Assert the response is successful

        $response->assertStatus(422)
            ->assertJson([
                'header' => "Form Error",
                'status' => false,
                'message' => "The confirmed password field must match password."
            ]);
    }

    /**
     * A basic test example.
     */
    public function test_to_check_for_existing_user_with_same_email_on_registration(): void
    {
        // Create a test user record
        $this->set_up();

        // Send a POST request to the API endpoint
        $payload = [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => 'john.doe@example.com',
            'password' => '1234567',
            'confirmed_password' => '123567',
        ];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/v1/onboard/register', $payload);
        // Assert the response is successful

        $response->assertStatus(422)
            ->assertJson([
                'header' => "Form Error",
                'status' => false,
                'message' => "The email has already been taken."
            ]);
    }

    /**
     * A basic test example.
     */
    public function test_to_login_a_user(): void
    {
        // Create a test user record
        $this->set_up();
        // Send a POST request to the API endpoint
        $payload = ['email' => 'john.doe@example.com', 'password' => '1234567',];
        $response = $this->withHeaders(['Accept' => 'application/json'])->postJson('/api/v1/onboard/login', $payload);
        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => "Login succeeded",
                'data' => []
            ]);
    }

    /**
     * A basic test example.
     */
    public function test_to_login_a_user_for_invalid_credentials(): void
    {
        // Create a test user record
        $this->set_up();
        // Send a POST request to the API endpoint
        $payload = ['email' => 'john.doe@example.com', 'password' => '123456',];
        $response = $this->withHeaders(['Accept' => 'application/json'])->postJson('/api/v1/onboard/login', $payload);
        // Assert the response is successful
        $response->assertStatus(400)
            ->assertJson([
                'header' => 'Error',
                'status' => false,
                'message' => "Invalid login credentials.",
            ]);
    }

    /**
     * A basic test example.
     */
    public function test_to_get_user_wallet_balance(): void
    {
        // Create a test user record
        $this->set_up();
        $user = User::first();
        $user_wallet = UserWallet::where('user_id', $user->id)->first();
        // Send a GET request to the API endpoint
        $response = $this->actingAs($user, 'api')->withHeaders(['Accept' => 'application/json'])->getJson('/api/v1/users/wallet-balance');
        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => "Wallet Balance Retrieved.",
                "data" => [
                    "balance" => 0
                ]
            ])->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "balance"
                ]
            ]);;
        $this->assertSame($user_wallet->balance, 0.0);
    }

    /**
     * A basic test example.
     */
    public function test_to_get_user_wallet_balance_when_user_does_not_have_a_wallet_created(): void
    {
        // Create a test user record
        $this->set_up();
        $user = User::first();
        $user_wallet = UserWallet::where('user_id', $user->id)->first()->update(['user_id' => null]);
        $user_wallet = UserWallet::where('user_id', $user->id)->first();
        // Send a GET request to the API endpoint
        $response = $this->actingAs($user, 'api')->withHeaders(['Accept' => 'application/json'])->getJson('/api/v1/users/wallet-balance');
        Log::error(json_encode($user));
        Log::error(json_encode($user_wallet));
        // Assert the response is successful
        $response->assertStatus(400)
            ->assertJson([
                'header' => 'Error',
                'status' => false,
                'message' => "Sorry!, we could not retrieve data, kindly try again later.",
            ]);
    }

    /**
     * A basic test example.
     */
    public function test_to_credit_user_wallet_balance(): void
    {
        // Create a test user record
        $this->set_up();
        $amount = 20000;
        $user = User::first();
        // Send a GET request to the API endpoint
        $response = $this->actingAs($user, 'api')->withHeaders(['Accept' => 'application/json'])->postJson('/api/v1/users/fund-wallet', ["amount" => $amount]);
        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => "Congratulations! You have successfully fund your wallet with =N={$amount}.",
                "data" => []
            ])->assertJsonStructure([
                "status",
                "message",
                "data"
            ]);
        $user_wallet = UserWallet::where('user_id', $user->id)->first();
        $this->assertSame($user_wallet->balance, 20000.0);
    }

    /**
     * A basic test example.
     */
    public function test_to_make_payment_from_user_wallet_balance(): void
    {
        // Create a test user record
        $this->set_up();
        $amount = 2000;
        $wallet_balance = 20000;
        $user = User::first();
        $user_wallet = UserWallet::where('user_id', $user->id)->first()->update(['balance' => $wallet_balance]);
        // Send a GET request to the API endpoint
        $response = $this->actingAs($user, 'api')->withHeaders(['Accept' => 'application/json'])->postJson('/api/v1/users/make-payment', ["amount" => $amount]);
        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => "Congratulations! Your payment was successful, =N={$amount} has been debited from your account.",
                "data" => []
            ])->assertJsonStructure([
                "status",
                "message",
                "data"
            ]);
        $user_wallet = UserWallet::where('user_id', $user->id)->first();
        $this->assertSame($user_wallet->balance, (float)($wallet_balance - $amount));
    }

    /**
     * A basic test example.
     */
    public function test_to_check_for_insufficient_user_wallet_balance(): void
    {
        // Create a test user record
        $this->set_up();
        $amount = 2000;
        $user = User::first();
        // Send a GET request to the API endpoint
        $response = $this->actingAs($user, 'api')->withHeaders(['Accept' => 'application/json'])->postJson('/api/v1/users/make-payment', ["amount" => $amount]);
        // Assert the response is successful
        $response->assertStatus(422)
            ->assertJson([
                "status" => false,
                "message" => "Sorry, your wallet balance is not sufficient for this transaction.",
                "header" => "Form Error"
            ]);
    }
}
