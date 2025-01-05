<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;

class AuthService
{
    public function register($data)
    {
        try {
            $isFirstUser = User::count() === 0;

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $isFirstUser ? 'admin' : 'user',
            ]);

            return JWTAuth::fromUser($user);
        } catch (Exception $e) {
            return response()->json(['message' => 'Registration failed. Please try again later.'], 500);
        }
    }

    public function login($credentials)
{
    try {
        if ($token = JWTAuth::attempt($credentials)) {
            $user = auth()->user();

            $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);

            return response()->json([
                'token' => $token,
                'refresh_token' => $refreshToken,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    } catch (JWTException $e) {
        return response()->json(['message' => 'Failed to create token. Please try again.'], 500);
    } catch (Exception $e) {
        return response()->json(['message' => 'There was an issue connecting to the server. Please check your connection and try again.'], 503);
    }
}

    public function refreshToken($refreshToken)
    {
        try {
            $token = JWTAuth::setToken($refreshToken);
            $user = JWTAuth::authenticate($token);

            if (!$user) {
                return response()->json(['message' => 'Invalid refresh token.'], 401);
            }

            $newAccessToken = JWTAuth::fromUser($user);

            return ['access_token' => $newAccessToken];
        } catch (TokenInvalidException $e) {

            return response()->json(['message' => 'The refresh token is invalid or expired. Please login again.'], 401);
        } catch (JWTException $e) {

            return response()->json(['message' => 'There was an issue with the token. Please try again.'], 500);
        }
    }
}
