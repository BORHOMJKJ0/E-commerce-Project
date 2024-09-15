<?php

namespace App\Services;

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use ValidationTrait;

    protected $EmailVerificationController;

    protected $userRepository;

    public function __construct(EmailVerificationController $EmailVerificationController, UserRepository $userRepository)
    {
        $this->EmailVerificationController = $EmailVerificationController;
        $this->userRepository = $userRepository;
    }

    public function show_all_users(){
        return $this->userRepository->get_users_contacts();
    }

    public function register(RegisterRequest $request)
    {
        $validationResponse = $this->validateRequest($request, $request->rules());
        if ($validationResponse) {
            return $validationResponse;
        }

        $user = $this->userRepository->create($request->validated());

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->EmailVerificationController->sendEmailVerification($request);

        $user = $this->userRepository->get_user_contact($user->id);

        return response()->json($user, 201);

    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, $request->rules());

        if ($validationResponse) {
            return $validationResponse;
        }

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $this->userRepository->findByEmail($request->email);

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    public function logout(): JsonResponse
    {
        $user = [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ];

        auth()->logout();
        $message = [
            'message' => 'Successfully logged out',
            'user' => $user,
        ];

        return response()->json($message, 200);
    }

    public function profile(Request $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, ['user_id' => 'required|exists:users,id|integer']);
        if ($validationResponse) {
            return $validationResponse;
        }

        $user = $this->userRepository->findById($request->user_id);

        $user = $this->userRepository->get_user_contact($request->user_id);

        return response()->json($user, 200);
    }

    public function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, $request->rules());
        if ($validationResponse) {
            return $validationResponse;
        }

        $user_id = auth()->user()->id;
        $newData = [
            'name' => $request->name,
            'password' => Hash::make($request->new_password),
            'mobile' => $request->mobile,
        ];

        $user = $this->userRepository->findById($user_id);
        $this->userRepository->update($user, $newData);

        $user = $this->userRepository->get_user_contact($user_id);

        $message = [
            'message' => 'Profile updated successfully',
            'user' => $user,
            'success' => true,
        ];

        return response()->json($message, 200);

    }
}
