<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\GymLog;
use App\Models\SaasLog;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GymController extends Controller
{
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gyms = Gym::paginate(10);

        return response()->json([
            'data' => $gyms->items(),
            'meta' => [
                'current_page' => $gyms->currentPage(),
                'last_page' => $gyms->lastPage(),
                'per_page' => $gyms->perPage(),
                'total' => $gyms->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255|unique:gyms,name',
            'email' => 'required|email|max:255',
            'phone' => 'required|digits_between:11,15',
            'address' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'subscription_plan_id' => 'required|exists:saas_subscription_plans,id',
        ]);

        $filename = null;
        $file = $request->file('logo');
        if ($file) {
            $filename = $this->uploadImage($file, 'gyms');
        }

        Gym::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $filename,
            'subscription_plan_id' => $request->subscription_plan_id,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return response()->json(['message' => 'تم إنشاء الصالة الرياضية بنجاح'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gym = Gym::findOrFail($id);
        return response()->json(['gym' => $gym], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gym = Gym::findOrFail($id);
        
        $fields = $request->validate([
            'name' => 'sometimes|string|max:255|unique:gyms,name,' . $gym->id,
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:15',
            'address' => 'sometimes|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'subscription_plan_id' => 'sometimes|exists:saas_subscription_plans,id',
        ]);
        
        // خزن النسخة الأصلية قبل التعديل
        $original = $gym->getOriginal();

        // التعامل مع الصورة
        if ($request->hasFile('logo')) {
            $this->deleteImage($gym->logo, 'gyms');
            $fields['logo'] = $this->uploadImage($request->file('logo'), 'gyms');
        }
        
        $fields['user_id'] = Auth::id();

        // حدّث كل البيانات دفعة واحدة
        $gym->update($fields);

        $current = $gym->getAttributes();

        $changes = array_diff_assoc($current, $original);

        GymLog::create([
            'gym_id' => $gym->id,
            'model_type' => Gym::class,
            'model_id' => $gym->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'changes' => json_encode($changes),
        ]);

        return response()->json(['message' => 'تم تحديث الصالة الرياضية بنجاح'], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gym = Gym::findOrFail($id);

        $gymData = json_encode($gym);

        $gym->delete();

        $filename = $gym->logo;
        if ($filename) {
            $this->deleteImage($filename, 'gyms');
        }

        SaasLog::create([
            'model_type' => Gym::class,
            'model_id' => $gym->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'changes' => $gymData,
        ]);

        return response()->json(['message' => 'تم حذف الصالة الرياضية بنجاح'], 200);
    }
}
