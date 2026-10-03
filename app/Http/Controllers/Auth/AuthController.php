<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request){

        $user = User::where('email', $request['email'])->first();

        if (!$user || !Hash::check($request['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
                'data' => null,
            ], 401);
        }

         $token = $user->createToken('auth_token')->plainTextToken;

            return response()
            ->json([
                'success' => true,
                'message' => 'Login successful.',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',

                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'gym_id' => $user->gym_id,
                    ],
                ],
            ]);

    }

    public function logout(Request $request){


     $request->user()->currentAccessToken()->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Logged out '
    ]);
    }
}
