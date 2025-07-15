<?php

namespace App\Http\Controllers;

use App\Models\SaasLog;
use App\Models\SaasSubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaasSubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = SaasSubscriptionPlan::paginate(10);

        return response()->json([
            'data' => $plans->items(),
            'meta' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'employee_limit' => 'required|integer|min:1',
            'features' => 'required|array',
            'features.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $plan = SaasSubscriptionPlan::create($fields);

        SaasLog::create([
            'model_type' => SaasSubscriptionPlan::class,
            'model_id' => $plan->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'changes' => json_encode($fields),
        ]);

        return response()->json(['message' => 'تم إنشاء الخطة بنجاح'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = SaasSubscriptionPlan::findOrFail($id);
        return response()->json(['plan' => $plan], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plan = SaasSubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'duration_days' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
            'employee_limit' => 'sometimes|integer|min:1',
            'features' => 'sometimes|array',
            'features.*' => 'string|max:255',
            'notes' => 'nullable|string',
        ]);

        $original = $plan->getOriginal();
        if (isset($original['features']) && is_array($original['features'])) {
            $original['features'] = json_encode($original['features']);
        }

        $plan->update($validated);

        $current = $plan->getAttributes();
        if (isset($current['features']) && is_array($current['features'])) {
            $current['features'] = json_encode($current['features']);
        }

        $changes = array_diff_assoc($current, $original);

        SaasLog::create([
            'model_type' => SaasSubscriptionPlan::class,
            'model_id' => $plan->id,
            'user_id' => Auth::id(),
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
        $plan = SaasSubscriptionPlan::findOrFail($id);
        $planData = json_encode($plan);
        $plan->delete();

        SaasLog::create([
            'model_type' => SaasSubscriptionPlan::class,
            'model_id' => $id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'changes' => $planData,
        ]);

        return response()->json(['message' => 'تم حذف الخطة بنجاح'], 200);
    }
}
