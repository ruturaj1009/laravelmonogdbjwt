<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'phone' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            );

            User::create($user);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!$token = auth()->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => false,
                    'message' => "Unauthorized Access",
                ], 400);
            }
            return $this->generateToken($token);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateToken($token)
    {
        try {
            $user = auth()->user();

            $customClaims = [
                'custom_claim' => 'some data'
            ];

            $token = JWTAuth::fromUser($user, $customClaims);

            $ttl = Auth::factory()->getTTL() / 3600;

            return response()->json([
                'status' => true,
                'message' => "User Logged in Successfully",
                'token' => $token,
                'expiresIn' => $ttl . 'hr',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json([
                'status' => true,
                'message' => "User Logged out Successfully",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile()
    {
        try {
            if (auth()->check()) {
                $user = auth()->user();
                return response()->json([
                    'status' => true,
                    'data' => $user,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access. Kindly login...',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allusers()
    {
        try {
            $users = User::all();

            if ($users->count() == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'No users found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function singleuser(string $id)
    {
        try {
            if (empty($id)) {
                return response()->json([
                    'status' => false,
                    'message' => "User ID is required",
                ], 404);
            }
            $user = User::findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "User not found",
            ], 404);
        }
    }
}
