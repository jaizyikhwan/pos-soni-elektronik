<div class="overflow-y-auto">
    <!-- ================= RINGKASAN BULAN INI ================= -->
    <div class="max-w-7xl mx-auto px-4 mt-4 mb-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                Ringkasan Bulan Ini
            </h2>

            <span
                class="text-xs px-2 py-1 rounded-full
                       bg-zinc-100 dark:bg-zinc-800
                       text-zinc-600 dark:text-zinc-400">
                {{ now()->translatedFormat('F Y') }}
            </span>
        </div>
    </div>

    <!-- ================= DASHBOARD STATS ================= -->
    <div class="max-w-7xl mx-auto px-4 mb-8">
        <div class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 xl:grid-cols-4">

            <!-- Total Pendapatan -->
            <div
                class="flex items-center p-4 bg-white dark:bg-zinc-900 rounded-xl shadow border border-zinc-200 dark:border-zinc-800">
                <div class="p-2 sm:p-3 mr-3 sm:mr-4 rounded-full bg-[var(--color-accent)]/15 text-[var(--color-accent)]">
                    <flux:icon name="banknotes" class="w-4 h-4 sm:w-5 sm:h-5" />
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">
                        Total Pendapatan
                    </p>
                    <p class="text-base sm:text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Total Barang -->
            <div
                class="flex items-center p-4 bg-white dark:bg-zinc-900 rounded-xl shadow border border-zinc-200 dark:border-zinc-800">
                <div class="p-2 sm:p-3 mr-3 sm:mr-4 rounded-full bg-green-500/15 text-green-600 dark:text-green-400">
                    <flux:icon name="archive-box" class="w-4 h-4 sm:w-5 sm:h-5" />
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">
                        Total Barang
                    </p>
                    <p class="text-base sm:text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                        {{ $totalBarang }}
                    </p>
                </div>
            </div>

            <!-- Modal Stok -->
            <div
                class="flex items-center p-4 bg-white dark:bg-zinc-900 rounded-xl shadow border border-zinc-200 dark:border-zinc-800">
                <div class="p-2 sm:p-3 mr-3 sm:mr-4 rounded-full bg-yellow-500/15 text-yellow-600 dark:text-yellow-400">
                    <flux:icon name="tag" class="w-4 h-4 sm:w-5 sm:h-5" />
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">
                        Nilai Modal Stok
                    </p>
                    <p class="text-base sm:text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                        Rp {{ number_format($totalHargaBarang, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Total Keuntungan -->
            <div
                class="flex items-center p-4 bg-white dark:bg-zinc-900 rounded-xl shadow border border-zinc-200 dark:border-zinc-800">
                <div class="p-2 sm:p-3 mr-3 sm:mr-4 rounded-full bg-purple-500/15 text-purple-600 dark:text-purple-400">
                    <flux:icon name="chart-bar-square" class="w-4 h-4 sm:w-5 sm:h-5" />
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">
                        Total Keuntungan
                    </p>
                    <p class="text-base sm:text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                        Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= PENJUALAN HARI INI ================= -->
    <div class="max-w-7xl mx-auto px-4 mb-8">
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 sm:p-5 bg-white dark:bg-zinc-900 rounded-xl shadow border border-zinc-200 dark:border-zinc-800">
            <div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Penjualan Hari Ini
                </p>
                <p class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                    {{ $todayTransactionCount }} Transaksi
                </p>
            </div>

            <div class="flex gap-6">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Pendapatan</p>
                    <p class="text-base font-semibold text-zinc-800 dark:text-zinc-100">
                        Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Keuntungan</p>
                    <p class="text-base font-semibold text-green-600 dark:text-green-400">
                        Rp {{ number_format($todayProfit, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= CHART ================= -->
    <div class="max-w-7xl mx-auto px-4 pb-10">
        <h2 class="text-lg sm:text-xl font-bold mb-4 text-zinc-800 dark:text-zinc-100">
            Grafik Keuntungan Mingguan Bulan Ini
        </h2>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow border border-zinc-200 dark:border-zinc-800 p-4 sm:p-5">
            <div class="relative w-full h-[260px] sm:h-[350px]">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>


    @script
        <script>
            let lineChart;

            function cssVar(name) {
                return getComputedStyle(document.documentElement)
                    .getPropertyValue(name)
                    .trim();
            }

            function hexToRgba(hex, alpha) {
                if (!hex.startsWith('#')) return hex;
                const bigint = parseInt(hex.slice(1), 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }

            function renderLineChart() {
                const canvas = document.getElementById('lineChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                if (lineChart) lineChart.destroy();

                const accent = cssVar('--color-accent');
                const zinc400 = cssVar('--color-zinc-400');
                const zinc600 = cssVar('--color-zinc-600');
                const zinc800 = cssVar('--color-zinc-800');

                lineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'Keuntungan (Rp)',
                            data: @json($data),
                            borderColor: accent,
                            backgroundColor: hexToRgba(accent, 0.2),
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: accent,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: zinc600
                                }
                            },
                            tooltip: {
                                backgroundColor: cssVar('--color-zinc-100'),
                                titleColor: zinc800,
                                bodyColor: zinc800,
                                borderWidth: 1,
                                borderColor: cssVar('--color-zinc-200'),
                                callbacks: {
                                    label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: zinc400
                                },
                                grid: {
                                    color: cssVar('--color-zinc-200'),
                                    drawBorder: false
                                }
                            },
                            y: {
                                ticks: {
                                    color: zinc400,
                                    callback: value => 'Rp ' + value.toLocaleString('id-ID')
                                },
                                grid: {
                                    color: cssVar('--color-zinc-200'),
                                    drawBorder: false
                                }
                            }
                        }
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', () => renderLineChart());

            document.addEventListener('livewire:navigated', () => {
                renderLineChart();
            });

            Livewire.hook('commit', ({
                succeed
            }) => {
                succeed(() => {
                    renderLineChart();
                });
            });

            window.addEventListener('flux-theme-changed', () => {
                renderLineChart();
            });
        </script>
    @endscript
