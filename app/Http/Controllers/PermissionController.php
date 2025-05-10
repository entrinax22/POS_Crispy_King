<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function list(Request $request){
        
        $permissions = Permission::all()->map(function ($permission) {
            return [
                'id' => encrypt($permission->id),
                'name' => $permission->name,
            ];
        });

        return response()->json([
            "result" => true,
            "data" => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name']]);

        return response()->json([
            'result' => true,
            'message' => 'Permission created successfully.',
        ]);
    }
}
