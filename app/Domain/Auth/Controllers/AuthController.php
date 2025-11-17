<?php

namespace App\Domain\Auth\Controllers;

use App\Domain\Auth\Requests\LoginRequest;
use App\Domain\Auth\Requests\RegisterRequest;
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
     * path="/api/auth/register",
     * summary="Register a new user",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "email", "password"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="User registered successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="User registered successfully")
     * )
     * ),
     * @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $this->service->register($request->validated());

        return $this->success(null, 'User registered successfully', 201);
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
