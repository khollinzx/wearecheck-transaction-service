<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserWallet;

class UserObserver
{
    /**
     * Hook to initialize a user wallet once this user record has been created successfully.
     */
    public function created(User $model): void
    {
        UserWallet::repo()->createModel(['user_id' => $model->getId()]);
    }
}
