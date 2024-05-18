<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\AdminResourceCollection;
use App\Models\Administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdministratorController extends Controller
{
    public function getAllAdmins(Request $request)
    {
        if (!$request->user()->tokenCan('adminToken')) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $admins = Administrator::all();
        $result = $admins->map(function ($admin) {
            return [
                'username' => $admin->username,
                'last_login_at' => $admin->last_login_at ? $admin->last_login_at->format('Y-m-d H:i:s') : null,
                'created_at' => $admin->created_at ? $admin->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $admin->updated_at ? $admin->updated_at->format('Y-m-d H:i:s') : null
            ];
        });

        return response()->json([
            'totalElements' => $result->count(),
            'content' => $result
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Administrator $administrator)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Administrator $administrator)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Administrator $administrator)
    {
        //
    }
}
