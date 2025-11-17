<?php

namespace App\Domain\Auth\Services;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthService
{
    use ApiResponse;
    public function login(array $credentials): ?string
    {
        return Auth::attempt($credentials) ?: null;
    }

    public function register(array $data): void
    {
        $is_exist = User::where('email', $data['email'])->first();

        if ($is_exist) {
            throw new HttpResponseException($this->error('User email already exist', 400));
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $userRole = Role::where('name', 'user')->first();

        $user->assignRole($userRole);
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
