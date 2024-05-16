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
            $token = $user->createToken('userToken')->plainTextToken;
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
        
    }
}
