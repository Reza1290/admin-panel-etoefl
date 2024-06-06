<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotMail;
use App\Mail\RegisterOtpMail;
use App\Models\User;
use App\Models\UserFriend;
use App\Models\UserScorer;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use illuminate\Support\Str;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgot', 'wellknownAssetLink']]);
    }

    /**
     * @OA\Post(
     * path="/login",
     * summary="Login",
     * description="Login enpoint",
     * operationId="authLogin",
     * tags={"Auth"},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"email","password"},
     *              @OA\Property(property="email",type="text"),
     *              @OA\Property(property="password",type="password"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Login Successfully",
     *         
     *     ),
     * )
     */

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 422);
        }

        try {
            $userCheck = User::where('email', $request->email)->first();
            if (!$userCheck) {
                return response()->json([
                    'success' => false,
                    'message' => 'not registered'
                ]);
            }

            $credentials = $request->only('email', 'password');


            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'email or password not registered',
                ], 200);
            }

            $mappedDataUser = [
                'id' => $userCheck ? $userCheck->_id : null,
                'name' => $userCheck ? $userCheck->name : null,
                'email' => $userCheck ? $userCheck->email : null,
                'is_verified_register' => $userCheck ? $userCheck->is_verified_register : null,
                'email_verified_at' => $userCheck ? $userCheck->email_verified_at : null,
            ];

            if ($userCheck['is_verified_register'] == false) {

                $validTokenRegister = rand(1000, 9999);

                $now = now();  // Waktu saat ini
                $expiredAt = $now->copy()->addHour()->toDateTimeString();

                // send new otp email notification
                $get_user_email = $userCheck['email'];
                $get_user_name = $userCheck['name'];
                Mail::to($userCheck['email'])->send(new RegisterOtpMail($get_user_email, $get_user_name, $validTokenRegister));

                $updateOtp = $userCheck->update([
                    'otp_register' => $validTokenRegister,
                    'otp_register_expired_at' => $expiredAt
                ]);

                if ($updateOtp) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Please verify your email first before login, check your email for verification',
                        'data' => $mappedDataUser, // lek false (column isverified e) ngarah ke kode otp, ws dikirim ho
                        'token' => $token
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'failed to send new otp, call admin pentol.verify@gmail.com'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User login successfully',
                'data' => $mappedDataUser,
                'token' => $token
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/register",
     * summary="Register",
     * description="Register enpoint",
     * operationId="authRegister",
     * tags={"Auth"},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"name","email","password"},
     *              @OA\Property(property="name",type="text"),
     *              @OA\Property(property="email",type="text"),
     *              @OA\Property(property="password",type="password"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Register Successfully",
     *         
     *     ),
     * )
     */

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    $pattern = '/@([a-z0-9\-]+\.)*pens\.ac\.id$/i';
                    if (!preg_match($pattern, $value)) {
                        $fail('The ' . $attribute . ' must be a valid email address with a domain ending in .pens.ac.id');
                    }
                },
            ],
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }



        try {
            $validTokenRegister = rand(1000, 9999);

            $now = now();  // Waktu saat ini
            $expiredAt = $now->copy()->addHour()->toDateTimeString();

            $requestEmailCheck = User::where('email', $request->email)->first();
            if ($requestEmailCheck) {
                return response()->json([
                    'success' => false,
                    'message' => 'email already digae'
                ], 200);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp_register' => $validTokenRegister,
                'otp_register_expired_at' => $expiredAt,
                'otp_forgot' => null,
                'otp_forgot_expired_at' => null,
                'is_verified_register' => false,
                'is_verified_forgot' => false,
                'target_id' => "",
                'email_verified_at' => null,

            ]);

            $token = Auth::guard('api')->login($user);

            // send email notification
            $get_user_email = $user['email'];
            $get_user_name = $user['name'];
            Mail::to($user['email'])->send(new RegisterOtpMail($get_user_email, $get_user_name, $validTokenRegister));

            $mappedDataUser = [
                'name' => $user ? $user->name : null,
                'email' => $user ? $user->email : null,
                'is_verified_register' => $user ? $user->is_verified_register : null,
                'otp_register' => $user ? $user->otp_register : null,
                'otp_register_expired_at' => $user ? $user->otp_register_expired_at : null,
            ];

            return response()->json([
                'success' => true,
                'message' => 'User created successfully, check your email for verification',
                'data' => $mappedDataUser,
                'token' => $token
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // make swagger VerifyOtpRegister
    /**
     * @OA\Post(
     * path="/users/verify-otp",
     * summary="Verify Otp Register",
     * description="Verify Otp Register enpoint",
     * operationId="authVerifyOtpRegister",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"otp_register"},
     *              @OA\Property(property="otp_register",type="text"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Verify Otp Register Successfully",
     *         
     *     ),
     * )
     */


    public function verifyOtpRegister(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'otp_register' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 422);
        }

        try {

            $initUser = User::where('_id', auth()->user()->_id)->first();
            $theToken = $initUser->otp_register;

            if ($request->otp_register != $theToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'the otp not correct, try again check your email addreses'
                ]);
            }

            // init date
            $now = now();
            $nowInitString = $now->toDateTimeString();


            if ($initUser->otp_register_expired_at < $nowInitString) {
                return response()->json([
                    'success' => false,
                    'message' => 'the otp has expired, click the button to try again otp and check your email addreses'
                ]);
            }

            User::where('_id', auth()->user()->_id)->update([
                'is_verified_register' => true,
                'email_verified_at' => $nowInitString,
                // 'email_verified_at' => now()
            ]);

            $user = User::where('_id', auth()->user()->_id)->first();

            $mappedDataUser = [
                'name' => $user ? $user->name : null,
                'email' => $user ? $user->email : null,
                'is_verified_register' => $user ? $user->is_verified_register : null,
                'email_verified_at' => $user ? $user->email_verified_at : null,
            ];

            return response()->json([
                'success' => true,
                'message' => 'The otp has verified',
                'data' => $mappedDataUser
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/users/new-otp",
     * summary="Generate New OTP",
     * description="Generate new OTP endpoint",
     * operationId="authGenerateNewOtp",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\Response(
     *         response="200",
     *         description="New OTP generated successfully",
     *         
     *     ),
     * )
     */

    public function newOtp()
    {
        $user = User::where('_id', auth()->user()->id)->first();

        try {

            $now = now();
            $expiredAt = $now->copy()->addHour()->toDateTimeString();

            $validTokenRegister = rand(1000, 9999);

            // send new otp email notification
            $get_user_email = $user['email'];
            $get_user_name = $user['name'];
            Mail::to($user['email'])->send(new RegisterOtpMail($get_user_email, $get_user_name, $validTokenRegister));

            $updateOtp = $user->update([
                'otp_register' => $validTokenRegister,
                'otp_register_expired_at' => $expiredAt

            ]);

            if ($updateOtp) {
                return response()->json([
                    'success' => true,
                    'message' => 'new otp has been sent to your email'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'failed to send new otp'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // ------------------------------------------------------------------------------ //

    // MAKE THE SWAGGER FORGOT
    /**
     * @OA\Post(
     * path="/forgot",
     * summary="Forgot Password",
     * description="Forgot Password enpoint",
     * operationId="authForgot",
     * tags={"Auth"},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"email"},
     *              @OA\Property(property="email",type="text"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Forgot Password Successfully",
     *         
     *     ),
     * )
     */


    public function forgot(Request $request)
    {
        $validate = Validator::make(request()->all(), [
            'email' => 'required|email'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors()
            ], 422);
        }

        try {

            $now = now();
            $expiredAt = $now->copy()->addHour()->toDateTimeString();

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found'
                ]);
            }

            // create token jwt auth bisa tidak login
            $token = JWTAuth::fromUser($user);


            $validTokenForgot = rand(1000, 9999);

            $user->update([
                'otp_forgot' => $validTokenForgot,
                'otp_forgot_expired_at' => $expiredAt
            ]);

            // send email notification
            $get_user_email = $user['email'];
            $get_user_name = $user['name'];
            Mail::to($user['email'])->send(new ForgotMail($get_user_email, $get_user_name, $validTokenForgot));

            return response()->json([
                'success' => true,
                'message' => 'Check your email for verification code',
                'data' => [
                    'token' => $token,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // MAKE THE SWAGGER VERIFY OTP FORGOT
    /**
     * @OA\Post(
     * path="/users/verify-otp-forgot",
     * summary="Verify Otp Forgot",
     * description="Verify Otp Forgot enpoint",
     * operationId="authVerifyOtpForgot",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"otp_forgot"},
     *              @OA\Property(property="otp_forgot",type="text"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Verify Otp Forgot Successfully",
     *         
     *     ),
     * )
     */


    public function verifyOtpForgot(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'otp_forgot' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 422);
        }

        try {
            $initUser = User::where('_id', auth()->user()->id)->first();
            $theToken = $initUser->otp_forgot;

            if ($request->otp_forgot != $theToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'the otp not correct, try again check your email addreses'
                ]);
            }

            $now = now();
            $nowInitString = $now->toDateTimeString();

            if ($initUser->otp_forgot_expired_at < $nowInitString) {
                return response()->json([
                    'success' => false,
                    'message' => 'the otp has expired, click the button to try again otp and check your email addreses'
                ]);
            }

            $successVerify = User::where('_id', auth()->user()->id)->update([
                'is_verified_forgot' => true,
            ]);

            $user = User::where('_id', auth()->user()->id)->first();

            $mappedDataUser = [
                'name' => $user ? $user->name : null,
                'email' => $user ? $user->email : null,
                'is_verified_forgot' => $user ? $user->is_verified_forgot : null,
            ];

            if ($successVerify) {
                return response()->json([
                    'success' => true,
                    'message' => 'The otp has verified',
                    'data' => $mappedDataUser
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The otp has not verified',
                ]);
            }
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    // the point changes password service
    // make the swagger reset
    /**
     * @OA\Post(
     * path="/reset",
     * summary="Reset Password",
     * description="Reset Password enpoint",
     * operationId="authReset",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"password","confirm_password"},
     *              @OA\Property(property="password",type="password"),
     *              @OA\Property(property="confirm_password",type="password"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Reset Password Successfully",
     *         
     *     ),
     * )
     */

    public function reset(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 422);
        }

        try {
            $user = User::where('_id', auth()->user()->_id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }


            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password cannot be the same as the previous password'
                ]);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'is_verified_forgot' => false,
                'otp_forgot' => null,
                'otp_forgot_expired_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password has been changed, try again login in new password'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ----------------------------------- SERVICE FACE ID ---------------------------------//

    // make the swagger checkPassword
    /**
     * @OA\Post(
     * path="/check/password",
     * summary="Check Password",
     * description="Check Password enpoint",
     * operationId="authCheckPassword",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"password"},
     *              @OA\Property(property="password",type="password"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Check Password Successfully",
     *         
     *     ),
     * )
     */


    public function checkPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'password' => 'required|string|min:6'

        ]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 422);
        }

        try {
            $user = User::where('_id', auth()->user()->_id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }


            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password is correct',
                    'data' => [
                        'password' => true
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is incorrect',
                    'data' => [
                        'password' => false
                    ]
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // make the swagger changePassword
    /**
     * @OA\Post(
     * path="/change/password",
     * summary="Change Password",
     * description="Change Password enpoint",
     * operationId="authChangePassword",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"password","confirm_password"},
     *              @OA\Property(property="password",type="password"),
     *              @OA\Property(property="confirm_password",type="password"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Change Password Successfully",
     *         
     *     ),
     * )
     */

    public function changePassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 422);
        }

        try {
            $user = User::where('_id', auth()->user()->_id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password cannot be the same as the previous password'
                ]);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password has been changed'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // ------------------------------------------------------------------------------------- //

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * @OA\Get(
     * path="/users/profile",
     * summary="Profile",
     * description="Profile enpoint",
     * operationId="authProfile",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\Response(
     *         response="200",
     *         description="User Profile Successfully",
     *         
     *     ),
     * )
     */

    public function profile()
    {
        $user = auth()->user()->_id;
        $userInit = User::with('target')->where('_id', $user)->first();
        $scoreUserLatest = UserScorer::where('user_id', $user)->latest()->first();
        if ($scoreUserLatest == null) {
            return response()->json([
                'success' => true,
                'message' => 'level and score are not available yet. (belum isi level dan test)',
                'data' => [
                    'id' => $userInit->_id,
                    'level' => "",
                    'current_score' => 0,
                    'target_score' => $userInit->target ? $userInit->target->score_target : 0,
                    'rank' => 0,
                    'name_user' => $userInit->name,
                    'email_user' => $userInit->email,
                    'profile_image' => $userInit ? $userInit->profile : "",
                ]
            ], 201);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Profile User has completed',
            'data' => [
                'id' => $userInit->_id,
                'level' => $scoreUserLatest->level_profiency,
                'current_score' => $scoreUserLatest->score_toefl,
                'target_score' => $userInit->target ? $userInit->target->score_target : 0,
                'rank' => 0,
                'name_user' => $userInit->name,
                'email_user' => $userInit->email,
                'profile_image' => $userInit ? $userInit->profile : "",
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/search/friend",
     * summary="Search Friend",
     * description="Search Friend enpoint",
     * operationId="authSearchFriend",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              required={"name"},
     *              @OA\Property(property="name",type="text"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Search Friend Successfully",
     *         
     *     ),
     * )
     */

    public function searchFriend(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 422);
        }

        $user = User::where('name', 'LIKE', '%' . $request->name . '%')->where('_id','!=',auth()->user()->_id)->get();

        if ($user->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        $mappedData = [];
        foreach ($user as $users) {
            $mappedData[] = [
                'id' => $users->_id,
                'profile_image' => $users ? $users->profile : "",
                'name' => $users->name,
            ];
        }


        return response()->json([
            'success' => true,
            'message' => 'User found',
            'data' => $mappedData
        ]);
    }

    // swagger spesifyFriend and add parameter
    /**
     * @OA\Get(
     * path="/spesify-user/{id}",
     * summary="Spesify Friend",
     * description="Spesify Friend enpoint",
     * operationId="authSpesifyFriend",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\Response(
     *         response="200",
     *         description="User Spesify Friend Successfully",
     *         
     *     ),
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="masukkan id user",
     *         required=true,
     *      ),
     * )
     */

    public function spesifyFriend($id)
    {
        try {
            $userInit = User::with('target')->where('_id', $id)->first();
            if (!$userInit) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            $scoreUserLatest = UserScorer::where('user_id', $userInit->_id)->latest()->first();
            if ($userInit->_id == auth()->user()->_id) {
                return response()->json([
                    'success' => true,
                    'message' => 'u lihat profil lu sendiri tol, nembak o api profile ',
                ]);
            }

            $is_friend = UserFriend::where('user_id', auth()->user()->_id)->where('friend_id', $userInit->_id)->first();

            return response()->json([
                'success' => true,
                'message' => 'data user aman cuy spesifik e, is friend true yo button e unfriend (nembak api frienf/add-patch), false brti addfriend sama nembak e mek',
                'data' => [
                    'id' => $userInit->_id,
                    'level' => $scoreUserLatest ? $scoreUserLatest->level_profiency : "",
                    'current_score' => $scoreUserLatest ? $scoreUserLatest->score_toefl : 0,
                    'rank' => 0,
                    'name_user' => $userInit->name,
                    'email_user' => $userInit->email,
                    'profile_image' => $userInit ? $userInit->profile : "",
                    'is_friend' => $is_friend ? true : false
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // swagger addFriend
    /**
     * @OA\Post(
     * path="/friend/process/add-patch/{id}",
     * summary="Add Friend",
     * description="Add Friend enpoint",
     * operationId="authAddFriend",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\Response(
     *         response="200",
     *         description="User Add Friend Successfully",
     *         
     *     ),
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="masukkan id user",
     *         required=true,
     *    ),
     * )
     */

    public function addFriend($idFriend)
    {
        try {
            $friend = User::find($idFriend);
            if (!$friend) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            $userLogged = auth()->user();

            $isFriend = UserFriend::where('user_id', $userLogged->_id)->where('friend_id', $idFriend)->first();

            if ($isFriend) {
                UserFriend::where('user_id', $userLogged->_id)->where('friend_id', $idFriend)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Friend deleted, button rubah add friend njeng',
                    'data' => [
                        'is_friend' => false
                    ]
                ]);
            }

            UserFriend::create([
                'user_id' => $userLogged->_id,
                'friend_id' => $idFriend
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Friend added, button rubah unfriend njeng',
                'data' => [
                    'is_friend' => true
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // buatkan sya swagger getAllFriends 
    /**
     * @OA\Get(
     * path="/get-all/friends",
     * summary="Get All Friends",
     * description="Get All Friends enpoint",
     * operationId="authGetAllFriends",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\Response(
     *         response="200",
     *         description="User Get All Friends Successfully",
     *         
     *     ),
     * )
     */


    public function getAllFriends()
    {
        $user = auth()->user();
        $friends = UserFriend::where('user_id', $user->_id)->get();
        $friendList = [];
        foreach ($friends as $friend) {
            $friendList[] = User::find($friend->friend_id);
        }

        $mappedData = [];
        foreach ($friendList as $friend) {
            $scoreUserLatest = UserScorer::where('user_id', $friend->_id)->latest()->first();
            $mappedData[] = [
                'id' => $friend->_id,
                'level' => $scoreUserLatest ? $scoreUserLatest->level_profiency : "",
                'current_score' => $scoreUserLatest ? $scoreUserLatest->score_toefl : 0,
                'rank' => 0,
                'name_user' => $friend->name,
                'email_user' => $friend->email,
                'profile_image' => $friend ? $friend->profile : "",
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Friend list get, ngko delok spesifik profile e nembak api spesify profile',
            'data' => $mappedData
        ]);
    }

    // buatkan sya swagger editProfile 
    /**
     * @OA\PATCH(
     * path="/edit/profile",
     * summary="Edit Profile",
     * description="Edit Profile enpoint",
     * operationId="authEditProfile",
     * tags={"Auth"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *      @OA\JsonContent(),
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(property="name",type="text"),
     *              @OA\Property(property="profile_image",type="file"),
     *          ),
     *         ),
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="User Edit Profile Successfully",
     *         
     *     ),
     * )
     */

    public function editProfile(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // 'profile_image' => '',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 422);
        }

        try {
            $user = User::where('_id', auth()->user()->_id)->first();

            $profileImage = $user->profile ? $user->profile : null;

            if ($request->hasFile('profile_image')) {
                // $profile = time() . '_' . Str::random(10) . '.' . $request->file('profile_image')->getClientOriginalExtension();
                $file = $request->file('profile_image');
                $filePath = Storage::cloud()->put('/user_photo', $file);
                // $filePath = $request->file('profile_image')->storeAs('user_photo', $profile, 'public');
            }

            $user->update([
                'name' => $request->name,
                'profile' => $request->hasFile('profile_image') ? $filePath : $profileImage,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'name' => $user->name,
                    'profile_image' => $user->profile,
                    'titit' => $request,
                    'aw' => $request->hasFile('profile_image')
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage(),
            ]);
        }
    }

    public function wellknownAssetLink()
    {
        $data = public_path('data/links.json');
        $data = json_decode(file_get_contents($data), true);
        return response()->json($data);
    }
}
