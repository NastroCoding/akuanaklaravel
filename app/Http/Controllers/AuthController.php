<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signUp(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|min:4|max:60',
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

        $token = $user->createToken('login')->plainTextToken;

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
        
        if(! $user || ! Hash::check($request->password, $user->password)){
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong username or password'
            ]);
        }

        $token = $user->createToken('login')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function signOut(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
