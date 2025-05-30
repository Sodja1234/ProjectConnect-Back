<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetLinkController extends Controller
{

    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $message = Password::sendResetLink(
            $request->only('email')
        );

        if ($message != Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => trans($message)
            ]);
        }

        return response()->json(['status' => Response::HTTP_OK]);
    }
}
