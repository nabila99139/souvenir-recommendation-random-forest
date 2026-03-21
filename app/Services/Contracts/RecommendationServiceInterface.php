<?php

namespace App\Services\Contracts;

interface RecommendationServiceInterface
{
    /**
     * Predict souvenir category based on user preferences.
     *
     * @param array $userPreferences User preference data including age, status, budget, and purpose
     * @return string The predicted category name
     * @throws \App\Exceptions\RecommendationApiException When API request fails
     */
    public function predictCategory(array $userPreferences): string;
}
