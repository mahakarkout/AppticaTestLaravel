<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;

class DateHelper
{
    public static function getValidatedDate(Request $request): string
    {
        return $request->validated()['date'] ?? now()->toDateString();
    }
}
