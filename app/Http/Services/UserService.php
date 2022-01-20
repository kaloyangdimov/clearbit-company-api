<?php

namespace App\Http\Services;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Password;

class UserService extends Controller
{
    public function store(array $attributes)
    {
        $user = new User();
        $user->fill($attributes);
        $user->handleToken();

        if (!$user->save()) {
            throw new ApiException(__('custom.error_registering_user'), 500);
        }

        return $user;
    }

    public function login(array $attributes)
    {
        $user = User::firstWhere('email', $attributes['email']);

        if ($user->token_valid_to < now()) {
            $user->handleToken();

            if (!$user->save()) {
                throw new ApiException(__('custom.error_refreshing_token'), 500);
            }
        }

        return $user;
    }

    public function changePassword(array $attributes)
    {
        $user = User::firstWhere('token', request()->bearerToken());
        $user->fill($attributes);

        if (!$user->save()) {
            throw new ApiException(__('custom.error_changing_password'), 500);
        }

        return $user;
    }

    public function sendLink(array $attributes)
    {
        return Password::sendResetLink($attributes);
    }

    public function resetForgottenPassword(array $attributes)
    {
        $user = User::firstWhere('email', $attributes['email']);

        if (!Password::tokenExists($user, $attributes['token'])) {
            throw new ApiException('Invalid reset token', 500);
        }

        $resetStatus = Password::reset($attributes,
            function ($user, $password) {
                $user->password = $password;
                $user->save();
            }
        );

        return $resetStatus === Password::PASSWORD_RESET;
    }
}
