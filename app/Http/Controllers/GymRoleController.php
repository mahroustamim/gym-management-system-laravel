<?php

namespace App\Http\Controllers;

use App\Models\GymLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GymRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::where('guard_name', 'web-gym')->paginate(10);

        return response()->json([
            'data' => $roles->items(),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web-gym',
        ]);

        if ($request->filled('permissions')) {
            $permissions = Permission::where('guard_name', 'web-gym')->whereIn('id', $request->permissions)->pluck('name');
            $role->givePermissionTo($permissions);
        }

        GymLog::create([
            'gym_id' => Auth::user()->gym->id ?? Auth::user()->employee->gym_id ?? null,
            'model_type' => Role::class,
            'model_id' => $role->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'changes' => json_encode([
                'name' => $role->name,
                'permissions' => $request->permissions
            ]),
        ]);

        return response()->json(['message' => 'تم تسجيل صلاحية الموظف بنجاح'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if ($role->guard_name === 'web') {
            return response()->json(['message' => 'لا يمكن عرض صلاحية النظام أو صلاحية مدير الصالة'], 403);
        }

        return response()->json([
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'), // returns array of names
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->guard_name === 'web') {
            return response()->json(['message' => 'لا يمكن تعديل صلاحية النظام أو صلاحية مدير الصالة'], 403);
        }

        $fields = $request->validate([
            'name' => 'sometimes|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array'
        ]);

        $original = $role->getOriginal();

        if (isset($fields['name'])) {
            $role->name = $fields['name'];
        }

        $role->save();

        if ($request->filled('permissions')) {
            $permissions = Permission::where('guard_name', 'web-gym')->whereIn('id', $request->permissions)->pluck('name');
            $role->syncPermissions($permissions);
        }

        $current = $role->getAttributes();
        $changes = array_diff_assoc($current, $original);

        if (!empty($changes)) {
            GymLog::create([
                'gym_id' => Auth::user()->gym->id ?? Auth::user()->employee->gym_id ?? null,
                'model_type' => Role::class,
                'model_id' => $role->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'changes' => json_encode($changes),
            ]);
        }

        return response()->json(['message' => 'تم تعديل الصلاحية بنجاح'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->guard_name === 'web') {
            return response()->json(['message' => 'لا يمكن حذف صلاحية النظام أو صلاحية مدير الصالة'], 403);
        }

        $roleData = $role;
        $role->delete();

        GymLog::create([
            'gym_id' => Auth::user()->gym->id ?? Auth::user()->employee->gym_id ?? null,
            'model_type' => Role::class,
            'model_id' => $roleData->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'changes' => json_encode($roleData),
        ]);

        return response()->json(['message' => 'تم حذف صلاحية الموظف بنجاح'], 200);
    }

    public function permissions()
    {
        $permissions = Permission::where('guard_name', 'web-gym')->get();
        return response()->json(['permissions' => $permissions], 200);
    }
}
