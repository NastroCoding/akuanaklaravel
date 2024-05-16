<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::all();
        return response()->json([
            'totalElements' => $user->count(),
            'content' => UserResource::collection($user)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|unique:administrators,username|min:4|max:60',
            'password' => 'required|min:5'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->messages()
            ]);
        }

        $hashed = Hash::make($request->password);

        $user = User::create([
            'username' => $request->username,
            'password' => $hashed,
        ]);

        return response()->json([
            'status' => 'success',
            'username' => $request->username
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:60|unique:administrators,username|unique:users,username,' . $id,
            'password' => 'required|min:5'
        ]);

        if($validator->fails()){
           $unique = $validator->failed();
           if(isset($unique['username']['Unique'])){
                return response()->json([
                    'status' => 'invalid',
                    'message' => 'Username already exists'
                ]);
           }
        }

        $user = User::where('id', $request->id)->first();
        $hashed = Hash::make($request->password);

        $user->update([
            'username' => $request->username,
            'password' => $hashed
        ]);

        return response()->json([
            'status' => 'success',
            'username' => $request->username
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if($user == null){
            return response()->json([
                'status' => 'not-found',
                'message' => 'User Not Found'
            ]);
        }

        $user->delete();
    }
}
