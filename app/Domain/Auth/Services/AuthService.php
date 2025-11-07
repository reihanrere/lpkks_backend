<?php

namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials): ?string
    {
        return Auth::attempt($credentials) ?: null;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function me()
    {
        return Auth::user();
    }

    public function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::factory()->getTTL() * 60,
            'user'         => Auth::user(),
        ];
    }
}
