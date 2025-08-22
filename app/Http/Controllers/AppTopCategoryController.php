<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\AppTopPosition;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AppTopCategoryRequest;
use App\Services\Contracts\AppTopFetcherInterface;

class AppTopCategoryController extends Controller
{
    protected AppTopFetcherInterface $fetcher;

    public function __construct(AppTopFetcherInterface $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function __invoke(AppTopCategoryRequest $request)
    {
        $date = $request->validated()['date'];

        Log::info('AppTopCategory endpoint hit', [
            'ip' => $request->ip(),
            'date_param' => $date,
            'timestamp' => now()->toDateTimeString()
        ]);

        $stored = $this->fetcher->fetchAndStore($date);

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
