<?php

namespace App\Http\Controllers;

use App\Models\GymLog;
use App\Models\GymSubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GymSubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = GymSubscriptionPlan::where('gym_id', Auth::user()->gym->id)->paginate(10);

        return response()->json([
            'data' => $plans->items(),
            'meta' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255|unique:gym_subscription_plans,name',
            'duration_type' => 'required|string|in:daily,weekly,monthly,yearly',
            'duration_count' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $fields['gym_id'] = Auth::user()->gym->id;

        $plan = GymSubscriptionPlan::create($fields);

        GymLog::create([
            'gym_id' => Auth::user()->gym->id,
            'model_type' => GymSubscriptionPlan::class,
            'model_id' => $plan->id,
            'user_id' => Auth::user()->id,
            'action' => 'created',
            'changes' => json_encode($fields)
        ]);

        return response()->json(['message' => 'تم إنشاء الخطة بنجاح'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = GymSubscriptionPlan::findOrFail($id);
        return response()->json(['plan' => $plan], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plan = GymSubscriptionPlan::findOrFail($id);

        $fields = $request->validate([
            'name' => 'sometimes|string|max:255|unique:gym_subscription_plans,name,' . $plan->id,
            'duration_type' => 'sometimes|string|in:daily,weekly,monthly,yearly',
            'duration_count' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $original = $plan->getOriginal();

        $plan->update($fields);

        $current = $plan->getAttributes();

        $changes = array_diff_assoc($original, $current);

        GymLog::create([
            'gym_id' => Auth::user()->gym->id,
            'model_type' => GymSubscriptionPlan::class,
            'model_id' => $plan->id,
            'user_id' => Auth::user()->id,
            'action' => 'updated',
            'changes' => json_encode($changes),
        ]);

        return response()->json(['message' => 'تم تحديث الخطة بنجاح'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plan = GymSubscriptionPlan::findOrFail($id);
        $planData = $plan;
        $plan->delete();

        GymLog::create([
            'gym_id' => Auth::user()->gym->id,
            'model_type' => GymSubscriptionPlan::class,
            'model_id' => $planData->id,
            'user_id' => Auth::user()->id,
            'action' => 'deleted',
            'changes' => json_encode($planData),
        ]);

        return response()->json(['message' => 'تم حذف الخطة بنجاح'], 200);
    }
}
