<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Laravel\Socialite\Facades\Socialite;
use App\Jobs\SendOtpEmailJob;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponseTrait;

    // Register
    public function register(RegisterRequest $request)
    {
        $otp = rand(100000, 999999);
        $data = $request->validated();

        $user = User::create([
            'full_name'   => $data['full_name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => $data['role'] ?? 'patient',
            'phone'       => $data['phone'],
            'age'         => $data['age'] ?? null,
            'address'     => $data['address'] ?? null,
            'profile_img' => $data['profile_img'] ?? null,
            'is_active'   => true,
        ]);

        Cache::put('otp_' . $user->id, $otp, now()->addMinutes(10));

        try {
            dispatch(new SendOtpEmailJob($user->email, $otp, 'Email Verification Code'));
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send OTP via Email. ' . $e->getMessage(), 500);
        }

        return $this->successResponse(['user_id' => $user->id], 'User registered successfully. OTP sent via Email.', 201);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id'           => 'required|exists:users,id',
            'verification_code' => 'required|string',
        ]);

        $user = User::find($request->user_id);
        $cachedOtp = Cache::get('otp_' . $user->id);

        if (!$cachedOtp || $cachedOtp != $request->verification_code) {
            return $this->errorResponse('Invalid or expired verification code.', 422);
        }

        Cache::forget('otp_' . $user->id);
        $user->email_verified_at = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 'Email verified successfully.');
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::find($request->user_id);

        $otp = rand(100000, 999999);
        Cache::put('otp_' . $user->id, $otp, now()->addMinutes(10));

        try {
            dispatch(new SendOtpEmailJob($user->email, $otp, 'Resend Verification Code'));
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to resend OTP via Email. ' . $e->getMessage(), 500);
        }

        return $this->successResponse(null, 'OTP resent successfully via Email.');
    }

    // Login
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = null;
        if (!empty($data['email'])) {
            $user = User::where('email', $data['email'])->first();
        } elseif (!empty($data['phone'])) {
            $user = User::where('phone', $data['phone'])->first();
        }

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('The provided credentials are incorrect.', 401);
        }

        if (is_null($user->email_verified_at)) {
            return $this->errorResponse('Please verify your email first.', 403);
        }

        if (!$user->is_active) {
            return $this->errorResponse('User is inactive.', 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 'User logged in successfully.');
    }



    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse([], 'User logged out successfully.');
    }

    // Change Password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed'
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('Current password is incorrect.', 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 'Password changed successfully, all sessions revoked.');
    }

    // Send Reset Code
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse('Email not found!', 404);
        }

        $code = rand(100000, 999999);
        Cache::put('reset_' . $user->id, $code, now()->addMinutes(10));

        try {
            Mail::to($user->email)->send(new OtpMail($code, 'Password Reset Code'));
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send reset code via Email. ' . $e->getMessage(), 500);
        }

        return $this->successResponse(null, 'Reset code sent via Email.');
    }

    // Update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email'        => 'required|email',
            'code'         => 'required',
            'new_password' => 'required|min:6|confirmed'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $cachedCode = Cache::get('reset_' . $user->id);

        if (!$cachedCode || $cachedCode != $request->code) {
            return $this->errorResponse('Invalid or expired reset code', 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        Cache::forget('reset_' . $user->id);

        return $this->successResponse(null, 'Password reset successfully.');
    }

    // Social Login




    public function socialLogin(Request $request, $provider)
    {
        $request->validate(['token' => 'required']);

        try {
            if ($provider === 'google') {
                $socialUser = Socialite::driver('google')->stateless()->userFromToken($request->token);
            } elseif ($provider === 'facebook') {
                $socialUser = Socialite::driver('facebook')->stateless()->userFromToken($request->token);
            } else {
                return $this->errorResponse('Unsupported provider', 400);
            }

            $providerId = $socialUser->getId();

            // provider + provider_id
            $user = User::where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            // try to find by email if not found by provider_id
            if (!$user && $socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            // Create user if not found
            if (!$user) {
                $email = $socialUser->getEmail() ?? 'fb_user_' . $providerId . '@example.com';

                // Ensure unique email
                $existingEmailUser = User::where('email', $email)->first();
                if ($existingEmailUser) {
                    $email = 'fb_user_' . $providerId . '_' . Str::random(4) . '@example.com';
                }

                $user = User::create([
                    'full_name'   => $socialUser->getName() ?? 'Unknown',
                    'email'       => $email,
                    'password'    => Hash::make(Str::random(12)),
                    'phone'       => mt_rand(1000000000, 9999999999),
                    'is_active'   => true,
                    'provider'    => $provider,
                    'provider_id' => $providerId,
                ]);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ], 'User logged in successfully via ' . $provider);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to login with ' . $provider . '. ' . $e->getMessage(), 500);
        }
    }
}
