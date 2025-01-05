<?php

namespace App\Http\Services;

use App\Models\User;
use Exception;

class UserService
{
    public function getAllUsers()
    {
        try {
            $users = User::all();
            if ($users->isEmpty()) {
                return response()->json(['message' => 'No users found.'], 404);
            }

            return $users;
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong while fetching users.'], 500);
        }
    }

    public function updateUser($data, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validatedData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
            ];

            $user->update($validatedData);

            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong, while updating the user.'], 404);
        }
    }
}
