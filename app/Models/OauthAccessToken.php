<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class OauthAccessToken extends Model
{
    use HasFactory;

    /**
     * this is used to truncate all tables
     * @return mixed
     */
    public static function processTableTruncation()
    {
        self::truncate();
        App::environment('APP_ENV') === 'local'
            ? exec("./vendor/bin/sail artisan passport:install")
            : exec("php artisan passport:install");
    }

    /**
     * Fetch the tokens of a particular user with respect to the guard used
     * @param int $userID
     * @param string $guard
     * @return mixed
     */
    private static function getUserTokens(int $userID, string $guard = 'user')
    {
        return self::where('user_id', $userID)
            ->where('guard', $guard)
            ->get();
    }

    /**
     * delete a particular user access tokens
     * @param int $userID
     * @param string $guard
     */
    public static function deleteUserAccessToken(int $userID, string $guard = 'user')
    {
        $tokens = self::getUserTokens($userID, $guard);

        if(count($tokens) > 0)
            foreach ($tokens as $token)
                $token->delete();
    }

    /**
     * this adds the provider type after creating an access token
     * All the Available Guards Are:
     * 'api', 'vendor', 'terminal_manager', 'driver'
     * @param string $bearerToken
     * @param Model $user
     * @param string $guard
     */
    private static function addGuard(string $bearerToken, Model $user, string $guard = 'user')
    {
        $token = (new Parser(new JoseEncoder()))->parse($bearerToken)->claims()->all()['jti'];
        $User = self::find($token);
        if($User)
        {
            $User->guard = $guard;
            $User->user_type = get_class($user);
            $User->save();
        }
    }

    /**
     * This is an exposed function to create access token for a particular user
     * @param User $model
     * @param string $guard
     * @param array $relationships
     * @return array
     */
    public static function createAccessToken(User $model, string $guard = 'user', array $relationships = []): array
    {
        self::deleteUserAccessToken($model->id, $guard);
        $accessToken = $model->createToken('accessToken')->accessToken;
        self::addGuard($accessToken, $model, $guard);
        $response_object['accessToken'] = $accessToken;
        $response_object['profile'] = $model;
        return $response_object;
    }

    /**
     * decodes and fetches the oauth client key
     * @param string $bearerToken
     * @return mixed
     */
    public static function retrieveOauthProvider(string $bearerToken) : string
    {
        $token = (new Parser(new JoseEncoder()))->parse($bearerToken)->claims()->all()['jti'];
        $Provider = self::find($token);
        $Provider? $value = $Provider : $value = '';
        return $value;
    }

    /**
     * This is used to set the Authentication guard for a particular User
     * @param string $provider
     */
    public static function setAuthProvider(string $provider)
    {
        Log::error("auth:{$provider}");
        Config::set('auth.guards.api.provider', $provider);
    }

}
