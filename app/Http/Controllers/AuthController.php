<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        Log::info('JWT Secret:', [env('JWT_SECRET')]);
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $token = $this->authService->register($request->all());

        return response()->json(['token' => $token]);
    }

    public function login(Request $request)
    {
        Log::info('JWT Secret:', [env('JWT_SECRET')]);
 
        $credentials = $request->only('email', 'password');
        $response = $this->authService->login($credentials);

        if ($response) {
            return $response;
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->input('refresh_token');
        $response = $this->authService->refreshToken($refreshToken);

        if ($response) {
            return response()->json($response);
        }

        return response()->json(['error' => 'Invalid refresh token'], 401);
    }
}
