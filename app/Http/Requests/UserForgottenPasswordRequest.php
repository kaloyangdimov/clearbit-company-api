<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserForgottenPasswordRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email'
        ];
    }
}
