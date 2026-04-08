<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('films')->insert([
            [
                'title' => 'Super Mario Galaxy: La Película',
                'logo' => 'logos/super_mario_galaxy_logo.jpg',
                'poster' => 'posters/super_mario_galaxy_poster.jpg',
                'release_date' => '2026-04-01',
                'director' => 'Michael Jelenic, Aaron Horvath',
                'genre' => 'Acción',
                'distribution' => 'Benny Safdie, Anya Taylor-Joy, Chris Pratt, Jack Black, Charlie Day, Keegan-Michael Key, Kevin Michael Richardson, Brie Larson',
                'synopsis' => 'La película tiene lugar después de los acontecimientos de la primera, en la que dos hermanos, Mario y Luigi, y la princesa Peach emprenden una aventura hasta los confines del espacio y a través de la galaxia.',
                'duration' => 98,
                'trailer' => 'trailers/Trailer_SMG_Pelicula.mp4',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
