<?php
namespace App\Services;

use App\Models\AppTopPosition;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Contracts\AppTopFetcherInterface;

class AppticaTopService implements AppTopFetcherInterface
{
    public function fetchAndStore(string $date): array
    {
        if (AppTopPosition::where('date', $date)->exists()) {
            Log::info("Data for date {$date} already exists, skipping API call.");
            return ['cached' => true];
        }

        $url = "https://api.apptica.com/package/top_history/1421444/1";
        $apiKey = env('APPTICA_API_KEY'); // move API key to .env
        $fullUrl = "{$url}?date_from={$date}&date_to={$date}&B4NKGg={$apiKey}";

        try {
            $response = Http::timeout(10)->get($fullUrl);

            if (!$response->ok()) {
                Log::error("API request failed", ['status' => $response->status()]);
                return ['error' => 'API request failed'];
            }

            $data = $response->json();

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
