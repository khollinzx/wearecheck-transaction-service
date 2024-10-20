<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWallet;
use App\Utils\GenericServiceResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class UserWalletService
{
    /**
     * To handle retrieval of user wallet balance.
     * @param Authenticatable|User $user
     * @return GenericServiceResponse
     */
    public function retrieveUserWalletBalance(Authenticatable|User $user): GenericServiceResponse
    {
        $response = new GenericServiceResponse();
        try {
            /** @var UserWallet $user_wallet */
            $user_wallet = UserWallet::repo()->findSingleByWhereClause(['user_id' => $user->getId()]);
            if(! $user_wallet) {
                $response->message = 'Sorry!, we could not retrieve data, kindly try again later.';
                return $response;
            }
            $response->status = true;
            $response->data = $user_wallet;
            $response->message = 'Wallet Balance Retrieved.';
            return $response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $response;
        }
    }
}
