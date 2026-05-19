<script setup>
import Layout from '@/Layouts/Layout.vue';
import DeleteFilmModal from '@/Components/DeleteFilmModal.vue';
import MovieSessionAdminForm from '@/Components/Movies/MovieSessionAdminForm.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import VChart from 'vue-echarts';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { LineChart, PieChart } from 'echarts/charts';
import { GridComponent, TooltipComponent, LegendComponent } from 'echarts/components';

use([CanvasRenderer, LineChart, PieChart, GridComponent, TooltipComponent, LegendComponent]);

const props = defineProps({
    billboardFilms: Array,
    upcomingFilms: Array,
    kpis: Object,
    topFilmsRevenue: Array,
    monthlyRevenueSeries: Array,
    rooms: Array,
});

const showSessionsModal = ref(false);
const selectedFilmId = ref(null);
const showDeleteFilmModal = ref(false);
const filmIdToDelete = ref(null);
const showDeleteSessionModal = ref(false);
const sessionIdToDelete = ref(null);

const selectedFilm = computed(() => {
    if (!selectedFilmId.value) return null;

    const films = [...(props.billboardFilms ?? []), ...(props.upcomingFilms ?? [])];

    return films.find((film) => film.id === selectedFilmId.value) ?? null;
});

const openSessionsModal = (film) => {
    selectedFilmId.value = film.id;
    showSessionsModal.value = true;
};

const closeSessionsModal = () => {
    showSessionsModal.value = false;
    selectedFilmId.value = null;
};

const goToCreateFilm = () => {
    router.visit('/films/create');
};

const goToEditFilm = (filmId) => {
    router.visit(`/films/${filmId}/edit`);
};

const askDeleteFilm = (filmId) => {
    filmIdToDelete.value = filmId;
    showDeleteFilmModal.value = true;
};

const cancelDeleteFilm = () => {
    showDeleteFilmModal.value = false;
    filmIdToDelete.value = null;
};

const confirmDeleteFilm = () => {
    if (!filmIdToDelete.value) return;

    router.delete(`/films/${filmIdToDelete.value}`, {
        onFinish: () => {
            cancelDeleteFilm();
        },
    });
};

const goToManageSeats = (sessionId) => {
    router.visit(`/sessions/${sessionId}/seats`);
};

const askDeleteSession = (sessionId) => {
    sessionIdToDelete.value = sessionId;
    showDeleteSessionModal.value = true;
};

const cancelDeleteSession = () => {
    showDeleteSessionModal.value = false;
    sessionIdToDelete.value = null;
};

const confirmDeleteSession = () => {
    if (!sessionIdToDelete.value) return;

    router.delete(`/sessions/${sessionIdToDelete.value}`, {
        onFinish: () => {
            cancelDeleteSession();
        },
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

const formatDateTime = (date) => {
    return new Date(date).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const lineChartOption = computed(() => {
    const revenuePoints = (props.monthlyRevenueSeries ?? []).map((item) => ({
        label: item.label,
        value: Number(item.revenue ?? 0),
    }));

    return {
        backgroundColor: 'transparent',
        tooltip: {
            trigger: 'axis',
            valueFormatter: (value) => formatCurrency(Number(value ?? 0)),
            backgroundColor: '#0F172A',
            borderColor: '#334155',
            textStyle: { color: '#E2E8F0' },
        },
        grid: {
            top: 20,
            right: 16,
            bottom: 24,
            left: 52,
        },
        xAxis: {
            type: 'category',
            data: revenuePoints.map((point) => point.label),
            axisLine: { lineStyle: { color: '#334155' } },
            axisLabel: { color: '#94A3B8' },
        },
        yAxis: {
            type: 'value',
            axisLine: { lineStyle: { color: '#334155' } },
            splitLine: { lineStyle: { color: '#1E293B' } },
            axisLabel: {
                color: '#94A3B8',
                formatter: (value) => `€${value}`,
            },
        },
        series: [
            {
                name: 'Ingresos mensuales',
                type: 'line',
                smooth: true,
                symbol: 'circle',
                symbolSize: 8,
                data: revenuePoints.map((point) => point.value),
                lineStyle: {
                    width: 3,
                    color: '#22C55E',
                },
                itemStyle: {
                    color: '#22C55E',
                    borderColor: '#86EFAC',
                    borderWidth: 1,
                },
                areaStyle: {
                    color: 'rgba(34, 197, 94, 0.14)',
                },
            },
        ],
    };
});

const donutData = computed(() => [
    {
        value: Number(props.kpis?.billboardCount ?? 0),
        name: 'En cartelera',
        itemStyle: { color: '#F59E0B' },
    },
    {
        value: Number(props.kpis?.upcomingCount ?? 0),
        name: 'Próximos estrenos',
        itemStyle: { color: '#38BDF8' },
    },
]);

const donutChartOption = computed(() => ({
    backgroundColor: 'transparent',
    tooltip: {
        trigger: 'item',
        formatter: ({ name, value }) => {
            const total = donutData.value.reduce((sum, item) => sum + Number(item.value ?? 0), 0);
            const percent = total > 0 ? Math.round((Number(value) / total) * 100) : 0;
            return `${name}: ${value} (${percent}%)`;
        },
        backgroundColor: '#0F172A',
        borderColor: '#334155',
        textStyle: { color: '#E2E8F0' },
    },
    legend: {
        bottom: 0,
        textStyle: {
            color: '#CBD5E1',
        },
    },
    series: [
        {
            type: 'pie',
            radius: ['52%', '72%'],
            center: ['50%', '48%'],
            avoidLabelOverlap: true,
            itemStyle: {
                borderColor: '#0F172A',
                borderWidth: 4,
            },
            label: {
                show: true,
                color: '#E2E8F0',
                formatter: '{d}%',
                fontWeight: 'bold',
            },
            labelLine: {
                show: true,
                length: 10,
                length2: 8,
                lineStyle: {
                    color: '#94A3B8',
                },
            },
            data: donutData.value,
        },
    ],
}));
</script>

<template>
    <Layout>
        <Head title="Panel administrativo" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <div class="mx-auto max-w-7xl px-6 py-10">
                <!-- Header -->
                <div class="mb-12">
                    <h1 class="text-4xl font-bold text-white mb-2">Panel administrativo</h1>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-12">
                    <section class="xl:col-span-2 rounded-2xl border border-slate-700 bg-slate-800/80 p-5">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-widest text-slate-400">Tendencia semestral</p>
                                <h2 class="text-xl font-semibold text-white">Ingresos mensuales</h2>
                            </div>
                            <div class="flex gap-2">
                                <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-2">
                                    <p class="text-[11px] uppercase tracking-wide text-emerald-300">Ingresos obtenidos</p>
                                    <p class="text-sm font-semibold text-white text-right">{{ formatCurrency(kpis.monthlyRevenue) }}</p>
                                </div>
                                <div class="rounded-lg border border-sky-500/30 bg-sky-500/10 px-3 py-2">
                                    <p class="text-[11px] uppercase tracking-wide text-sky-300">Entradas vendidas</p>
                                    <p class="text-sm font-semibold text-white text-right">{{ kpis.monthlyTickets }}</p>
                                </div>
                            </div>
                        </div>
                        <VChart
                            style="height: 290px; width: 100%;"
                            :option="lineChartOption"
                            :update-options="{ notMerge: true, lazyUpdate: true }"
                        />
                    </section>

                    <section class="rounded-2xl border border-slate-700 bg-slate-800/80 p-5">
                        <p class="text-xs uppercase tracking-widest text-slate-400">Distribución</p>
                        <h2 class="text-xl font-semibold text-white mb-4">Cartelera - Próximos estrenos</h2>
                        <VChart
                            style="height: 290px; width: 100%;"
                            :option="donutChartOption"
                            :update-options="{ notMerge: true, lazyUpdate: true }"
                        />
                    </section>
                </div>

                <!-- Películas en Cartelera Section -->
                <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 mb-12">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-white">Cartelera</h2>
                        <button
                            class="inline-flex items-center justify-center rounded-full bg-yellow-500 px-6 py-2 text-sm font-semibold text-slate-950 hover:bg-yellow-400 transition"
                            @click="goToCreateFilm"
                        >
                            Insertar película
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Director</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Género</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Estreno</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Sesiones</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700">
                                <tr v-for="film in billboardFilms" :key="film.id" class="hover:bg-slate-700/50 transition">
                                    <td class="px-6 py-4 text-sm text-white font-medium">{{ film.title }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ film.director || '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ film.genre }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ formatDate(film.release_date) }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">
                                        <span :class="film.movie_sessions.length > 0 ? 'text-slate-300' : 'text-slate-500'">
                                            {{ film.movie_sessions.length }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <button
                                            class="mr-4 text-blue-400 hover:text-blue-300"
                                            @click="openSessionsModal(film)"
                                        >
                                            Sesiones
                                        </button>
                                        <button class="text-yellow-400 hover:text-yellow-300 mr-4" @click="goToEditFilm(film.id)">
                                            Editar
                                        </button>
                                        <button class="text-red-400 hover:text-red-300" @click="askDeleteFilm(film.id)">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="billboardFilms.length === 0" class="text-center py-8 text-slate-400">
                            No hay películas en cartelera
                        </div>
                    </div>
                </div>

                <!-- Próximos Estrenos Section -->
                <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 mb-12">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-white">Próximos Estrenos</h2>
                        <button
                            class="inline-flex items-center justify-center rounded-full bg-yellow-500 px-6 py-2 text-sm font-semibold text-slate-950 hover:bg-yellow-400 transition"
                            @click="goToCreateFilm"
                        >
                            Insertar película
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Director</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Género</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Estreno</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Sesiones</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700">
                                <tr v-for="film in upcomingFilms" :key="film.id" class="hover:bg-slate-700/50 transition">
                                    <td class="px-6 py-4 text-sm text-white font-medium">{{ film.title }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ film.director || '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ film.genre }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ formatDate(film.release_date) }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">
                                        <span :class="film.movie_sessions.length > 0 ? 'text-slate-300' : 'text-slate-500'">
                                            {{ film.movie_sessions.length }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <button
                                            class="mr-4 text-blue-400 hover:text-blue-300"
                                            @click="openSessionsModal(film)"
                                        >
                                            Sesiones
                                        </button>
                                        <button class="text-yellow-400 hover:text-yellow-300 mr-4" @click="goToEditFilm(film.id)">
                                            Editar
                                        </button>
                                        <button class="text-red-400 hover:text-red-300" @click="askDeleteFilm(film.id)">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="upcomingFilms.length === 0" class="text-center py-8 text-slate-400">
                            No hay próximos estrenos
                        </div>
                    </div>
                </div>

                <!-- Top Films by Revenue -->
                <div v-if="topFilmsRevenue.length > 0" class="bg-slate-800 rounded-lg border border-slate-700 p-6">
                    <h2 class="text-2xl font-semibold text-white mb-6">Top 5 películas en función al número de ingresos</h2>
                    <div class="space-y-3">
                        <div v-for="(film, index) in topFilmsRevenue" :key="film.id" class="flex items-center justify-between bg-slate-700/50 p-4 rounded">
                            <div class="flex items-center gap-4">
                                <span class="text-yellow-400 font-bold text-lg">{{ index + 1 }}</span>
                                <span class="text-white font-medium">{{ film.title }}</span>
                            </div>
                            <span class="text-green-400 font-semibold">{{ formatCurrency(film.revenue) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Sessions Modal -->
        <div v-if="showSessionsModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-slate-800 rounded-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto border border-slate-700">
                <div class="sticky top-0 bg-slate-900 border-b border-slate-700 p-6 flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-white">Sesiones: {{ selectedFilm?.title }}</h3>
                    <button class="text-slate-400 hover:text-white text-2xl" @click="closeSessionsModal">×</button>
                </div>

                <div class="p-6">
                    <MovieSessionAdminForm v-if="selectedFilm" :film-id="selectedFilm.id" :rooms="rooms" />
                    
                    <div v-if="selectedFilm?.movie_sessions.length > 0" class="space-y-4 mt-6">
                        <h4 class="text-sm font-semibold text-slate-300 uppercase mb-4">Sesiones existentes</h4>
                        <div v-for="session in selectedFilm.movie_sessions" :key="session.id"
                            class="bg-slate-700/50 border border-slate-600 rounded p-4 flex justify-between items-center">
                            <div>
                                <p class="text-white font-medium">{{ session.room?.name }}</p>
                                <p class="text-slate-400 text-sm">{{ formatDateTime(session.date) }}</p>
                                <p class="text-slate-400 text-sm">Precio: {{ formatCurrency(session.price) }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="text-blue-400 hover:text-blue-300 text-sm px-3 py-2 bg-blue-500/20 rounded"
                                    @click="goToManageSeats(session.id)">
                                    Butacas
                                </button>
                                <button class="text-red-400 hover:text-red-300 text-sm px-3 py-2 bg-red-500/20 rounded"
                                    @click="askDeleteSession(session.id)">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-slate-400 mt-6">
                        Añade tu primera sesión para esta película
                    </div>
                </div>
            </div>
        </div>

        <DeleteFilmModal
            :show="showDeleteFilmModal"
            @cancel="cancelDeleteFilm"
            @confirm="confirmDeleteFilm"
        />

        <DeleteFilmModal
            :show="showDeleteSessionModal"
            title="¿Borrar sesión?"
            message="Esta acción no se puede deshacer. La sesión seleccionada será eliminada permanentemente."
            @cancel="cancelDeleteSession"
            @confirm="confirmDeleteSession"
        />
    </Layout>
</template>