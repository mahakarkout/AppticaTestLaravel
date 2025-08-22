<?php
namespace App\Http\Controllers;

use App\Http\Requests\AppTopCategoryRequest;
use App\Services\Contracts\AppTopFetcherInterface;
use App\Http\Helpers\RequestLogger;
use App\Http\Helpers\ApiResponder;
use App\Http\Helpers\PositionHelper;
use App\Http\Helpers\DateHelper;
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
        $date = DateHelper::getValidatedDate($request);

        RequestLogger::log($request->ip(), $date);

        $result = $this->fetcher->fetchAndStore($date);

        $errorResponse = ApiResponder::handleFetchResult($result);
        if ($errorResponse) {
            return $errorResponse;
        }

        return PositionHelper::getResponseOrNotFound($date);
    }
}
