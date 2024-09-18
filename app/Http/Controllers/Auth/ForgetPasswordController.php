<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
=======
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Models\User;
>>>>>>> 1b9ca55b03482f63d84a43dd0cd9871d5a372669
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
