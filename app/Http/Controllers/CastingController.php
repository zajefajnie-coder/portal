<?php

namespace App\Http\Controllers;

use App\Models\Casting;
use Illuminate\Http\Request;

class CastingController extends Controller
{
    public function index()
    {
        $castings = Casting::with('user')
            ->where('status', 'open')
            ->latest()
            ->paginate(12);
        
        return view('castings.index', compact('castings'));
    }

    public function create()
    {
        return view('castings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:100'],
            'casting_date' => ['nullable', 'date'],
            'required_roles' => ['required', 'array', 'min:1'],
            'required_roles.*' => ['string'],
        ]);

        Casting::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'casting_date' => $validated['casting_date'],
            'required_roles' => $validated['required_roles'],
            'status' => 'open',
        ]);

        return redirect()->route('castings.index')->with('success', 'Casting utworzony pomyślnie.');
    }

    public function show(Casting $casting)
    {
        $casting->load('user');
        return view('castings.show', compact('casting'));
    }

    public function edit(Casting $casting)
    {
        $this->authorize('update', $casting);
        return view('castings.edit', compact('casting'));
    }

    public function update(Request $request, Casting $casting)
    {
        $this->authorize('update', $casting);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:100'],
            'casting_date' => ['nullable', 'date'],
            'required_roles' => ['required', 'array', 'min:1'],
            'required_roles.*' => ['string'],
            'status' => ['required', 'in:open,closed,filled'],
        ]);

        $casting->update($validated);

        return redirect()->route('castings.show', $casting)->with('success', 'Casting zaktualizowany.');
    }

    public function destroy(Casting $casting)
    {
        $this->authorize('delete', $casting);
        $casting->delete();

        return redirect()->route('castings.index')->with('success', 'Casting usunięty.');
    }
}


