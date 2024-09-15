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

    public function sendEmailVerification(Request $request): \Illuminate\Http\JsonResponse
    {

        $validationResponse = $this->validateRequest($request, ['email' => 'required|email|exists:users,email']);
        if ($validationResponse) {
            return response()->json($validationResponse->original, 400);
        }

        return $this->EmailService->sendEmailVerification($request->email);
    }
}
