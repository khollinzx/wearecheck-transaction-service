<?php

namespace App\Http\Requests;

use App\Utils\JsonResponseAPI;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class BaseFormRequest extends FormRequest
{
    /**
     * BaseFormRequest constructor.
     */
   public function __construct()
   {
       parent::__construct();
   }

    /**
     * THis overrides the default throwable failed message in json format
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        Log::error("Form validation error", [$validator->errors()]);
        throw new HttpResponseException(
            JsonResponseAPI::errorResponse(
                $validator->errors()->first(),
                JsonResponseAPI::$UNPROCESSABLE_ENTITY,
                "Form Error"
            )
        );
    }
}
