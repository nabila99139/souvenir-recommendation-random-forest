<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\Souvenir;
use App\Services\Contracts\RecommendationServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
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
     * Show the application home page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('home');
    }

    /**
     * Show the souvenir catalog.
     *
     * @return \Illuminate\View\View
     */
    public function catalog(): View
    {
        $souvenirs = Souvenir::all();
        return view('catalog', compact('souvenirs'));
    }

    /**
     * Process the recommendation form and show results.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitRecommendation(Request $request): RedirectResponse
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

            return redirect()->route('recommend.results')
                ->with([
                    'predictedCategory' => $predictedCategory,
                    'recommendations' => $recommendations,
                    'total' => $recommendations->count()
                ]);

        } catch (\App\Exceptions\RecommendationApiException $e) {
            return back()->with('error', 'Unable to process recommendation: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Show recommendation results.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function showResults(Request $request): View
    {
        $predictedCategory = $request->session()->get('predictedCategory', 'souvenirs');
        $recommendations = $request->session()->get('recommendations', collect());
        $total = $request->session()->get('total', 0);

        return view('results', compact('predictedCategory', 'recommendations', 'total'));
    }
}
