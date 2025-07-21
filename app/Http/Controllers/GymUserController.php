<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\GymLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GymUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;

        $users = User::whereHas('employee', function ($query) use ($gymId) {
            $query->where('type', 'gym')->where('gym_id', $gymId);
        })
            ->with('employee')
            ->paginate(10);

        return response()->json([
            'users' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'count' => $users->count(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id ?? null;

        // validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15',
            'role_id' => 'required|exists:roles,id',
        ]);

        // get right role
        $role = Role::where('id', $request->role_id)->where('gym_id', $gymId)->where('guard_name', 'web-gym')->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
        ]);

        $user->employee()->create([
            'user_id' => $user->id,
            'gym_id' => $gymId,
            'type' => 'gym',
        ]);

        $user->assignRole($role);

        GymLog::create([
            'gym_id' => $gymId,
            'model_type' => User::class,
            'model_id' => $user->id,
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
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;

        $user = User::where('id', $id)
            ->whereHas('employee', function ($query) use ($gymId) {
                $query->where('type', 'gym')->where('gym_id', $gymId);
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
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;

        $user = User::where('id', $id)
            ->whereHas('employee', function ($query) use ($gymId) {
                $query->where('type', 'gym')->where('gym_id', $gymId);
            })
            ->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:15',
            'role_id' => 'sometimes|exists:roles,id',

            // Password validation
            'old_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed', // Requires `password_confirmation`
        ]);

        if ($request->filled('role_id')) {
            $role = Role::where('id', $request->role_id)->where('gym_id', $gymId)->where('guard_name', 'web-gym')->firstOrFail();
            $user->assignRole($role);
        }

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

        // Log the changes
        GymLog::create([
            'gym_id' => $gymId,
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
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;

        $user = User::where('id', $id)
            ->whereHas('employee', function ($query) use ($gymId) {
                $query->where('type', 'gym')->where('gym_id', $gymId);
            })
            ->firstOrFail();


        $userData = $user;

        $user->delete();

        GymLog::create([
            'gym_id' => $gymId,
            'model_type' => User::class,
            'model_id' => $userData->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'changes' => json_encode($userData),
        ]);

        return response()->json(['message' => 'تم حذف الموظف بنجاح'], 200);
    }

    public function roles()
    {
        $roles = Role::where('guard_name', 'web-gym')->select('id', 'name')->get();
        return response()->json([
            'roles' => $roles,
        ], 200);
    }
}
