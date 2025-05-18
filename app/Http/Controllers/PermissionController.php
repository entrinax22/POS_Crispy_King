<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        return inertia('permissions/Index');
    }

    public function list(Request $request)
    {
        $search = $request->input('search');
        $permissions = Permission::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        $data = $permissions->getCollection()->map(function ($permission) {
            return [
                'id' => encrypt($permission->id),
                'name' => $permission->name,
            ];
        });

        return response()->json([
            "result" => true,
            "data" => $data,
            "message" => 'Permissions retrieved successfully.',
            "pagination" => [
                "total" => $permissions->total(),
                "current_page" => $permissions->currentPage(),
                "last_page" => $permissions->lastPage(),
                "per_page" => $permissions->perPage(),
            ],
        ]);
    }

    public function edit($encryptedId)
    {
        $id = decrypt($encryptedId);
        $permission = Permission::findOrFail($id);

        return response()->json([
            'result' => true,
            'data' => [
                'id' => encrypt($permission->id),
                'name' => $permission->name,
            ],
            'message' => 'Permission retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('id')) {
            $id = decrypt($request->input('id'));
            $permission = Permission::findOrFail($id);
            $permission->name = $request->input('name');
            $permission->save();

            return response()->json([
                'result' => true,
                'message' => 'Permission updated successfully.',
            ]);
        } else {

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

    public function destroy($encryptedId)
    {
        $id = decrypt($encryptedId);
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'result' => true,
            'message' => 'Permission deleted successfully.',
        ]);
    }
}
