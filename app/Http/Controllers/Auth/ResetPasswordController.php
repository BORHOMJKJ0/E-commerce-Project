<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\PasswordService;
use App\Traits\ValidationTrait;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use ValidationTrait;

    private $otp;
    protected $PasswordService;

    public function __construct(PasswordService $PasswordService, Otp $otp)
    {
        $this->PasswordService = new PasswordService();
        $this->otp = $otp;
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->PasswordService->resetPassword($request);
    }
}
