<?php

namespace App\Services;

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use ValidationTrait;

    protected $EmailVerificationController;

    protected $userRepository;

    public function __construct(EmailVerificationController $EmailVerificationController, UserRepository $userRepository)
    {
        $this->EmailVerificationController = $EmailVerificationController;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/users/all",
     *     summary="Get all users with their contact information",
     *     tags={"Users"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of users and their contact details",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Hasan"),
     *                 @OA\Property(property="email", type="string", example="hzaeter@gmail.com"),
     *                 @OA\Property(property="mobile", type="string", example="0935917557"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=3),
     *                     @OA\Property(property="contact_type_id", type="integer", example=5)
     *                 ))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, invalid or missing token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return $this->userRepository->get_users_contacts();
    }

    /**
     * @OA\Post(
     *     path="/api/users/register",
     *     summary="Register a new user",
     *     tags={"Users"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "mobile", "gender", "password", "password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="hasan zaeter"),
     *             @OA\Property(property="email", type="string", example="hzaeter@gmail.com"),
     *             @OA\Property(property="mobile", type="string", example="0935917667"),
     *             @OA\Property(property="gender", type="string", example="male or female"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="email-verification", type="string", example="An activation code has been sent to your email"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="hasan zaeter"),
     *                 @OA\Property(property="email", type="string", example="hzaeter04@gmail.com")
     *             )
     *         )
     *     ),
     *
     *          @OA\Response(
     *               response=400,
     *               description="Bad Request",
     *          @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Validation error"),
     *               @OA\Property(property="errors", type="object", example={})
     *          )
     *     ),
     *      @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function register(RegisterRequest $request)
    {
        $validationResponse = $this->validateRequest($request, $request->rules());
        if ($validationResponse) {
            return $validationResponse;
        }

        $user = $this->userRepository->create($request->validated());

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->EmailVerificationController->sendEmailVerification($request->email);

        $user = $this->userRepository->get_userById($user->id);

        return response()->json([
            'message' => 'User registered successfully',
            'email-verification' => 'An activation code has been sent to your email',
            'user' => $user,
        ], 201);

    }

    /**
     * @OA\Post(
     *     path="/api/users/login",
     *     summary="User login",
     *     tags={"Users"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="hzaeter04@gmail.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="jwt-token"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Hasan Zaeter"),
     *                 @OA\Property(property="email", type="string", example="hzaeter@gmail.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error or invalid password"),
     *             @OA\Property(property="errors", type="object", example={})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */

    public function login(LoginRequest $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, $request->rules());

        if ($validationResponse) {
            return $validationResponse;
        }

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $this->userRepository->findByEmail($request->email);

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users/logout",
     *     summary="Logout the current user",
     *     tags={"Users"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Successfully logged out"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="hasan zaeter"),
     *                 @OA\Property(property="email", type="string", example="hzaeter04@gmail.com")
     *             )
     *         )
     *     )
     * )
     */

    public function logout(): JsonResponse
    {
        $user = [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ];

        auth()->logout();
        $message = [
            'message' => 'Successfully logged out',
            'user' => $user,
        ];

        return response()->json($message, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users/profile/{user_id}",
     *     summary="Get user profile by user ID",
     *     tags={"Users"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer"),
     *         description="The ID of the user to retrieve the profile for"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of user profile and contact details",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Hasan Zaeter"),
     *             @OA\Property(property="email", type="string", example="hzaeter@gmail.com"),
     *             @OA\Property(property="mobile", type="string", example="0935917667"),
     *             @OA\Property(property="contacts", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="contact_type_id", type="integer", example=2)
     *             ))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized - Invalid or missing token"
     *      )
     * )
     */
    public function profile($user_id): JsonResponse
    {
        $user = $this->userRepository->findById($user_id);
        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user = $this->userRepository->get_user_contactById($user_id);

        return response()->json($user, 200);
    }

    //    public function respondWithToken($token): JsonResponse
    //    {
    //        return response()->json([
    //            'access_token' => $token,
    //            'token_type' => 'bearer',
    //            //'expires_in' => auth()->factory()->getTTL() * 60
    //        ]);
    //    }

    /**
     * @OA\Put(
     *     path="/api/users/update",
     *     summary="Update user profile",
     *     tags={"Users"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="Hasan Zaeter"),
     *             @OA\Property(property="mobile", type="string", example="0935917667"),
     *             @OA\Property(property="old_password", type="string", example="oldpassword123"),
     *             @OA\Property(property="new_password", type="string", example="newpassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", example="newpassword123"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="user", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Hasan Zaeter"),
     *                 @OA\Property(property="email", type="string", example="hzaeter@gmail.com"),
     *                 @OA\Property(property="mobile", type="string", example="0935917667"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="contact_type_id", type="integer", example=2)
     *              ))
     *             )),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Validation error or incorrect old password"),
     *             @OA\Property(property="errors", type="object", example={})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, invalid old password",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request, $request->rules());
        if ($validationResponse) {
            return $validationResponse;
        }

        $user_id = auth()->user()->id;
        $newData = [
            'name' => $request->name,
            'password' => Hash::make($request->new_password),
            'mobile' => $request->mobile,
        ];

        $user = $this->userRepository->findById($user_id);
        $this->userRepository->update($user, $newData);

        $user = $this->userRepository->get_user_contactById($user_id);

        $message = [
            'message' => 'Profile updated successfully',
            'user' => $user,
            'success' => true,
        ];

        return response()->json($message, 200);

    }
}
