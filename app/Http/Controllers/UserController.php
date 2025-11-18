<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Exception;

class UserController extends Controller
{
    /**
     * Get a list of all users for the admin panel.
     */
    public function index(): JsonResponse
    {
        try {
            // Fetch only the columns needed for the admin table
            $users = User::select('id', 'display_name', 'email', 'role', 'plan', 'status')
                         ->where('role', 'user') // Only get 'user' roles
                         ->get();

            return response()->json($users);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}