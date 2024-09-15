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

    public function register(RegisterRequest $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, $request->rules());
        if ($validationResponse) {
            return $validationResponse;
        }

        $this->userRepository->create($request->validated());

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $message = [
            'Email_message' => 'The activation code has been sent to your email',
            'token' => $token,
            'token_type' => 'bearer',
        ];

        $this->EmailVerificationController->sendEmailVerification();

        return response()->json($message);

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

        return $this->respondWithToken($token);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        $message = ['message' => 'Successfully logged out'];

        return response()->json($message, 200);
    }

    public function profile(Request $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, ['user_id' => 'required|exists:users,id|integer']);
        if ($validationResponse) {
            return $validationResponse;
        }

        $user = $this->userRepository->findById($request->user_id);
        $contacts = $user->contacts()->get();
        return response()->json(['user' => $user, 'contacts' => $contacts]);
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

        $message = [
            'message' => 'Profile updated successfully',
            'status_code' => 200,
            'success' => true,
        ];

        return response()->json($message);

    }
}
