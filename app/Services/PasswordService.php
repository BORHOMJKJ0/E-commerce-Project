<?php

namespace App\Services;

use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Jobs\ForgetPasswordJob;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordService
{
    use ValidationTrait;
    protected $userRepository;
    public function __construct(){
        $this->userRepository = new UserRepository();
    }
    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request,$request->rules());
        if($validationResponse){
            return $validationResponse;
        }

        $data = $request->validated();
        $user = $this->userRepository->findByEmail($data['email']);

        ForgetPasswordJob::dispatch($user);
        $success['success'] = true;
        return response()->json($success, 200);
    }


    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request,$request->rules());

        if($validationResponse){
            return $validationResponse;
        }

        $otp2 = $this->otp->validate($request->email, $request->otp);

        if (!$otp2->status) {
            return response()->json(['error' => $otp2], 401);
        }
        $user = User::where('email', $request->email)->first();

        $this->userRepository->update($user,['password' => Hash::make($request->password)]);

//        $user->tokens()->delete();
        $success['success'] = true;

        return response()->json(['success' => $success], 200);

    }
}
