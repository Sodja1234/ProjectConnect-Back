<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetLinkController extends Controller
{

    public function __invoke(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $validation->errors()
            ]);
        }

        $message = Password::sendResetLink(
            $request->only('email')
        );

        if ($message != Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => $message
            ]);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
        ]);
    }
}
