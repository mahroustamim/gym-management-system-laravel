<?php

namespace App\Http\Controllers;

use App\Models\GymLog;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;
        $trainers = Trainer::where('gym_id', $gymId)->paginate(10);

        return response()->json([
            'trainers' => $trainers->items(),
            'pagination' => [
                'total' => $trainers->total(),
                'count' => $trainers->count(),
                'per_page' => $trainers->perPage(),
                'current_page' => $trainers->currentPage(),
                'last_page' => $trainers->lastPage(),
            ],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:trainers,email',
            'specialty' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $data['gym_id'] = $gymId;

        $trainer = Trainer::create($data);

        GymLog::create([
            'gym_id' => $gymId,
            'user_id' => Auth::id(),
            'model_type' => Trainer::class,
            'model_id' => $trainer->id,
            'action' => 'created',
            'changes' => json_encode($trainer)
        ]);

        return response()->json(['message' => 'تم تسجيل المدرب بنجاح'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;

        $trainer = Trainer::where('id', $id)->where('gym_id', $gymId)->firstOrFail();
        return response()->json(['trainer' => $trainer], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;

        $trainer = Trainer::where('id', $id)->where('gym_id', $gymId)->firstOrFail();

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|unique:trainers,email,' . $trainer->id,
            'specialty' => 'nullable|string|max:255',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $trainer->update($data);

        GymLog::create([
            'gym_id' => $gymId,
            'user_id' => Auth::id(),
            'model_type' => Trainer::class,
            'model_id' => $trainer->id,
            'action' => 'updated',
            'changes' => json_encode($trainer->getChanges())
        ]);

        return response()->json(['message' => 'تم  تحديث المدرب بنجاح'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gymId = Auth::user()->gym->id ?? Auth::user()->employee->gym_id;

        $trainer = Trainer::where('id', $id)->where('gym_id', $gymId)->firstOrFail();
        $trainerData = $trainer;
        $trainer->delete();

        GymLog::create([
            'gym_id' => $gymId,
            'user_id' => Auth::id(),
            'model_type' => Trainer::class,
            'model_id' => $trainerData->id,
            'action' => 'deleted',
            'changes' => json_encode($trainerData)
        ]);

        return response()->json(['message' => 'تم حذف المدرب بنجاح'], 200);
    }
}
