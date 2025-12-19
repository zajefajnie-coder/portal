<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function show($id)
    {
        $user = User::approved()
            ->notBanned()
            ->with(['publicPortfolios.images', 'publicPortfolios.tags'])
            ->findOrFail($id);

        return response()->json($user);
    }

    public function index(Request $request)
    {
        $query = User::approved()
            ->notBanned()
            ->withCount('publicPortfolios');

        if ($request->has('profession')) {
            $query->where('profession', $request->profession);
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('bio', 'like', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate(20);

        return response()->json($users);
    }
}

