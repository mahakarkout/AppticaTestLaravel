<?php

namespace App\Services;

use App\Models\AppTopPosition;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppticaTopService
{
    public function fetchAndStore(string $date)
    {
        if (AppTopPosition::where('date', $date)->exists()) {
            Log::info("Data for date {$date} already exists, skipping API call.");
            return ['cached' => true];
        }

        $url = "https://api.apptica.com/package/top_history/1421444/1";
        $apiKey = "fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l";
        $fullUrl = "{$url}?date_from={$date}&date_to={$date}&B4NKGg={$apiKey}";

        try {
            $response = Http::timeout(10)->get($fullUrl);

            if (!$response->ok()) {
                Log::error("API request failed", ['status' => $response->status()]);
                return ['error' => 'API request failed'];
            }

            $data = $response->json();

            // ✅ Save debug logs
            Log::info("ENV: " . env('APP_ENV'));
            Log::info("URL being hit: " . $fullUrl);
            Log::info("Full API response: " . json_encode($data));

            if (empty($data['data'])) {
                Log::warning("No data returned for date: $date");
                return ['warning' => 'No data returned'];
            }

            foreach ($data['data'] as $countryId => $categories) {
                foreach ($categories as $categoryId => $dates) {
                    foreach ($dates as $dateKey => $rank) {
                        AppTopPosition::firstOrCreate([
                            'date' => $dateKey,
                            'category_id' => $categoryId,
                        ], [
                            'position' => $rank,
                            'app_id' => 1421444,
                            'country' => $countryId,
                        ]);
                    }
                }
            }

            return ['stored' => true];

        } catch (\Exception $e) {
            Log::error("Exception while calling API", ['msg' => $e->getMessage()]);
            return ['error' => 'API error'];
        }
    }
}
