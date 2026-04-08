<?php

namespace App\Http\Controllers;
use App\Models\Film;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function index() {
        // Todas las películas de la base de datos y del seeder
        $films = Film::all();

        return Inertia::render('Home', [
            'movies' => $films,
        ]);
    }

    public function show(Film $film) {
        return Inertia::render('Movies/Show', [
            'film' => $film,
        ]);
    }

    public function create() {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        return Inertia::render('Films/Create');
    }

    public function store(Request $request) {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'release_date' => 'required|date',
            'director' => 'nullable|string|max:255',
            'genre' => 'required|string|max:255',
            'distribution' => 'nullable|string|max:255',
            'synopsis' => 'required|string',
            'duration' => 'required|integer',
            'trailer' => 'required|mimes:mp4,avi,mov,wmv|max:524288',
        ]);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }
        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }
        if ($request->hasFile('trailer')) {
            $validated['trailer'] = $request->file('trailer')->store('trailers', 'public');
        }

        Film::create($validated);

        return redirect()->route('home')->with('success', 'Película creada exitosamente.');
    }

    public function edit(Film $film) {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        return Inertia::render('Films/Edit', [
            'film' => $film,
        ]);
    }

    public function update(Request $request, Film $film) {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'release_date' => 'required|date',
            'director' => 'nullable|string|max:255',
            'genre' => 'required|string|max:255',
            'distribution' => 'nullable|string|max:255',
            'synopsis' => 'required|string',
            'duration' => 'required|integer',
            'trailer' => 'nullable|mimes:mp4,avi,mov,wmv|max:524288',
        ]);

        // Handle file uploads if the logo, poster or trailer are not being edited keeping the old ones 
        if ($request->hasFile('logo')) {
            if ($film->logo) {
                Storage::disk('public')->delete($film->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($validated['logo']);
        }
        if ($request->hasFile('poster')) {
            if ($film->poster) {
                Storage::disk('public')->delete($film->poster);
            }
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        } else {
            unset($validated['poster']);
        }
        if ($request->hasFile('trailer')) {
            if ($film->trailer) {
                Storage::disk('public')->delete($film->trailer);
            }
            $validated['trailer'] = $request->file('trailer')->store('trailers', 'public');
        } else {
            unset($validated['trailer']);
        }

        $film->update($validated);

        return redirect()->route('home')->with('success', 'Película actualizada exitosamente.');
    }

    public function destroy(Film $film) {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        // Delete associated files
        if ($film->logo) {
            Storage::disk('public')->delete($film->logo);
        }
        if ($film->poster) {
            Storage::disk('public')->delete($film->poster);
        }
        if ($film->trailer) {
            Storage::disk('public')->delete($film->trailer);
        }
        $film->delete();

        return redirect()->route('home')->with('success', 'Película eliminada exitosamente.');
    }
}
