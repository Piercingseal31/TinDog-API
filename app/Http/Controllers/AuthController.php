<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            $admin = DB::table('users')
                ->where('email', $validated['email'])
                ->where('role', 'admin')
                ->first();

            if (!$admin || $admin->password !== $validated['password']) {
                return response()->json(['success' => false, 'message' => 'Invalid administrator credentials.'], 401);
            }

            return response()->json([
                'success' => true,
                'adminId' => $admin->id
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'A server error occurred.'], 500);
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
            $user = DB::table('users')
                ->where('email', $validated['email'])
                ->where('role', 'user')
                ->first();

            if (!$user || $user->password !== $validated['password']) {
                return response()->json(['success' => false, 'message' => 'Invalid user credentials.'], 401);
            }

            return response()->json([
                'success' => true,
                'userId' => $user->id,
                'status' => $user->status,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'A server error occurred.'], 500);
        }
    }

        // This is for authetication when registering for user -kirk
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
            'password' => bcrypt($request->password),
            'role' => 'user',
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }



}