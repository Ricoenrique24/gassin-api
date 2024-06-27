<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Validator;
use Auth;

class AuthController extends Controller
{

    /**
     * register user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'role' => 'required|string|in:employee,manager',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors(),
            ]);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['apikey'] = Str::random(60);  // Generate API key

        $user = User::create($input);

        return response()->json([
            'error' => false,
            'message' => 'User Created',
        ]);
    }


    /**
     * Login User
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
            'token_fcm' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors(),
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $apikey = Str::random(60);  // Generate API key
        $fcmToken = $request->token_fcm;
        $user->update([
            'apikey' => $apikey,
            'token_fcm' => $fcmToken
        ]);

        return response()->json([
            'error' => false,
            'message' => 'success',
            'loginResult' => $user
        ]);
    }


    /**
     * logout User
     */
    public function logout(Request $request)
    {
        // Untuk API key, kita tidak memerlukan tindakan logout
        return response()->json(['message' => 'Logged out'], 200);
    }

}
