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
                'success' => false,
                'message' => 'Ada kesalahan',
                'data' => $validator->errors()
            ], 422);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['apikey'] = Str::random(60);  // Generate API key

        $user = User::create($input);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil registrasi',
            'data' => $user
        ], 201);
    }


    /**
     * Login User
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $apikey = Str::random(60);  // Generate API key
        $user->update([
            'apikey' => $apikey
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => $user
        ], 200);
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
