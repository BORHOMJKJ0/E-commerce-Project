<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Services\EmailService;

class EmailVerificationController extends Controller
{
    protected $EmailService;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->EmailService = new EmailService;
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
