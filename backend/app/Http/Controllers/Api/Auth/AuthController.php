<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $request->tenant_id,
                'organization_id' => $request->organization_id,
                'branch_id' => $request->branch_id,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tenant_id' => $user->tenant_id,
                    'organization_id' => $user->organization_id,
                    'branch_id' => $user->branch_id,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Registration failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Login user and create token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            
            // Check if tenant is active
            if ($user->tenant && $user->tenant->status !== 'active') {
                return $this->sendError('Account suspended', [
                    'Your account has been suspended. Please contact support.'
                ], 403);
            }

            // Revoke old tokens
            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tenant_id' => $user->tenant_id,
                    'organization_id' => $user->organization_id,
                    'branch_id' => $user->branch_id,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');
        } catch (ValidationException $e) {
            return $this->sendError('Invalid credentials', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->sendError('Login failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Logout user (revoke token).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->sendResponse([], 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Logout from all devices (revoke all tokens).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens()->delete();

            return $this->sendResponse([], 'Logged out from all devices successfully');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Get authenticated user information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->load(['tenant', 'organization', 'branch', 'roles', 'permissions']);

            return $this->sendResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tenant_id' => $user->tenant_id,
                    'organization_id' => $user->organization_id,
                    'branch_id' => $user->branch_id,
                    'tenant' => $user->tenant,
                    'organization' => $user->organization,
                    'branch' => $user->branch,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
            ], 'User information retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve user information', [$e->getMessage()], 500);
        }
    }

    /**
     * Refresh authentication token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Token refresh failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Send password reset link.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return $this->sendResponse([], 'Password reset link sent successfully');
            }

            return $this->sendError('Failed to send reset link', [$status], 500);
        } catch (\Exception $e) {
            return $this->sendError('Failed to process password reset', [$e->getMessage()], 500);
        }
    }

    /**
     * Reset user password.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    // Revoke all existing tokens
                    $user->tokens()->delete();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->sendResponse([], 'Password reset successfully');
            }

            return $this->sendError('Failed to reset password', [$status], 500);
        } catch (\Exception $e) {
            return $this->sendError('Failed to process password reset', [$e->getMessage()], 500);
        }
    }
}
