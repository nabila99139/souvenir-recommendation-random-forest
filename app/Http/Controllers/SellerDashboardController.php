<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Souvenir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SellerDashboardController extends Controller
{
    /**
     * Show the seller dashboard
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        // Get seller statistics
        $stats = [
            'total_souvenirs' => $this->getTotalSouvenirs($user->id),
            'total_views' => $this->getTotalViews($user->id),
            'recent_views' => $this->getRecentViews($user->id),
        ];

        Log::info('Seller dashboard accessed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'timestamp' => now()
        ]);

        return view('seller.dashboard', compact('user', 'stats'));
    }

    /**
     * Show business profile management page
     */
    public function businessProfile()
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        return view('seller.business-profile', compact('user'));
    }

    /**
     * Update business profile
     */
    public function updateBusinessProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'business_hours' => 'nullable|string|max:200',
        ], [
            'business_name.required' => 'Business name is required',
            'description.required' => 'Business description is required',
            'address.required' => 'Business address is required',
            'phone.required' => 'Contact phone is required',
        ]);

        try {
            // Update user profile
            $user->update([
                'business_name' => $request->business_name,
                'business_description' => $request->description,
                'business_address' => $request->address,
                'business_phone' => $request->phone,
                'business_hours' => $request->business_hours,
            ]);

            Log::info('Seller business profile updated', [
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'timestamp' => now()
            ]);

            return redirect()->route('seller.dashboard')->with('success', 'Business profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update seller business profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

    /**
     * Show souvenir catalog management page
     */
    public function souvenirs()
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        // Get seller's souvenirs
        $souvenirs = Souvenir::where('seller_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        Log::info('Seller souvenirs catalog accessed', [
            'user_id' => $user->id,
            'count' => $souvenirs->total(),
            'timestamp' => now()
        ]);

        return view('seller.souvenirs', compact('user', 'souvenirs'));
    }

    /**
     * Show create souvenir form
     */
    public function createSouvenir()
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        return view('seller.create-souvenir', compact('user'));
    }

    /**
     * Store new souvenir
     */
    public function storeSouvenir(Request $request)
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Souvenir name is required',
            'description.required' => 'Description is required',
            'price.required' => 'Price is required',
            'category.required' => 'Category is required',
            'image.required' => 'Image is required',
            'image.image' => 'Please upload a valid image',
            'image.mimes' => 'Image must be jpeg, png, jpg, or gif',
            'image.max' => 'Image size must be less than 2MB',
        ]);

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('souvenirs', 'public');
            }

            // Create souvenir
            Souvenir::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category' => $request->category,
                'image' => $imagePath,
                'seller_id' => $user->id,
                'views' => 0,
            ]);

            Log::info('New souvenir created by seller', [
                'user_id' => $user->id,
                'souvenir_name' => $request->name,
                'price' => $request->price,
                'timestamp' => now()
            ]);

            return redirect()->route('seller.souvenirs')->with('success', 'Souvenir added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create souvenir', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to add souvenir. Please try again.']);
        }
    }

    /**
     * Show edit souvenir form
     */
    public function editSouvenir($id)
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        $souvenir = Souvenir::where('id', $id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$souvenir) {
            return redirect()->route('seller.souvenirs')->with('error', 'Souvenir not found or access denied.');
        }

        return view('seller.edit-souvenir', compact('user', 'souvenir'));
    }

    /**
     * Update souvenir
     */
    public function updateSouvenir(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        $souvenir = Souvenir::where('id', $id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$souvenir) {
            return redirect()->route('seller.souvenirs')->with('error', 'Souvenir not found or access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Souvenir name is required',
            'description.required' => 'Description is required',
            'price.required' => 'Price is required',
            'category.required' => 'Category is required',
            'image.image' => 'Please upload a valid image',
            'image.mimes' => 'Image must be jpeg, png, jpg, or gif',
            'image.max' => 'Image size must be less than 2MB',
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('souvenirs', 'public');
                $souvenir->image = $imagePath;
            }

            // Update souvenir
            $souvenir->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category' => $request->category,
            ]);

            Log::info('Souvenir updated by seller', [
                'user_id' => $user->id,
                'souvenir_id' => $id,
                'timestamp' => now()
            ]);

            return redirect()->route('seller.souvenirs')->with('success', 'Souvenir updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update souvenir', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to update souvenir. Please try again.']);
        }
    }

    /**
     * Delete souvenir
     */
    public function deleteSouvenir($id)
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        $souvenir = Souvenir::where('id', $id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$souvenir) {
            return redirect()->route('seller.souvenirs')->with('error', 'Souvenir not found or access denied.');
        }

        try {
            $souvenir->delete();

            Log::info('Souvenir deleted by seller', [
                'user_id' => $user->id,
                'souvenir_id' => $id,
                'timestamp' => now()
            ]);

            return redirect()->route('seller.souvenirs')->with('success', 'Souvenir deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete souvenir', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to delete souvenir. Please try again.']);
        }
    }

    /**
     * Show lead tracking page
     */
    public function leads()
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return redirect()->route('home')->with('error', 'Access denied. Seller access required.');
        }

        // Get customer views statistics
        $leadData = [
            'total_views' => $this->getTotalViews($user->id),
            'today_views' => $this->getTodayViews($user->id),
            'this_week_views' => $this->getWeekViews($user->id),
            'this_month_views' => $this->getMonthViews($user->id),
            'top_viewed_souvenirs' => $this->getTopViewedSouvenirs($user->id),
            'recent_views' => $this->getRecentViews($user->id),
        ];

        Log::info('Seller lead tracking accessed', [
            'user_id' => $user->id,
            'total_views' => $leadData['total_views'],
            'timestamp' => now()
        ]);

        return view('seller.leads', compact('user', 'leadData'));
    }

    /**
     * Get total souvenirs count for seller
     */
    private function getTotalSouvenirs($sellerId): int
    {
        return Souvenir::where('seller_id', $sellerId)->count();
    }

    /**
     * Get total views for seller's souvenirs
     */
    private function getTotalViews($sellerId): int
    {
        return Souvenir::where('seller_id', $sellerId)->sum('views') ?? 0;
    }

    /**
     * Get today's views for seller
     */
    private function getTodayViews($sellerId): int
    {
        return Souvenir::where('seller_id', $sellerId)
            ->whereDate('updated_at', today())
            ->sum('views') ?? 0;
    }

    /**
     * Get this week's views for seller
     */
    private function getWeekViews($sellerId): int
    {
        return Souvenir::where('seller_id', $sellerId)
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('views') ?? 0;
    }

    /**
     * Get this month's views for seller
     */
    private function getMonthViews($sellerId): int
    {
        return Souvenir::where('seller_id', $sellerId)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('views') ?? 0;
    }

    /**
     * Get recent views for seller
     */
    private function getRecentViews($sellerId): array
    {
        return Souvenir::where('seller_id', $sellerId)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['name', 'views', 'updated_at'])
            ->map(function ($souvenir) {
                return [
                    'name' => $souvenir->name,
                    'views' => $souvenir->views,
                    'date' => $souvenir->updated_at->format('M d, Y H:i'),
                ];
            })
            ->toArray();
    }

    /**
     * Get top viewed souvenirs for seller
     */
    private function getTopViewedSouvenirs($sellerId): array
    {
        return Souvenir::where('seller_id', $sellerId)
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get(['name', 'views', 'category'])
            ->map(function ($souvenir) {
                return [
                    'name' => $souvenir->name,
                    'views' => $souvenir->views,
                    'category' => $souvenir->category,
                ];
            })
            ->toArray();
    }
}