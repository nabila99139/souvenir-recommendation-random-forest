<?php

namespace App\Services;

use App\Exceptions\RecommendationApiException;
use App\Services\Contracts\RecommendationServiceInterface;
use Illuminate\Support\Facades\Http;

class RecommendationService implements RecommendationServiceInterface
{
    /**
     * The API endpoint URL for the recommendation service.
     *
     * @var string
     */
    protected string $apiUrl;

    /**
     * Timeout for HTTP requests in seconds.
     *
     * @var int
     */
    protected int $timeout;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiUrl = config('services.recommendation_api.url');
        $this->timeout = config('services.recommendation_api.timeout', 30);
    }

    /**
     * Predict souvenir category based on user preferences.
     *
     * @param array $userPreferences User preference data including age, status, budget, and purpose
     * @return string The predicted category name
     * @throws \App\Exceptions\RecommendationApiException When API request fails
     */
    public function predictCategory(array $userPreferences): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl, $userPreferences);

            if (!$response->successful()) {
                throw new RecommendationApiException(
                    "API request failed with status code: {$response->status()}"
                );
            }

            $data = $response->json();

            if (!isset($data['predicted_category'])) {
                throw new RecommendationApiException(
                    "Invalid API response: predicted_category field is missing"
                );
            }

            return $data['predicted_category'];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new RecommendationApiException(
                "Failed to connect to recommendation API: {$e->getMessage()}"
            );
        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new RecommendationApiException(
                "API request failed: {$e->getMessage()}"
            );
        }
    }
}
