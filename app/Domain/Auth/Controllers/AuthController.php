<?php

namespace App\Domain\Auth\Controllers;

use App\Domain\Auth\Requests\LoginRequest;
use App\Domain\Auth\Services\AuthService;
use App\Domain\Auth\Resources\AuthResource;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="JWT-based authentication endpoints"
 * )
 */
class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AuthService $service
    ) {}

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="Login user and get JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->service->login($request->validated());

        if (!$token) {
            return $this->error('Invalid credentials', null, 401);
        }

        return $this->success(
            $this->service->respondWithToken($token),
            'Login successful'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout user and invalidate JWT token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Successfully logged out")
     * )
     */
    public function logout(): JsonResponse
    {
        $this->service->logout();
        return $this->success(null, 'Successfully logged out');
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"Authentication"},
     *     summary="Get current authenticated user info",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Authenticated user data")
     * )
     */
    public function me(): JsonResponse
    {
        $user = $this->service->me();

        if (!$user) {
            return $this->error('User not authenticated', null, 401);
        }

        return $this->success(new AuthResource($user), 'Authenticated user');
    }
}
