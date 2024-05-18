<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {
        $users = User::all();
        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection($users)
        ], 200);
    }

    public function createUser(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|min:4|max:60',
            'password' => 'required|min:5|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'invalid',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => 'success',
            'username' => $user->username
        ], 201);
    }

    public function updateUser(Request $request, $id) {
        if (!$request->user()->tokenCan('adminToken')) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }
    
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User not found'
            ], 404);
        }
    
        $rules = [];
        $messages = [];
    
        if ($request->has('username')) {
            $rules['username'] = 'required|unique:users,username,' . $id . '|min:4|max:60';
            $messages['username.required'] = 'The username field is required when updating the username.';
        }
        if ($request->has('password')) {
            $rules['password'] = 'required|min:5|max:10';
            $messages['password.required'] = 'The password field is required when updating the password.';
        }
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'invalid',
                'message' => $validator->errors()->first()
            ], 400);
        }
    
        if ($request->has('username')) {
            $user->username = $request->input('username');
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }
    
        $user->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully'
        ], 200);
    }
    
    public function showUser($username) {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 200);
    }

    public function deleteUser(Request $request, $id)
    {
        if (!$request->user()->tokenCan('adminToken')) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not administrator'
            ], 403);
        }

        try {
            $user = User::findOrFail($id);

            $user->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User not found'
            ], 404);
        }
    }
}
