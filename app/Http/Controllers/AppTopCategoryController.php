<?php
namespace App\Http\Controllers;

use App\Http\Requests\AppTopCategoryRequest;
use App\Services\Contracts\AppTopFetcherInterface;
use App\Http\Helpers\RequestLogger;
use App\Http\Helpers\ApiResponder;
use App\Http\Helpers\PositionHelper;
use Illuminate\Http\JsonResponse;



class AppTopCategoryController extends Controller
{
    protected AppTopFetcherInterface $fetcher;

    public function __construct(AppTopFetcherInterface $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function __invoke(AppTopCategoryRequest $request): JsonResponse
    {
        $date = $request->validated()['date'];

        RequestLogger::log($request->ip(), $date);

        $result = $this->fetcher->fetchAndStore($date);
        if (isset($result['error'])) {
            return ApiResponder::error('API fetch failed', 500);
        }

        $positions = PositionHelper::getMinPositionsGroupedByCategory($date);
        if ($positions->isEmpty()) {
            return ApiResponder::error('No data found for this date', 404);
        }

        return ApiResponder::success($positions);
    }
}
