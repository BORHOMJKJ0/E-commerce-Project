<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EmailVerificationRequest;
use App\Jobs\EmailVerificationJob;
use App\Repositories\UserRepository;
use App\Traits\ValidationTrait;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\JsonResponse;

class EmailService
{
    use ValidationTrait;

    protected $userRepository;

    protected $otp;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
        $this->otp = new Otp;
    }

    /**
     * @OA\Post(
     *     path="/api/users/email-verification",
     *     summary="Verify user's email using OTP",
     *     tags={"Email Verification"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *                required={"email", "code"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="hzaeter04@gmail.com", description="User's email to verify"),
     *             @OA\Property(property="code", type="string", example="123456", description="OTP code for email verification")
     *         )
     *        )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Email verification successfully",
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email verification successfully"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *         ),
     *
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid email activation code",
     *
     *         @OA\JsonContent(
     *
     *         @OA\Property(property="successful", type="boolean", example=false),
     *         @OA\Property(property="message", type="string", example="Validation failed"),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(
     *                 property="email",
     *                 type="array",
     *
     *                 @OA\Items(type="string", example="The selected email is invalid.")
     *             )
     *         ),
     *
     *          @OA\Property(property="status_code", type="integer", example=400),
     *  )
     *     )
     * )
     */
    public function email_verification(EmailVerificationRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->email);
        if (! $user) {
            return ResponseHelper::jsonResponse([], 'Email not found', 404);
        }
        $otp2 = $this->otp->validate($user->email, $request->code);

        if (! $otp2->status) {
            return ResponseHelper::jsonResponse([], 'Your email activation code is incorrect', 400, false);
        } else {
            $this->userRepository->VerifyEmail($user);

            return ResponseHelper::jsonResponse([], 'Email verification successfully');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/email-verification/{email}",
     *     summary="Send an email verification code to the user",
     *     tags={"Email Verification"},
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="email", example="hzaeter04@gmail.com"),
     *         description="User's email to send the verification code to"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Verification code sent successfully",
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *             @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The activation code has been sent to your email"),
     *              @OA\Property(property="status_code", type="integer", example=200),
     *         )
     *       )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Email already verified",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Email already verified"),
     *              @OA\Property(property="status_code", type="integer", example=400),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Email not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Email not found"),
     *              @OA\Property(property="status_code", type="integer", example=404),
     *         )
     *     )
     * )
     */
    public function sendEmailVerification($user): JsonResponse
    {
        if ($user->email_verified_at) {
            return ResponseHelper::jsonResponse([], 'Email already verified', 401, false);
        } else {
            EmailVerificationJob::dispatch($user);
            return ResponseHelper::jsonResponse([], 'The activation code has been sent to your email');
        }
    }
}
