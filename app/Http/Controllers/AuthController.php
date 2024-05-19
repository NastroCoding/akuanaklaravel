<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signUp(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|unique:administrators,username|min:4|max:60',
            'password' => 'required|min:5'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->messages()
            ]);
        }

        $currentDate  = date('Y-m-d H:i:s');
        $hashed = Hash::make($request->password);

        $user = User::create([
            'username' => $request->username,
            'password' => $hashed,
            'last_login_at' => $currentDate
        ]);

        $token = $user->createToken('userToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token
        ], 201);
    }

    public function signIn(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:60',
            'password' => 'required|min:5'
        ]);
        
        if($validator->fails()){
            return response()->json([
                'message' => $validator->messages()
            ]);
        }
        
        $user = User::where('username', $request->username)->first();
        $admin = Administrator::where('username', $request->username)->first();
        
        if( $admin &&  Hash::check($request->password, $admin->password)){
            $token = $admin->createToken('adminToken')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token   
            ]);
        }
        if( $user &&  Hash::check($request->password, $user->password)){
            $tokenType = 'userToken';
            if (strpos($user->username, 'dev') !== false) {
                $tokenType = 'devToken';
            }
            $token = $user->createToken($tokenType)->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token
            ]);
        }

        return response()->json([
            'status' => 'invalid',
            'message' => 'Wrong username or password'
        ]);
        
    }
    
    public function signOut(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success'
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
    
        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'invalid',
                'message' => $validator->errors()->first()
            ], 400);
        }
    
        // Update only the fields provided in the request
        if ($request->has('username')) {
            $user->username = $request->input('username');
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }
    
        // Save the user
        $user->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully'
        ], 200);
    }
    
    

    public function deleteUser(Request $request, $id){
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

        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Delete user Successfully'
        ], 204);
    }
}