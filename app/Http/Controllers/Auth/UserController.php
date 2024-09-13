<?php

namespace App\Http\Controllers\Auth;

use App\Events\VerifyEmailByCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;
use PharIo\Manifest\Email;

class UserController extends Controller
{
    protected $EmailVerificationController;
    protected $UserService;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->EmailVerificationController = new EmailVerificationController();
        $this->UserService = new UserService(new EmailVerificationController(), new UserRepository());
    }


    public function register(RegisterRequest $request):JsonResponse
    {
        return $this->UserService->register($request);
    }

    public function login(LoginRequest $request):JsonResponse
    {
        return $this->UserService->login($request);
    }

    public function profile():JsonResponse
    {
        return $this->UserService->profile();
    }

    public function logout():JsonResponse
    {
        return $this->UserService->logout();
    }

    public function updateUser(UpdateUserRequest $request):JsonResponse
    {
        return $this->UserService->updateUser($request);
    }
}
