<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="Your API Title",
 *     version="1.0.0"
 * ),
 *
 */
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
        $users = $this->UserService->index();

        return response()->json(['users' => $users]);
    }


    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->UserService->register($request);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        return $this->UserService->login($request);
    }


    public function profile($user_id): JsonResponse
    {
        return $this->UserService->profile($user_id);
    }


    public function logout(): JsonResponse
    {
        return $this->UserService->logout();
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        return $this->UserService->update($request);
    }
}
