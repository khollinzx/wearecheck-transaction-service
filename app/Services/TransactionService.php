<?php

namespace App\Services;

use App\DTOs\TransactionDto\TransactionDto;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\UserWalletTransaction;
use App\Utils\Constants;
use App\Utils\GenericServiceResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class TransactionService
{

    /**
     * To handle debit activities
     * @param TransactionDto $transactionDto
     * @param Authenticatable|User $user
     * @return GenericServiceResponse
     */
    public function handleUserMakePayment(TransactionDto $transactionDto, Authenticatable|User $user): GenericServiceResponse
    {
        $response = new GenericServiceResponse();
        try {
            return DB::transaction(function () use ($transactionDto, $user, $response) {
                /** @var UserWallet $user_wallet */
                $user_wallet = UserWallet::repo()->findSingleByWhereClause(['user_id' => $user->getId()]);
                $new_balance = ($user_wallet->getBalance() - $transactionDto->amount);
                $transaction = Transaction::repo()->createModel([
                    'reference_no' => Uuid::uuid4(),
                    'user_id' => $user->getId(),
                    'amount' => $transactionDto->amount,
                    'type' => "DEBIT",
                    'status' => Constants::SUCCESSFUL,
                ]);
                UserWalletTransaction::repo()->createModel([
                    'transaction_id' => $transaction->getId(),
                    'user_id' => $user->getId(),
                    'previous_balance' => $user_wallet->getBalance(),
                    'new_balance' => $new_balance
                ]);
                $user_wallet->update(['balance' => $new_balance]);
                $response->status = true;
                $response->message = "Congratulations! Your payment was successful, =N={$transactionDto->amount} has been debited from your account.";
                return $response;
            }, Constants::ATTEMPT);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $response;
        }
    }

    /**
     * To handle credit activities of an authenticated user's wallet
     * @param TransactionDto $transactionDto
     * @param Authenticatable|User $user
     * @return GenericServiceResponse
     */
    public function handleUserFundWallet(TransactionDto $transactionDto, Authenticatable|User $user): GenericServiceResponse
    {
        $response = new GenericServiceResponse();
        try {
            return DB::transaction(function () use ($transactionDto, $user, $response) {
                /** @var UserWallet $user_wallet */
                $user_wallet = UserWallet::repo()->findSingleByWhereClause(['user_id' => $user->getId()]);
                $new_balance = ($user_wallet->getBalance() + $transactionDto->amount);
                $transaction = Transaction::repo()->createModel([
                    'reference_no' => Uuid::uuid4(),
                    'user_id' => $user->getId(),
                    'type' => "CREDIT",
                    'amount' => $transactionDto->amount,
                    'status' => Constants::SUCCESSFUL,
                ]);
                UserWalletTransaction::repo()->createModel([
                    'transaction_id' => $transaction->getId(),
                    'previous_balance' => $user_wallet->getBalance(),
                    'new_balance' => $new_balance
                ]);
                $user_wallet->update(['balance' => $new_balance]);
                $response->status = true;
                $response->message = "Congratulations! You have successfully fund your wallet with =N={$transactionDto->amount}.";
                return $response;
            }, Constants::ATTEMPT);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $response;
        }
    }
}
