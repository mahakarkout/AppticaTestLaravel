<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;

class RequestLogger
{
    public static function log(string $ip, string $date): void
    {
        Log::info('AppTopCategory endpoint hit', [
            'ip' => $ip,
            'date_param' => $date,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
