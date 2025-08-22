<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\AppticaTopService;
use App\Models\AppTopPosition;
use Illuminate\Support\Facades\Log;

class AppTopCategoryController extends Controller
{

    protected $service;

    public function __construct(AppticaTopService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        Log::info('AppTopCategory endpoint hit', [
            'ip' => $request->ip(),
            'date_param' => $request->query('date'),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            $validated = $request->validate([
                'date' => [
                    'required',
                    'regex:/^\d{4}-\d{2}-\d{2}$/',
                    function ($attribute, $value, $fail) {
                        $parts = explode('-', $value);
                        if (count($parts) !== 3 || !checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
                            $fail("The $attribute must be a real calendar date in the format YYYY-MM-DD.");
                        }
                    }
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $date = $request->query('date', now()->toDateString());

        $stored = $this->service->fetchAndStore($date);

        if (isset($stored['error'])) {
            return response()->json(['error' => $stored['error']], 500);
        }

        $positions = AppTopPosition::where('date', $date)
            ->select('category_id', DB::raw('MIN(position) as min_position'))
            ->groupBy('category_id')
            ->pluck('min_position', 'category_id');

        if ($positions->isEmpty()) {
            return response()->json(['message' => 'No data found for this date'], 404);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'ok',
            'data' => $positions
        ]);
    }

}
