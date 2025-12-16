<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::with('user')->latest()->paginate(12);
        return view('portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        return view('portfolios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'max:10240'],
        ]);

        $imagePath = $request->file('image')->store('uploads/portfolios', 'public');
        
        Portfolio::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('portfolios.index')->with('success', 'Portfolio dodane pomyślnie.');
    }

    public function show(Portfolio $portfolio)
    {
        $portfolio->load('user');
        return view('portfolios.show', compact('portfolio'));
    }

    public function edit(Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);
        return view('portfolios.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($portfolio->image_path);
            $validated['image_path'] = $request->file('image')->store('uploads/portfolios', 'public');
        }

        $portfolio->update($validated);

        return redirect()->route('portfolios.show', $portfolio)->with('success', 'Portfolio zaktualizowane.');
    }

    public function destroy(Portfolio $portfolio)
    {
        $this->authorize('delete', $portfolio);
        
        Storage::disk('public')->delete($portfolio->image_path);
        $portfolio->delete();

        return redirect()->route('portfolios.index')->with('success', 'Portfolio usunięte.');
    }
}


