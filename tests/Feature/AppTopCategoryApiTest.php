<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Models\AppTopPosition;

class AppTopCategoryApiTest extends TestCase
{
    use RefreshDatabase;
    protected string $endpoint = '/api/appTopCategory';


    // 1. Invalid calendar date (June 31)
    public function test_invalid_calendar_date_throws_422()
    {
        $response = $this->getJson("{$this->endpoint}?date=2025-06-31");
        $response->dump();
        $response->assertStatus(422);
    }

    // 2. Incomplete date (missing day)
    public function test_partial_date_throws_422()
    {
        $response = $this->getJson("{$this->endpoint}?date=2025-07");
        $response->dump();
        $response->assertStatus(422);
    }

    // 3. Missing date param
    public function test_missing_date_param_throws_422()
    {
        $response = $this->getJson($this->endpoint);
        $response->dump();
        $response->assertStatus(422);
    }

    // 4. Wrong format (DD-MM-YYYY)
    public function test_wrong_format_date_throws_422()
    {
        $response = $this->getJson("{$this->endpoint}?date=21-08-2025");
        $response->dump();
        $response->assertStatus(422);
    }


    // 5. API returns empty → “No data found”
    public function test_api_empty_response_returns_404()
    {
        Http::fake([$this->anyAppticaUrl() => Http::response(['data' => ['positions' => []]], 200)]);
        $response = $this->getJson("{$this->endpoint}?date=2025-08-21");
        $response->dump();
        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'No data found for this date']);
    }

    // 6. API returns HTTP 500 → error gracefully
    public function test_api_returns_500_is_handled_gracefully()
    {
        Http::fake([$this->anyAppticaUrl() => Http::response([], 500)]);
        $response = $this->getJson("{$this->endpoint}?date=2025-08-21");
        $response->dump();
        $response->assertStatus(500)
            ->assertJsonStructure(['error']);
    }

    // 7. API times out → handled
    public function test_api_timeout_is_handled()
    {
        Http::fake([$this->anyAppticaUrl() => Http::response(null, 504)]);
        $response = $this->getJson("{$this->endpoint}?date=2025-08-21");
        $response->dump();
        $response->assertStatus(500);
    }

    // Helper: Match any Apptica API request
    private function anyAppticaUrl(): string
    {
        return 'https://api.apptica.com/*';
    }

    // Helper: Common mocked response
    private function validApiResponse()
    {
        return Http::response([
            'data' => [
                'positions' => [
                    ['category' => 2, 'position' => 165],
                    ['category' => 2, 'position' => 40],
                    ['category' => 23, 'position' => 1]
                ]
            ]
        ], 200);
    }
}
