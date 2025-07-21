<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\SaasLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SystemUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::whereHas('employee', function ($query) {
            $query->where('type', 'system');
        })
            ->with('employee')
            ->paginate(10);

        return response()->json([
            'users' => $user->items(),
            'pagination' => [
                'total' => $user->total(),
                'count' => $user->count(),
                'per_page' => $user->perPage(),
                'current_page' => $user->currentPage(),
                'last_page' => $user->lastPage(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
        ]);

        $user->employee()->create([
            'user_id' => $user->id,
            'gym_id' => Auth::user()->gym->id ?? Auth::user()->employee->gym_id,
            'type' => 'system',
        ]);

        $role = Role::findOrFail($request->role_id);

        $user->assignRole($role);

        SaasLog::create([
            'model_type' => User::class,
            'model_id' => $user->employee->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'changes' => json_encode($user),
        ]);

        return response()->json(['message' => 'تم تسجيل الموظف بنجاح'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::where('id', $id)
            ->whereHas('employee', function ($query) {
                $query->where('type', 'system');
            })
            ->with('employee')
            ->firstOrFail(); // ✅ uses all conditions safely

        return response()->json(['user' => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:15',
            'role_id' => 'sometimes|exists:roles,id',

            // Password validation
            'old_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed', // Requires `password_confirmation`
        ]);

        // Validate old password if user wants to update password
        if ($request->filled('password')) {
            if (!$request->filled('old_password') || !Hash::check($request->old_password, $user->password)) {
                return response()->json(['message' => 'كلمة المرور القديمة غير صحيحة'], 422);
            }

            $user->password = bcrypt($request->password);
        }

        // Update user fields
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;
        $user->save();

        if ($request->filled('role_id')) {
            $role = Role::findOrFail($request->role_id);
            $user->assignRole($role);
        }

        // Log the changes
        SaasLog::create([
            'model_type' => User::class,
            'model_id' => $user->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'changes' => json_encode($user->getChanges()),
        ]);

        return response()->json(['message' => 'تم تحديث الموظف بنجاح'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('id', $id)
            ->whereHas('employee', function ($query) {
                $query->where('type', 'system');
            })
            ->firstOrFail();


        $employeeData = $user;

        $user->delete();

        SaasLog::create([
            'model_type' => User::class,
            'model_id' => $employeeData->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'changes' => json_encode($employeeData),
        ]);

        return response()->json(['message' => 'تم حذف الموظف بنجاح'], 200);
    }

    public function roles()
    {
        $roles = Role::where('guard_name', 'web-system')->select('id', 'name')->get();
        return response()->json([
            'roles' => $roles,
        ], 200);
    }
}
