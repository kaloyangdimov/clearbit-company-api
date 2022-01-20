<?php

namespace App\Http\Requests;

class ResetForgottenPasswordRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'            => 'required|exists:users,email',
            'password'         => 'required|alpha_num|min:6|max:15',
            'password_confirm' => 'required|same:password',
        ];
    }
}
