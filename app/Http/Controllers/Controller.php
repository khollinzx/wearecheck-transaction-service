<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Controller
{
    /**
     * @return string
     */
    public function welcome(): string
    {
        return "Welcome to We Are Check ".env("APP_ENV")." API Version 1";
    }

    /**
     * @return mixed
     */
    public function getUserId(): int
    {
        return auth()->user()->getAuthIdentifier();
    }

    /**
     * @return Authenticatable|User|Model|null
     */
    public function getUser(): Authenticatable|User|Model|null
    {
        return auth()->user();
    }
}
