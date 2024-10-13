<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserContactsResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\ValidationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use LaravelLang\Publisher\Concerns\Has;

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
     *        @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $users = $this->userRepository->GetAllUsers();

        $data = ['users' => UserContactsResource::collection($users)];

        return ResponseHelper::jsonResponse($data, 'Users retrieved successfully');
        // return response()->json(['users' =>], 200);
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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *          @OA\Schema(
     *                 type="object",
     *             required={"first_name","last_name", "email", "mobile", "address", "password", "password_confirmation"},
     *
     *             @OA\Property(property="first_name", type="string", example="hasan",description="User first name"),
     *             @OA\Property(property="last_name", type="string", example="zaeter",description="User last name"),
     *             @OA\Property(property="email", type="string", example="hzaeter@gmail.com",description="User email"),
     *             @OA\Property(property="mobile", type="string", example="0935917667",description="User mobile phone"),
     *             @OA\Property(property="address", type="string", example="midan",description="User address"),
     *             @OA\Property(property="password", type="string", example="password123",description="User password"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123",description="User confirmation password")
     *         )
     *       )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The activation code has been sent to your email"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="hasan zaeter"),
     *                 @OA\Property(property="email", type="string", example="hzaeter04@gmail.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *
     *        @OA\JsonContent(
     *
     *         @OA\Property(property="successful", type="boolean", example=false),
     *         @OA\Property(property="message", type="string", example="Validation failed"),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             example={}
     *         )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->userRepository->findByEmail($request->email);
        if ($user) {
            return ResponseHelper::jsonResponse([], 'The Email has already been taken', 400, false);
        }
        $user = $this->userRepository->create($request->validated());

        $this->EmailVerificationController->sendEmailVerification($user);

        return ResponseHelper::jsonResponse([], 'The activation code has been sent to your email', 201);
    }

    /**
     * @OA\Post(
     *     path="/api/users/login",
     *     summary="User login",
     *     tags={"Users"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *            @OA\Schema(
     *                 type="object",
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", example="hzaeter@gmail.com",description="User email"),
     *             @OA\Property(property="password", type="string", example="password1234",description="User password")
     *         )
     *       )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="jwt-token"),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="hasan"),
     *                     @OA\Property(property="last_name", type="string", example="zaeter"),
     *                     @OA\Property(property="email", type="string", example="hzaeter@gmail.com"),
     *                     @OA\Property(property="mobile", type="string", example="0935917557"),
     *                     @OA\Property(property="Address", type="string", example="median")
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials. Please check your email and password"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="email", type="string", example="Email does not exist in our records")
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Your email address is not verified"),
     *             @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $user = $this->userRepository->findByEmail($credentials['email']);
        if (! $user) {
            return ResponseHelper::jsonResponse([], 'Email not found. Please register or check your email', 404, false);
        }

        if (! $token = Auth::attempt($credentials)) {
            return ResponseHelper::jsonResponse([], 'Invalid password. Please check your password', 401, false);
        }

        $user = $this->userRepository->findByEmail($request->email);

        $data = [
            'token' => $token,
            'token_type' => 'bearer',
            'user' => new UserResource($user),
        ];

        return ResponseHelper::jsonResponse($data, 'Login successful');
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
     *             @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="Hasan"),
     *                 @OA\Property(property="last_name", type="string", example="Zaeter"),
     *                 @OA\Property(property="email", type="string", example="hzaeter04@gmail.com")
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        $user = $this->userRepository->findById(auth()->user()->id);
        auth()->logout();
        $data = ['user' => new UserResource($user)];

        return ResponseHelper::jsonResponse($data, 'Successfully logged out');
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
     *            @OA\Property(property="successful", type="boolean", example=true),
     *            @OA\Property(property="message",type="string",example="Profile retrieved successfully"),
     *            @OA\Property(property="id", type="integer", example=1),
     *            @OA\Property(property="first_name", type="string", example="Hasan"),
     *            @OA\Property(property="last_name", type="string", example="Zaeter"),
     *            @OA\Property(property="email", type="string", example="hzaeter@gmail.com"),
     *            @OA\Property(property="mobile", type="string", example="0935917667"),
     *            @OA\Property(property="Address", type="string", example="median"),
     *            @OA\Property(property="contacts", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="contact_type_id", type="integer", example=2)
     *             )),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(property="successful", type="boolean", example=false),
     *               @OA\Property(property="message", type="string", example="User Not Found"),
     *               @OA\Property(property="status_code", type="integer", example=404)
     *           )
     *     ),
     *
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized - Invalid or missing token",
     *
     *     @OA\JsonContent(
     *
     *               @OA\Property(property="successful", type="boolean", example=false),
     *               @OA\Property(property="message", type="string", example="Unauthenticated"),
     *               @OA\Property(property="status_code", type="integer", example=401)
     *           )
     *      )
     * )
     */
    public function profile(User $user)
    {

        $data = ['user' => new UserContactsResource($user->loadCount('contacts'))];

        return ResponseHelper::jsonResponse($data, 'Profile retrieved successfully');
    }

    public function storeFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $user = $this->userRepository->findById(auth()->user()->id);
        $this->userRepository->update($user, ['fcm_token' => $request->fcm_token]);

        return ResponseHelper::jsonResponse([], 'Fcm token stored successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/users/update",
     *     summary="Update user profile",
     *     tags={"Users"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *             @OA\Property(property="first_name", type="string", example="Hasan",description="User first name"),
     *             @OA\Property(property="last_name", type="string", example="Zaeter",description="User last name"),
     *             @OA\Property(property="mobile", type="string", example="0935917667",description="User mobile phone"),
     *             @OA\Property(property="address", type="string", example="median",description="User Address"),
     *             @OA\Property(property="old_password", type="string", example="oldpassword123",description="User old password"),
     *             @OA\Property(property="new_password", type="string", example="newpassword123",description="User new password"),
     *             @OA\Property(property="new_password_confirmation", type="string", example="newpassword123",description="User confirmation new password")
     *         )
     *        )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="Hasan"),
     *                 @OA\Property(property="last_name", type="string", example="Zaeter"),
     *                 @OA\Property(property="email", type="string", example="hzaeter@gmail.com"),
     *                 @OA\Property(property="address", type="string", example="median"),
     *                 @OA\Property(property="mobile", type="string", example="0935917667"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="contact_type_id", type="integer", example=2)
     *                 ))
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object", example={}),
     *             @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, invalid old password",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="successful", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to modify this profile"),
     *             @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, $user_id): JsonResponse
    {
        if (empty($request->all())) {
            return response()->json(['message' => 'there is no data to update'], 200);
        }
        $validationResponse = $this->validateRequest($request, $request->rules());
        if ($validationResponse) {
            return $validationResponse;
        }

        $user = $this->userRepository->findById($user_id);

        if ($request->filled('new_password')) {
            $request->merge(['password' => Hash::make($request->new_password)]);
        }
        $this->userRepository->update($user, $request->except(['old_password', 'new_password']));

        $data = ['user' => new UserContactsResource($user)];

        return ResponseHelper::jsonResponse($data, 'Profile updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/user/delete/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     security={{"bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="User ID you want to delete it",
     *
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *
     *    @OA\Response(
     *         response=403,
     *         description="forbidden error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="You are not authorized to delete this User.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function destroy()
    {
        $user = $this->userRepository->destroy();

        $data = ['user' => new UserContactsResource($user)];

        return ResponseHelper::jsonResponse($data, 'deleted Account successfully');
    }
}
