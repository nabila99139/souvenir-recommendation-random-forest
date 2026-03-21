<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\Souvenir;
use App\Services\Contracts\RecommendationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SouvenirController extends Controller
{
    /**
     * The recommendation service instance.
     *
     * @var \App\Services\Contracts\RecommendationServiceInterface
     */
    protected RecommendationServiceInterface $recommendationService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\Contracts\RecommendationServiceInterface $recommendationService
     * @return void
     */
    public function __construct(RecommendationServiceInterface $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get souvenir recommendations based on user preferences.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'age' => 'required|integer|min:1|max:120',
            'status' => 'required|in:student,worker',
            'budget' => 'required|numeric|min:0',
            'purpose' => 'required|in:family,colleague,partner'
        ]);

        try {
            $predictedCategory = $this->recommendationService->predictCategory($validated);

            Recommendation::create(array_merge($validated, [
                'predicted_category' => $predictedCategory
            ]));

            $recommendations = Souvenir::where('category', $predictedCategory)->get();

            return response()->json([
                'success' => true,
                'predicted_category' => $predictedCategory,
                'recommendations' => $recommendations,
                'total' => $recommendations->count()
            ], 200);
        } catch (\App\Exceptions\RecommendationApiException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Unable to process recommendation request',
                'message' => $e->getMessage()
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
