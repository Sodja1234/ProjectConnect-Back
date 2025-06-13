<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use App\Events\RegisteredUserEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RegisterController extends Controller
{

    public function __invoke(RegisterUserRequest $request)
    {
        $name = $request->validated('name');

        $user = User::create([
            'name' => $name,
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        $user->update([
            'slug' => Str::slug(sprintf("%s-%s", $name, $user->id))
        ]);

        event(new RegisteredUserEvent($user));

        return response()->json([
            'status' => HttpResponse::HTTP_CREATED,
        ]);
    }
}
