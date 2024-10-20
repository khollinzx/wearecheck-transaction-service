<?php

namespace App\Services;

use App\DTOs\AuthDto\LoginDto;
use App\DTOs\AuthDto\SignUpDto;
use App\Models\OauthAccessToken;
use App\Models\User;
use App\Utils\Constants;
use App\Utils\GenericServiceResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{

    /**
     * To handle registration activities
     * @param SignUpDto $signUpDto
     * @return GenericServiceResponse
     */
    public function handleUserRegistration(SignUpDto $signUpDto): GenericServiceResponse
    {
        $response = new GenericServiceResponse();
        try {
            return DB::transaction(function () use ($signUpDto, $response) {
                /** @var User $user */
                $user = User::repo()->createModel([
                    'first_name' => $signUpDto->firstname,
                    'last_name' => $signUpDto->lastname,
                    'email' => $signUpDto->email,
                    'password' => Hash::make($signUpDto->password),
                ]);
                $response->status = true;
                $response->message = 'Congratulations, Your account has been created, Kindly login.';
                $response->data = $user;
                return $response;
            }, Constants::ATTEMPT);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $response;
        }
    }

    /**
     * To handle login activities
     * @param LoginDto $loginDto
     * @return GenericServiceResponse
     */
    public function handleUserLogin(LoginDto $loginDto): GenericServiceResponse
    {
        $response = new GenericServiceResponse();
        try {
            return DB::transaction(function () use ($loginDto, $response) {
                /** @var User $user */
                $user = User::repo()->findSingleByWhereClause(['email' => $loginDto->email]);
                if(! $user) {
                    $response->message = 'No account exist with such email.';
                    return $response;
                }
                if(! Auth::guard('user')->attempt(['email'=> $user->getEmail(), 'password'=> $loginDto->password])) {
                    $response->message = 'Invalid login credentials.';
                    return $response;
                }
                $response->status = true;
                Log::error(json_encode($response));
                $response->data = OauthAccessToken::createAccessToken($user);
                $response->message = 'Login successful.';
                return $response;
            }, Constants::ATTEMPT);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $response;
        }
    }
}
