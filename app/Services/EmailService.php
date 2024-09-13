<?php
namespace App\Services;
use App\Http\Requests\EmailVerificationRequest;
use App\Jobs\EmailVerificationJob;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\ValidationTrait;
use Ichtrojan\Otp\Otp;

class EmailService{
    use ValidationTrait;
    protected $userRepository;
    protected $otp;
    public function __construct(){
        $this->userRepository = new UserRepository();
        $this->otp = new Otp();
    }
    public function email_verification(EmailVerificationRequest $request): \Illuminate\Http\JsonResponse
    {
        $validationResponse = $this->validateRequest($request,$request->rules());
        if($validationResponse){
            return $validationResponse;
        }

        $user = $this->userRepository->findById(auth()->user()->id);
        $otp2 = $this->otp->validate($user->email, $request->otp);

        if (!$otp2->status) {
            return response()->json(['error' => 'Your email activation code is invalid'], 400);
        }else{
            $this->userRepository->VerifyEmail($user);
            return response()->json(['success' => 'Email verification successfully'], 200);
        }
    }

    public function sendEmailVerification(): \Illuminate\Http\JsonResponse
    {
        $user_id = auth()->user()->id;
        $user = $this->userRepository->findById($user_id);

        EmailVerificationJob::dispatch($user);

        return response()->json(['message'=>'The activation code has been sent to your email']);
    }
}
