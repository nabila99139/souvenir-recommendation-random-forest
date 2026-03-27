<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\Souvenir;
use App\Services\Contracts\RecommendationServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Verify user is authenticated customer or admin (for testing/oversight).
     *
     * @return void
     * @throws \Illuminate\Auth\AccessDeniedHttpException
     */
    protected function verifyCustomerOrAdminAccess(): void
    {
        if (!Auth::check()) {
            abort(401, 'Authentication required.');
        }

        // Allow access to customers and admins only
        if (!Auth::user()->isCustomer() && !Auth::user()->isRoot()) {
            if (Auth::user()->isSeller()) {
                abort(403, 'Access denied. This area is for customers and admins only. Redirecting to seller dashboard.');
            }

            abort(403, 'Access denied. Customer or admin access required.');
        }
    }

    /**
     * Verify user is authenticated customer only (for main dashboard).
     *
     * @return void
     * @throws \Illuminate\Auth\AccessDeniedHttpException
     */
    protected function verifyCustomerAccess(): void
    {
        if (!Auth::check()) {
            abort(401, 'Authentication required.');
        }

        if (!Auth::user()->isCustomer()) {
            abort(403, 'Access denied. Customer access required.');
        }
    }

    /**
     * Show the welcome/landing page.
     *
     * @return \Illuminate\View\View
     */
    public function welcome(): View
    {
        return view('welcome');
    }

    /**
     * Show the application home page (authenticated customers only).
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $this->verifyCustomerOrAdminAccess();

        return view('home');
    }

    /**
     * Show the souvenir catalog (customers and admins only).
     *
     * @return \Illuminate\View\View
     */
    public function catalog(): View
    {
        $this->verifyCustomerOrAdminAccess();

        // Get all souvenirs from all sellers for customers to browse
        $souvenirs = Souvenir::whereNotNull('seller_id')
            ->orderBy('views', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('catalog', compact('souvenirs'));
    }

    /**
     * Process the recommendation form and show results (customers only).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitRecommendation(Request $request): RedirectResponse
    {
        $this->verifyCustomerOrAdminAccess();

        $validated = $request->validate([
            'age' => 'required|integer|min:1|max:120',
            'status' => 'required|in:student,worker',
            'budget' => 'required|numeric|min:0',
            'purpose' => 'required|in:family,colleague,partner'
        ]);

        try {
            $predictedCategory = $this->recommendationService->predictCategory($validated);

            // Save recommendation request to database
            Recommendation::create(array_merge($validated, [
                'predicted_category' => $predictedCategory
            ]));

            // Get souvenirs that match the predicted category
            $recommendations = Souvenir::where('category', $predictedCategory)
                ->whereNotNull('seller_id')
                ->orderBy('views', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

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
     * Show recommendation results (customers and admins only).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function showResults(Request $request): View
    {
        $this->verifyCustomerOrAdminAccess();

        $predictedCategory = $request->session()->get('predictedCategory', 'souvenirs');
        $recommendations = $request->session()->get('recommendations', collect());
        $total = $request->session()->get('total', 0);

        return view('results', compact('predictedCategory', 'recommendations', 'total'));
    }
}
