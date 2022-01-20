<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetForgottenPasswordRequest;
use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserForgottenPasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserChangedPasswordResource;
use App\Http\Resources\UserRegisteredResource;
use App\Http\Services\UserService;
use Carbon\Carbon;
use Exception;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    public function register(UserRegisterRequest $request)
    {
        $user = $this->userService->store($request->validated());

        return new UserRegisteredResource($user);
    }

    public function logIn(UserLoginRequest $request)
    {
        $user = $this->userService->login($request->validated());

        return new UserRegisteredResource($user);
    }

    public function changePassword(UserChangePasswordRequest $request)
    {
        $user = $this->userService->changePassword($request->validated());

        return new UserChangedPasswordResource($user);
    }

    public function forgotPassword(UserForgottenPasswordRequest $request)
    {
        $this->userService->sendLink($request->validated());

        return response()->json(['message' => 'Reset link sent'], 200);
    }

    public function resetForgottenPassword(ResetForgottenPasswordRequest $request)
    {
        $this->userService->resetForgottenPassword(array_merge($request->validated(), ['token' => $request->token]));

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

}
