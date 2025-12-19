<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'pending_approvals' => User::where('is_approved', false)->where('is_banned', false)->count(),
            'banned_users' => User::where('is_banned', true)->count(),
            'total_portfolios' => Portfolio::count(),
            'reported_images' => PortfolioImage::reported()->count(),
            'recent_registrations' => User::latest()->take(10)->get(['id', 'name', 'email', 'profession', 'created_at', 'is_approved']),
            'top_profiles' => User::approved()
                ->notBanned()
                ->withCount('portfolios')
                ->orderBy('portfolios_count', 'desc')
                ->take(10)
                ->get(['id', 'name', 'profession', 'city']),
        ];

        return response()->json($stats);
    }

    public function users(Request $request)
    {
        $query = User::with('roles');

        if ($request->has('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->where('is_approved', false)->where('is_banned', false);
                    break;
                case 'approved':
                    $query->where('is_approved', true)->where('is_banned', false);
                    break;
                case 'banned':
                    $query->where('is_banned', true);
                    break;
            }
        }

        if ($request->has('profession')) {
            $query->where('profession', $request->profession);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate(20);

        return response()->json($users);
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_approved' => true]);

        return response()->json(['message' => 'User approved successfully', 'user' => $user]);
    }

    public function denyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Soft delete

        return response()->json(['message' => 'User registration denied']);
    }

    public function banUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'is_banned' => true,
            'banned_at' => now(),
        ]);

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json(['message' => 'User banned successfully', 'user' => $user]);
    }

    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'is_banned' => false,
            'banned_at' => null,
        ]);

        return response()->json(['message' => 'User unbanned successfully', 'user' => $user]);
    }

    public function assignRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:admin,moderator,user',
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles([$validated['role']]);

        return response()->json(['message' => 'Role assigned successfully', 'user' => $user->load('roles')]);
    }

    public function reportedImages()
    {
        $images = PortfolioImage::reported()
            ->with(['portfolio.user'])
            ->latest()
            ->paginate(20);

        return response()->json($images);
    }

    public function hideImage($id)
    {
        $image = PortfolioImage::findOrFail($id);
        $image->update(['is_hidden' => true]);

        return response()->json(['message' => 'Image hidden successfully']);
    }

    public function unhideImage($id)
    {
        $image = PortfolioImage::findOrFail($id);
        $image->update([
            'is_hidden' => false,
            'is_reported' => false,
            'report_reason' => null,
        ]);

        return response()->json(['message' => 'Image unhidden successfully']);
    }

    public function deleteImage($id)
    {
        $image = PortfolioImage::findOrFail($id);
        
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        if ($image->thumbnail_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->thumbnail_path);
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    public function tags()
    {
        $tags = \App\Models\Tag::withCount('portfolios')->orderBy('name')->get();
        return response()->json($tags);
    }

    public function deleteTag($id)
    {
        $tag = \App\Models\Tag::findOrFail($id);
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }
}



