<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\NewPasswordRequest;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Symfony\Component\HttpFoundation\Response;

class NewPasswordController extends Controller
{
    public function __invoke(NewPasswordRequest $request): JsonResponse
    {
        $message = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($message != Password::PASSWORD_RESET) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => trans($message)
            ]);
        }

        return response()->json([
            'status' => Response::HTTP_OK
        ]);
    }
}
