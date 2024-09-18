<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordService;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;

class ForgetPasswordController extends Controller
{
    use ValidationTrait;

    protected $PasswordService;

    public function __construct()
    {
        $this->PasswordService = new PasswordService;
    }

    public function forgetPassword(User $user): JsonResponse
    {
        return $this->PasswordService->forgetPassword($user);
    }
}
