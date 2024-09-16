<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Services\EmailService;
use App\Traits\ValidationTrait;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    use ValidationTrait;

    protected $EmailService;

    public function __construct()
    {
        $this->EmailService = new EmailService;
    }

    public function email_verification(EmailVerificationRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->EmailService->email_verification($request);
    }

    public function sendEmailVerification($email): \Illuminate\Http\JsonResponse
    {
        return $this->EmailService->sendEmailVerification($email);
    }
}
