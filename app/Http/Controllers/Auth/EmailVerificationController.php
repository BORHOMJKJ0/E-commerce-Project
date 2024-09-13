<?php

namespace App\Http\Controllers\Auth;

use App\Events\VerifyEmailByCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Jobs\EmailVerificationJob;
use App\Models\User;
use App\Services\EmailService;
use App\Traits\ValidationTrait;
use Ichtrojan\Otp\Otp;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    protected $EmailService;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->EmailService = new EmailService();
    }

    public function email_verification(EmailVerificationRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->EmailService->email_verification($request);
    }

    public function sendEmailVerification(): \Illuminate\Http\JsonResponse
    {
        return $this->EmailService->sendEmailVerification();
    }


}
