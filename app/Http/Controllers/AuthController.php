<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle a login request for an administrator.
     */
    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid input.'], 422);
        }

        $validated = $validator->validated();

        try {
            $admin = User::where('email', $validated['email'])
                ->where('role', 'admin')
                ->first();

            // REVERTED to your original plain-text password check
            if (!$admin || !($validated['password'] == $admin->password)) {
                return response()->json(['success' => false, 'message' => 'Invalid administrator credentials.'], 401);
            }

            // Create a token for the admin
            $token = $admin->createToken('admin-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'adminId' => $admin->id,
                'token' => $token // Send the token
            ]);

        } catch (\Exception $e) {
            // This catch block is what's being triggered
            return response()->json(['success' => false, 'message' => 'A server error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle a login request for a standard user.
     */
    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid input.'], 422);
        }

        $validated = $validator->validated();

        try {
            $user = User::where('email', $validated['email'])
                ->where('role', 'user')
                ->first();

            // REVERTED to your original plain-text password check
            if (!$user || !($validated['password'] == $user->password)) {
                return response()->json(['success' => false, 'message' => 'Invalid user credentials.'], 401);
            }

            // Create a token for the user
            $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'userId' => $user->id,
                'status' => $user->status,
                'token' => $token // Send the token
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'A server error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle a registration request for a new user.
     * This uses your original bcrypt logic.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'display_name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Using your original bcrypt
            'role' => 'user',
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }
}