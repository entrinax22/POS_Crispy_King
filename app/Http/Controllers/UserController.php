<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('users/Index');
    }

    public function list(Request $request)
    {
        $search = $request->input('search');
        $users = User::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->with('roles')
            ->paginate(10);
        
        $data = $users->getCollection()->map(function ($user) {
            return [
                'id' => encrypt($user->id),
                'name' => $user->name,
                'email' => $user->email,
                'roles' => [
                    'assigned' => $user->roles->map(function ($role) {
                        return [
                            'id' => encrypt($role->id),
                            'name' => $role->name,
                        ];
                    })->toArray(),
                ],
            ];
        });

        return response()->json([
            "result" => true,
            "data" => $data,
            "message" => 'Users retrieved successfully.',
            "pagination" => [
                "total" => $users->total(),
                "current_page" => $users->currentPage(),
                "last_page" => $users->lastPage(),
                "per_page" => $users->perPage(),
            ],
        ]);
    }

    
    public function store(Request $request)
    {
        if ($request->has('id')) {
            $id = decrypt($request->input('id'));
            $user = User::findOrFail($id);
            $user->name = $request->input('name');
            $roleId = collect($request->input('roles') ?? [])
                ->pluck('id')
                ->map(function ($id) {
                    return decrypt($id);
                })
                ->all();
            $user->syncRoles($roleId);
            $user->save();

            return response()->json([
                'result' => true,
                'message' => 'User updated successfully.',
            ]);
        } else {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'roles' => 'array',
            ]);

            $roleId = collect($validated['roles'] ?? [])
                ->pluck('id')
                ->map(function ($id) {
                    return decrypt($id);
                })
                ->all();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);
            $user->syncRoles($roleId);

            return response()->json([
                'result' => true,
                'message' => 'User created successfully.',
            ]);
        }
    }

    public function edit($encryptedId)
    {
        $id = decrypt($encryptedId);
        $user = User::with('roles')->findOrFail($id);

        $roles = Role::all();
        $encryptedRoles = $roles->mapWithKeys(function ($role) {
            return [$role->id => encrypt($role->id)];
        });

        return response()->json([
            'result' => true,
            'message' => 'User retrieved successfully.',
            'data' => [
                'id' => encrypt($user->id),
                'name' => $user->name,
                'email' => $user->email,
                'roles' => [
                    'assigned' => $user->roles->map(function ($role) use ($encryptedRoles) {
                        return [
                            'id' => $encryptedRoles[$role->id], 
                            'name' => $role->name,
                        ];
                    })->toArray(),
                    'available' => $roles->map(function ($role) use ($encryptedRoles) {
                        return [
                            'id' => $encryptedRoles[$role->id],
                            'name' => $role->name,
                        ];
                    })->toArray(),
                ]
            ],
        ]);
    }
    
    public function destroy($encryptedId)
    {
        $id = decrypt($encryptedId);
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
