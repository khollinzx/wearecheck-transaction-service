<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\AuthDto\LoginDto;
use App\DTOs\AuthDto\SignUpDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\OnBoardRequest;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserOnboardController extends Controller
{

    /**
     * set constructor
     */
    public function __construct(
        protected UserRepository $usersRepository, protected UserService $service,
    )
    {}

    /** signup users
     * @param OnBoardRequest $request
     * @return JsonResponse
     */
    public function register(OnBoardRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $response = $this->service->handleUserRegistration(SignUpDto::signUpDto($validated['first_name'], $validated['last_name'], $validated['email'], $validated['password']));
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::$CREATED);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::$BAD_REQUEST);
        }
    }

    /** login users
     * @param OnBoardRequest $request
     * @return JsonResponse
     */
    public function login(OnBoardRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $response = $this->service->handleUserLogin(LoginDto::loginDto($validated['email'], $validated['password']));
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse('Login succeeded', $response->data, JsonResponseAPI::$SUCCESS);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::$BAD_REQUEST);
        }
    }
}
