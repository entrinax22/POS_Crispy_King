<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use function PHPUnit\Framework\isEmpty;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('roles/Index', [
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all(),
        ]);
    }

    public function list(Request $request)
    {
        $search = $request->input('search');
        $roles = Role::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->with('permissions')
            ->paginate(10);
        
        $data = $roles->getCollection()->map(function ($role) {
            return [
                'id' => encrypt($role->id),
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ];
        });

        return response()->json([
            "result" => true,
            "data" => $data,
            "message" => 'Roles retrieved successfully.',
            "pagination" => [
                "total" => $roles->total(),
                "current_page" => $roles->currentPage(),
                "last_page" => $roles->lastPage(),
                "per_page" => $roles->perPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('id')) {
            $id = decrypt($request->input('id'));
            $role = Role::findOrFail($id);
            $role->name = $request->input('name');
            $permissionIds = collect($request->input('permissions') ?? [])
                ->pluck('id')
                ->map(function ($id) {
                    return decrypt($id);
                })
                ->all();
            $role->syncPermissions($permissionIds);
            $role->save();

            return response()->json([
                'result' => true,
                'message' => 'Role updated successfully.',
            ]);
        } else {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'permissions' => 'array',
            ]);

            $permissionIds = collect($validated['permissions'] ?? [])
                ->pluck('id')
                ->map(function ($id) {
                    return decrypt($id);
                })
                ->all();

            $role = Role::create(['name' => $validated['name']]);
            $role->syncPermissions($permissionIds);

            return response()->json([
                'result' => true,
                'message' => 'Role created successfully.',
            ]);
        }
    }

    public function edit($encryptedId)
    {
        $id = decrypt($encryptedId);
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();

        return response()->json([
            'result' => true,
            'message' => 'Role retrieved successfully.',
            'data' => [
                'id' => encrypt($role->id),
                'name' => $role->name,
                'permissions' => $permissions->map(function ($permission) use ($role) {
                    return [
                        'id' => encrypt($permission->id),
                        'name' => $permission->name,
                        'assigned' => $role->hasPermissionTo($permission),
                    ];
                }),
            ],

        ]);
    }


    public function destroy($encryptedId)
    {
        $id = decrypt($encryptedId);
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
