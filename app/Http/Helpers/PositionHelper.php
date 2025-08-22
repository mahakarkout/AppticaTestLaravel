<?php

namespace App\Http\Helpers;

use App\Models\AppTopPosition;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\ApiResponder;

class PositionHelper
{
    public static function getMinPositionsGroupedByCategory(string $date)
    {
        return AppTopPosition::where('date', $date)
            ->select('category_id', DB::raw('MIN(position) as min_position'))
            ->groupBy('category_id')
            ->pluck('min_position', 'category_id');
    }

    public static function getResponseOrNotFound(string $date)
    {
        $positions = self::getMinPositionsGroupedByCategory($date);

        if ($positions->isEmpty()) {
            return ApiResponder::error('No data found for this date', 404);
        }

        return ApiResponder::success($positions);
    }
}
