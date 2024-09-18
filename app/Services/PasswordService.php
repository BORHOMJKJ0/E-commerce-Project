<?php

namespace App\Services;

use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Jobs\ForgetPasswordJob;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\ValidationTrait;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordService
{
    use ValidationTrait;

    protected $userRepository;

    protected $otp;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
        $this->otp = new Otp;
    }

    public function forgetPassword($email): JsonResponse
    {
        if (! str_ends_with($email, '@gmail.com')) {
            return response()->json(['message' => 'Email is not a valid email address, it must be end with a @gmail.com']);
        }
        $user = $this->userRepository->findByEmail($email);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 403);
        }
        ForgetPasswordJob::dispatch($user);
        $message = [
            'message' => 'The code has been successfully sent to your email',
            'success' => true,
        ];

        return response()->json($message, 200);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, $request->rules());

        if ($validationResponse) {
            return $validationResponse;
        }

        $otp2 = $this->otp->validate($request->email, $request->otp);

        if (! $otp2->status) {
            return response()->json(['error' => $otp2], 401);
        }
        $user = User::where('email', $request->email)->first();

        $this->userRepository->update($user, ['password' => Hash::make($request->password)]);

        //        $user->tokens()->delete();
        $success['success'] = true;

        return response()->json(['success' => $success], 200);

    }
}
