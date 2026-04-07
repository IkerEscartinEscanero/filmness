<?php

namespace App\Http\Controllers;
use App\Models\Film;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function index() {
        // Todas las películas de la base de datos y del seeder
        $films = Film::all();

        return Inertia::render('Home', [
            'movies' => $films,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
