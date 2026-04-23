<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a DB-level guard to prevent exact duplicate schedules per room.
     */
    public function up(): void
    {
        Schema::table('movie_sessions', function (Blueprint $table) {
            $table->unique(['room_id', 'date'], 'movie_sessions_room_date_unique');
        });
    }

    /**
     * Remove the unique constraint.
     */
    public function down(): void
    {
        Schema::table('movie_sessions', function (Blueprint $table) {
            $table->dropUnique('movie_sessions_room_date_unique');
        });
    }
};
