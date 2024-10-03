<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $EmailVerificationController;

    protected $UserService;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->EmailVerificationController = new EmailVerificationController;
        $this->UserService = new UserService(new EmailVerificationController, new UserRepository);
    }

    public function index(): JsonResponse
    {
        return $this->UserService->index();
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->UserService->register($request);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->UserService->login($request);
    }

    public function profile(User $user)
    {
        return $this->UserService->profile($user);
    }

    public function logout(): JsonResponse
    {
        return $this->UserService->logout();
    }

    public function update(UpdateUserRequest $request, $user_id): JsonResponse
    {
        return $this->UserService->update($request, $user_id);
    }

    public function destroy()
    {
        return $this->UserService->destroy();
    }
}
