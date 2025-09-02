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

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            activity()
                ->causedByAnonymous()
                ->withProperties(['email' => $validated['email'], 'ip' => $request->ip()])
                ->log('Failed login attempt');

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            return $this->errorResponse('Email not verified', 403);
        }

        $token = $user->createToken($validated['device_name'] ?? 'Desktop App')->plainTextToken;

        activity()
            ->causedBy($user)
            ->withProperties(['device_name' => $validated['device_name'] ?? 'Desktop App'])
            ->log('User logged in');

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