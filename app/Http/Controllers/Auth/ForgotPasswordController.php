<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $email = $request->input('email');

        // Throttling logic: Check if the user has exceeded the reset limit
        if (RateLimiter::tooManyAttempts('reset-password:'.$email, 5)) {
            $seconds = RateLimiter::availableIn('reset-password:'.$email);

            return response()->json([
                'status' => false,
                'message' => 'Too many password reset attempts. Please try again in '.$seconds.' seconds.',
            ], 429); // 429 Too Many Requests
        }

        // Validate the email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Attempt to send reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Hit the rate limiter on successful attempt
            RateLimiter::hit('reset-password:'.$email);

            return response()->json([
                'status' => true,
                'message' => __($status)
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => __($status)
        ], 400);
    }

    public function resetPassword(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Clear throttling after successful reset
            RateLimiter::clear('reset-password:'.$request->input('email'));

            return redirect()->route('password.success');
        }

        return response()->json([
            'status' => false,
            'message' => __($status)
        ], 400);
    }
}
