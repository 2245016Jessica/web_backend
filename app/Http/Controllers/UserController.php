<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;

use App\Http\Resources\UserResource;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\support\str;
use Illuminate\support\Facades\Hash;

use Illuminate\Http\Exceptions\HttpRespondException;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login (UserLoginRequest $request): UserResourse
    {
        $data = $request->validated();
        $user = User::where('email',$data['email'])->first();
        if (!User || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException (response([
                'error' => [
                    'message' => ['username or password wrong'],
                ]
                ],401));
        }

        $user->remember_token = str::uuid()->toString();
        $user->save();
        return new UserResource($user);
    }
}
