<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $validated = $this->validateRequest([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'device_name' => 'sometimes|string|max:255',
        ], $request->all());

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            activity()
                ->causedByAnonymous()
                ->withProperties(['email' => $validated['email'], 'ip' => $request->ip()])
                ->log('Failed login attempt - user not found');

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->locked_until && $user->locked_until->isFuture()) {
            return $this->errorResponse('Account temporarily locked due to multiple failed attempts', 423);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            $user->increment('failed_login_attempts');
            
            if ($user->failed_login_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinutes(15)]);
                activity()
                    ->causedBy($user)
                    ->withProperties(['ip' => $request->ip(), 'attempts' => $user->failed_login_attempts])
                    ->log('Account locked due to failed attempts');
            }

            activity()
                ->causedByAnonymous()
                ->withProperties(['email' => $validated['email'], 'ip' => $request->ip(), 'attempts' => $user->failed_login_attempts])
                ->log('Failed login attempt - wrong password');

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            return $this->errorResponse('Email not verified', 403);
        }

        if (!$user->is_active) {
            return $this->errorResponse('Account is deactivated', 403);
        }

        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now()
        ]);

        $token = $user->createToken($validated['device_name'] ?? 'Desktop App')->plainTextToken;

        activity()
            ->causedBy($user)
            ->withProperties(['device_name' => $validated['device_name'] ?? 'Desktop App', 'ip' => $request->ip()])
            ->log('User logged in successfully');

        return $this->successResponse([
            'user' => $user->load('roles', 'permissions'),
            'token' => $token,
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        activity()
            ->causedBy($request->user())
            ->log('User logged out');

        return $this->successResponse(null, 'Logout successful');
    }

    public function user(Request $request)
    {
        return $this->successResponse($request->user()->load('roles', 'permissions'));
    }

    public function register(Request $request)
    {
        $validated = $this->validateRequest([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], $request->all());

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('user');

        activity()
            ->causedBy($user)
            ->log('User registered');

        return $this->successResponse($user, 'Registration successful', 201);
    }
}