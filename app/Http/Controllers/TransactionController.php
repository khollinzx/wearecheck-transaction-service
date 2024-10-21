<?php

namespace App\Http\Controllers;

use App\DTOs\TransactionDto\TransactionDto;
use App\Http\Requests\TransactionRequest;
use App\Services\TransactionService;
use App\Services\UserWalletService;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{

    /**
     * set constructor
     */
    public function __construct(protected TransactionService $service, protected UserWalletService $userWalletService)
    {}

    /**
     * For a user to make payment from is wallet
     * @param TransactionRequest $request
     * @return JsonResponse
     */
    public function performTransactions(TransactionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $response = $this->service->handleTransactions(TransactionDto::TransactionDto($validated['amount'], $validated['type']), $this->getUser());
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::$SUCCESS);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::$BAD_REQUEST);
        }
    }

    /**
     * To get user wallet balance
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalance(Request $request): JsonResponse
    {
        try {
            $response = $this->userWalletService->retrieveUserWalletBalance($this->getUser());
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::$SUCCESS);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::$BAD_REQUEST);
        }
    }
}
