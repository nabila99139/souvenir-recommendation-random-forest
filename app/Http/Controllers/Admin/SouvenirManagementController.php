<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Souvenir;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SouvenirManagementController extends Controller
{
    /**
     * Display all souvenirs.
     */
    public function index(): View
    {
        $souvenirs = Souvenir::latest()->paginate(10);
        return view('admin.souvenirs.index', compact('souvenirs'));
    }

    /**
     * Show souvenir details.
     */
    public function show(Souvenir $souvenir): View
    {
        return view('admin.souvenirs.show', compact('souvenir'));
    }

    /**
     * Show souvenir create form.
     */
    public function create(): View
    {
        return view('admin.souvenirs.create');
    }

    /**
     * Store new souvenir.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'price_range' => 'required|in:low,medium,high',
            'image_path' => 'nullable|string|max:255',
        ]);

        Souvenir::create($validated);

        return redirect()->route('admin.souvenirs.index')
            ->with('success', 'Souvenir created successfully.');
    }

    /**
     * Show souvenir edit form.
     */
    public function edit(Souvenir $souvenir): View
    {
        return view('admin.souvenirs.edit', compact('souvenir'));
    }

    /**
     * Update souvenir.
     */
    public function update(Request $request, Souvenir $souvenir): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'price_range' => 'required|in:low,medium,high',
            'image_path' => 'nullable|string|max:255',
        ]);

        $souvenir->update($validated);

        return redirect()->route('admin.souvenirs.index')
            ->with('success', 'Souvenir updated successfully.');
    }

    /**
     * Delete souvenir.
     */
    public function destroy(Souvenir $souvenir): RedirectResponse
    {
        $souvenir->delete();

        return redirect()->route('admin.souvenirs.index')
            ->with('success', 'Souvenir deleted successfully.');
    }
}
