<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use App\Events\RegisteredUserEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RegisterController extends Controller
{

    public function __invoke(RegisterUserRequest $request)
    {

        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        event(new RegisteredUserEvent($user));

        return response()->json([
            'status' => HttpResponse::HTTP_CREATED,
        ]);
    }
}
