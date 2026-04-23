<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FilmController extends Controller {
    private const YOUTUBE_URL_REGEX = '/^(https?:\/\/)?(www\.|m\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/)[A-Za-z0-9_-]{11}([&?].*)?$/';

    public function index() {
        $today = now()->toDateString();

        // Up to 4 upcoming films (release date in the future)
        $upcomingFilms = Film::where('release_date', '>', $today)
            ->orderBy('release_date')
            ->limit(4)
            ->get();

        // Up to 8 films currently on billboard (release date today or past)
        $billboardFilms = Film::where('release_date', '<=', $today)
            ->orderByDesc('release_date')
            ->limit(8)
            ->get();

        return Inertia::render('Home', [
            'upcomingFilms' => $upcomingFilms,
            'billboardFilms' => $billboardFilms,
        ]);
    }

    public function show(Film $film) {
        $sessions = $film->movieSessions()
            ->where('date', '>=', now())
            ->with('room')
            ->orderBy('date')
            ->get()
            ->map(fn ($session) => [
                'id' => $session->id,
                'room_id' => $session->room_id,
                'date' => $session->date->toISOString(),
                'price' => (float) $session->price,
                'room' => $session->room->name,
            ]);

        $rooms = Room::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Movies/Show', [
            'film' => $film,
            'sessions' => $sessions,
            'rooms' => $rooms,
            'canManageSessions' => Auth::user()?->role === 'admin',
        ]);
    }

    public function billboard() {
        $today = now()->toDateString();

        $films = Film::where('release_date', '<=', $today)
            ->orderByDesc('release_date')
            ->get();

        return Inertia::render('Cartelera', [
            'films' => $films,
        ]);
    }

    public function upcoming() {
        $today = now()->toDateString();

        $films = Film::where('release_date', '>', $today)
            ->orderBy('release_date')
            ->get();

        return Inertia::render('ProximosEstrenos', [
            'films' => $films,
        ]);
    }

    public function create() {
        $this->ensureAdmin();

        return Inertia::render('Films/Create');
    }

    public function store(Request $request) {
        $this->ensureAdmin();

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
            'trailer_url' => $this->youtubeTrailerRules(),
        ]);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }
        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }

        Film::create($validated);

        return redirect()->route('home')->with('success', 'Película creada exitosamente.');
    }

    public function edit(Film $film) {
        $this->ensureAdmin();

        return Inertia::render('Films/Edit', [
            'film' => $film,
        ]);
    }

    public function update(Request $request, Film $film) {
        $this->ensureAdmin();

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
            'trailer_url' => $this->youtubeTrailerRules(),
        ]);

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

        $film->update($validated);

        return redirect()->route('home')->with('success', 'Película actualizada exitosamente.');
    }

    public function destroy(Film $film) {
        $this->ensureAdmin();

        if ($film->logo) {
            Storage::disk('public')->delete($film->logo);
        }

        if ($film->poster) {
            Storage::disk('public')->delete($film->poster);
        }

        $film->delete();

        return redirect()->route('home')->with('success', 'Película eliminada exitosamente.');
    }

    private function youtubeTrailerRules(): array {
        return [
            'required',
            'string',
            'max:2048',
            'regex:'.self::YOUTUBE_URL_REGEX,
        ];
    }

    private function ensureAdmin(): void {
        abort_unless(Auth::user()?->role === 'admin', 403);
    }
}
