<?php

namespace App\Http\Controllers;

use App\Models\GymLog;
use App\Models\SaasLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function saas() 
    {
        $logs = SaasLog::paginate(10);

        $data = $logs->map(function ($log) {
            $log->changes = json_decode($log->changes, true);
            return $log;
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }

    public function gym()
    {
        $logs = GymLog::paginate(10);

        $data = $logs->map(function ($log) {
            $log->changes = json_decode($log->changes, true);
            return $log;
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }
}
