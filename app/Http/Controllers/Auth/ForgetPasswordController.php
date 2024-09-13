<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\ForgetPasswordJob;
use App\Services\PasswordService;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ResetPasswordVerificationNotifiaction;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use Illuminate\Support\Facades\DB;

class ForgetPasswordController extends Controller
{
    use ValidationTrait;
    protected $PasswordService;
    public function __construct(){
        $this->PasswordService = new PasswordService();
    }
    public function forgetPassword(ForgetPasswordRequest $request):JsonResponse
    {
       return $this->PasswordService->forgetPassword($request);
    }
}
