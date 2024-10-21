<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TransactionRequest extends BaseFormRequest
{
    public function __construct(protected Controller $controller)
    {
        parent::__construct();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if(in_array(basename($this->url()), ['transaction'])) {
            # validate header
            if(!$this->hasHeader('Content-Type') || $this->header('Content-Type') !== 'application/json')
                throw new HttpResponseException(JsonResponseAPI::errorResponse( 'Include Content-Type and set the value to: application/json in your header.', ResponseAlias::HTTP_BAD_REQUEST));
        }
        switch (basename($this->url())) {
            case "transaction"       : return $this->validatePayment();
        }
    }

    /**
     * @return string[]
     */
    private function validatePayment(): array
    {
        return [
            'type' => 'required|string|in:Credit,Debit',
            'amount' => [
                'required',
                'numeric',
                'min:100',
                function($key, $value, $callback) {
                    if ($this->type === 'Debit') {
                        /** @var UserWallet $user_wallet */
                        $user_wallet = UserWallet::repo()->findSingleByWhereClause(['user_id' => $this->controller->getUserId()]);
                        if(UserWallet::repo()->checkUserWalletSufficiency($user_wallet, $value))
                            return $callback("Sorry, your wallet balance is not sufficient for this transaction.");
                    }
                }
            ]
        ];
    }
}
