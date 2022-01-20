<?php

namespace App\Http\Requests;

class UserRegisterRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|email|unique:users',
            'password' => 'required|alpha_num|min:6|max:15'
        ];
    }
}
