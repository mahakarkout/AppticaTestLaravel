<?php

namespace App\Http\Helpers;

use App\Models\AppTopPosition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PositionHelper
{
    public static function getMinPositionsGroupedByCategory(string $date): Collection
    {
        return AppTopPosition::where('date', $date)
            ->select('category_id', DB::raw('MIN(position) as min_position'))
            ->groupBy('category_id')
            ->pluck('min_position', 'category_id');
    }
}
