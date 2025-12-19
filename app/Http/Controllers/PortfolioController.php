<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $query = Portfolio::with(['user', 'images', 'tags'])
            ->public()
            ->withCount('images');

        // Search filters
        if ($request->has('profession')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('profession', $request->profession)
                  ->approved()
                  ->notBanned();
            });
        }

        if ($request->has('city')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%')
                  ->approved()
                  ->notBanned();
            });
        }

        if ($request->has('tags')) {
            $tagIds = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $portfolios = $query->latest()->paginate(12);

        return response()->json($portfolios);
    }

    public function show($id)
    {
        $portfolio = Portfolio::with(['user', 'images', 'tags'])
            ->public()
            ->findOrFail($id);

        // Increment views
        $portfolio->incrementViews();

        return response()->json($portfolio);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ]);

        $portfolio = Portfolio::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? true,
        ]);

        // Handle tags
        if (!empty($validated['tags'])) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tagName = trim($tagName);
                if (empty($tagName)) continue;
                
                // Sanitize tag name (prevent XSS)
                $tagName = htmlspecialchars(strip_tags($tagName), ENT_QUOTES, 'UTF-8');
                
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
            $portfolio->tags()->sync($tagIds);
        }

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('portfolios/' . $portfolio->id, 'public');
                
                $portfolio->images()->create([
                    'image_path' => $path,
                    'order' => $index,
                    'alt_text' => $validated['title'] . ' - Image ' . ($index + 1),
                ]);
            }
        }

        return response()->json($portfolio->load(['images', 'tags']), 201);
    }

    public function update(Request $request, $id)
    {
        $portfolio = Portfolio::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'image_order' => 'sometimes|array',
            'image_order.*' => 'integer|exists:portfolio_images,id',
        ]);

        $portfolio->update([
            'title' => $validated['title'] ?? $portfolio->title,
            'description' => $validated['description'] ?? $portfolio->description,
            'is_public' => $validated['is_public'] ?? $portfolio->is_public,
        ]);

        // Update tags
        if (isset($validated['tags'])) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tagName = trim($tagName);
                if (empty($tagName)) continue;
                
                $tagName = htmlspecialchars(strip_tags($tagName), ENT_QUOTES, 'UTF-8');
                
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
            $portfolio->tags()->sync($tagIds);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $currentMaxOrder = $portfolio->images()->max('order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('portfolios/' . $portfolio->id, 'public');
                
                $portfolio->images()->create([
                    'image_path' => $path,
                    'order' => ++$currentMaxOrder,
                    'alt_text' => $portfolio->title . ' - Image ' . ($currentMaxOrder + 1),
                ]);
            }
        }

        // Update image order
        if (isset($validated['image_order'])) {
            foreach ($validated['image_order'] as $order => $imageId) {
                $portfolio->images()->where('id', $imageId)->update(['order' => $order]);
            }
        }

        return response()->json($portfolio->load(['images', 'tags']));
    }

    public function destroy(Request $request, $id)
    {
        $portfolio = Portfolio::where('user_id', $request->user()->id)->findOrFail($id);

        // Delete images from storage
        foreach ($portfolio->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            if ($image->thumbnail_path) {
                Storage::disk('public')->delete($image->thumbnail_path);
            }
        }

        $portfolio->delete();

        return response()->json(['message' => 'Portfolio deleted successfully']);
    }

    public function myPortfolios(Request $request)
    {
        $portfolios = Portfolio::with(['images', 'tags'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($portfolios);
    }
}



