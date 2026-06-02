<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Purchase;
use App\Models\Room;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index() {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $dbDriver = DB::connection()->getDriverName();
        $perPage = 8;

        $today = now()->startOfDay();

        // Films currently in the cinema
        $billboardFilms = Film::where('release_date', '<=', $today)
            ->orderByDesc('release_date')
            ->with(['movieSessions' => fn ($q) => $q->where('date', '>=', now())->with('room')])
            ->paginate($perPage, ['*'], 'billboard_page')
            ->withQueryString();

        // Upcoming films
        $upcomingFilms = Film::where('release_date', '>', $today)
            ->orderBy('release_date')
            ->with(['movieSessions' => fn ($q) => $q->where('date', '>=', now())->with('room')])
            ->paginate($perPage, ['*'], 'upcoming_page')
            ->withQueryString();

        // KPIs for the current month
        $currentMonthStart = now()->startOfMonth()->toDateString();
        $currentMonthEnd = now()->endOfMonth()->toDateString();

        // Total revenue in current month
        $monthlyRevenue = Purchase::whereDate('created_at', '>=', $currentMonthStart)
            ->whereDate('created_at', '<=', $currentMonthEnd)
            ->where('status', 'pagado')
            ->sum('total');

        // Total tickets sold in current month
        $monthlyTickets = Ticket::whereHas('purchase', function ($q) use ($currentMonthStart, $currentMonthEnd) {
            $q->whereDate('created_at', '>=', $currentMonthStart)
                ->whereDate('created_at', '<=', $currentMonthEnd)
                ->where('status', 'pagado');
        })->count();

        // Top 5 films per revenue from purchases in current month
        $topFilmsRevenue = Film::select('films.id', 'films.title', DB::raw('COALESCE(SUM(purchases.total), 0) as revenue'))
            ->leftJoin('movie_sessions', 'films.id', '=', 'movie_sessions.film_id')
            ->leftJoin('tickets', 'movie_sessions.id', '=', 'tickets.movie_session_id')
            ->leftJoin('purchases', 'tickets.purchase_id', '=', 'purchases.id')
            ->where(function ($q) use ($currentMonthStart, $currentMonthEnd) {
                $q->whereNull('purchases.id')
                    ->orWhere(function ($subq) use ($currentMonthStart, $currentMonthEnd) {
                        $subq->whereDate('purchases.created_at', '>=', $currentMonthStart)
                            ->whereDate('purchases.created_at', '<=', $currentMonthEnd)
                            ->where('purchases.status', 'pagado');
                    });
            })
            ->groupBy('films.id', 'films.title')
            ->having('revenue', '>', 0)
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(fn ($film) => [
                'id' => $film->id,
                'title' => $film->title,
                'revenue' => (float) $film->revenue,
            ]);

        // Monthly revenue series from last 6 months
        $seriesStart = now()->subMonths(5)->startOfMonth();

        $monthlyGrouping = $dbDriver === 'sqlite'
            ? "CAST(strftime('%Y', created_at) AS INTEGER) as year, CAST(strftime('%m', created_at) AS INTEGER) as month"
            : 'YEAR(created_at) as year, MONTH(created_at) as month';

        $monthlyRevenueRaw = Purchase::selectRaw($monthlyGrouping.', COALESCE(SUM(total), 0) as revenue')
            ->whereDate('created_at', '>=', $seriesStart->toDateString())
            ->where('status', 'pagado')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(fn ($item) => sprintf('%04d-%02d', $item->year, $item->month));

        $monthlyRevenueSeries = collect(range(0, 5))
            ->map(function ($offset) use ($seriesStart, $monthlyRevenueRaw) {
                $monthDate = $seriesStart->copy()->addMonths($offset);
                $key = $monthDate->format('Y-m');
                $monthData = $monthlyRevenueRaw->get($key);

                return [
                    'month' => $monthDate->format('Y-m-01'),
                    'label' => ucfirst($monthDate->translatedFormat('M Y')),
                    'revenue' => (float) ($monthData->revenue ?? 0),
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'billboardFilms' => $billboardFilms,
            'upcomingFilms' => $upcomingFilms,
            'kpis' => [
                'monthlyRevenue' => (float) $monthlyRevenue,
                'monthlyTickets' => $monthlyTickets,
                'billboardCount' => $billboardFilms->total(),
                'upcomingCount' => $upcomingFilms->total(),
            ],
            'topFilmsRevenue' => $topFilmsRevenue,
            'monthlyRevenueSeries' => $monthlyRevenueSeries,
            'rooms' => Room::all(),
        ]);
    }
}