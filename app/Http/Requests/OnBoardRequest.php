<?php

namespace App\Http\Requests;

use App\Utils\JsonResponseAPI;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OnBoardRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if(in_array(basename($this->url()), ['login', 'register'])) {
            # validate header
            if(!$this->hasHeader('Content-Type') || $this->header('Content-Type') !== 'application/json')
                throw new HttpResponseException(JsonResponseAPI::errorResponse( 'Include Content-Type and set the value to: application/json in your header.', ResponseAlias::HTTP_BAD_REQUEST));
        }
        switch (basename($this->url())) {
            case "login"       : return $this->validateLogin();
            case "register"       : return $this->validateSignup();
        }
    }

    /**
     * @return string[]
     */
    private function validateLogin(): array
    {
        return [
            'email' => "required|regex:/(.+)@(.+)\.(.+)/i|exists:users,email",
            'password' => "required|string"
        ];
    }

    /**
     * @return string[]
     */
    private function validateSignup(): array
    {
        return [
            'email' => "required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email",
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'password' => "required|string|min:6",
            'confirmed_password' => "required|string|same:password|min:6"
        ];
    }
}
