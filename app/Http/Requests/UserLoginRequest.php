<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Auth;

class UserLoginRequest extends ApiRequest
{
    public function authorize()
    {
        if (!Auth::attempt(['email' => request()->email, 'password' => request()->password])) {
            throw new ApiException('Access denied', 401);
        }
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|string|exists:users,email',
            'password' => 'required|alpha_num|min:6|max:15'
        ];
    }
}
