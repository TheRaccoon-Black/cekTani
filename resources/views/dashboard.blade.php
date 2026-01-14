@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold py-1 mb-0"><span class="text-muted fw-light">Financial /</span> Executive Dashboard</h4>
            <small class="text-muted">Analisis performa & kesehatan arus kas.</small>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
                <select name="land_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Lahan</option>
                    @foreach($lands as $l)
                        <option value="{{ $l->id }}" {{ request('land_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y')-4; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            <a href="{{ route('finance.create') }}" class="btn btn-primary btn-sm" title="Catat Transaksi"><i class="bx bx-plus"></i></a>
        </div>
    </div>

    {{-- Alert Panen --}}
    @if($alerts->count() > 0)
        <div class="alert alert-danger alert-dismissible shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-error-circle bx-md me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">{{ $alerts->count() }} Tanaman Perlu Dipanen!</h6>
                    <span>Segera cek area bedengan untuk menghindari pembusukan.</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="card-title mb-0">Net Profit (YTD)</h6>
                        <span class="avatar p-2 rounded bg-label-success"><i class="bx bx-trending-up"></i></span>
                    </div>
                    <h4 class="mb-1 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($netProfit / 1000, 0, ',', '.') }}k
                    </h4>
                    <small class="text-muted">Cash Flow Bersih</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="card-title mb-0">Pengeluaran Tunai</h6> {{-- Label Diperjelas --}}
                        <span class="avatar p-2 rounded bg-label-danger"><i class="bx bx-wallet"></i></span>
                    </div>
                    {{-- Gunakan $cashExpenseTotal sesuai controller --}}
                    <h4 class="mb-1 text-danger">Rp {{ number_format($cashExpenseTotal / 1000, 0, ',', '.') }}k</h4>
                    <small class="text-muted">Uang Keluar Real</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="card-title mb-0">Avg. Cost / Pohon</h6>
                        <span class="avatar p-2 rounded bg-label-warning"><i class="bx bx-calculator"></i></span>
                    </div>
                    <h4 class="mb-1 text-warning">Rp {{ number_format($costPerPlant, 0, ',', '.') }}</h4>
                    <small class="text-muted">Biaya Produksi (HPP)</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="card-title mb-0">Okupansi Lahan</h6>
                        <span class="avatar p-2 rounded bg-label-info"><i class="bx bx-layer"></i></span>
                    </div>
                    <h4 class="mb-1 text-info">{{ number_format($occupancyRate, 1) }}%</h4>
                    <small class="text-muted">{{ $totalPlants }} Pohon Aktif</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Tren Arus Kas Bulanan</h5>
                        <small class="text-muted">Tahun {{ $selectedYear }}</small>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div id="cashflowChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Struktur Biaya</h5>
                    <small class="text-muted">Termasuk Pemakaian Stok</small>
                </div>
                <div class="card-body">
                    <div id="expenseChart" style="min-height: 250px;"></div>

                    <div class="mt-3">
                        <ul class="p-0 m-0">
                            @foreach($costCategories->take(3) as $index => $cat)
                                @php
                                    // Hitung total untuk persentase (Tunai + Stok)
                                    $totalAllCost = array_sum($costValues->toArray());
                                    $percent = ($totalAllCost > 0) ? ($costValues[$index] / $totalAllCost) * 100 : 0;
                                @endphp
                                <li class="d-flex mb-3 pb-1 align-items-center">
                                    <div class="badge bg-label-secondary me-3 rounded p-2"><i class="bx bx-money"></i></div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">{{ $cat }}</h6>
                                        </div>
                                        <div class="user-progress text-end">
                                            <h6 class="mb-0 text-danger">Rp {{ number_format($costValues[$index] / 1000, 0) }}k</h6>
                                            <small class="text-muted">{{ number_format($percent, 1) }}%</small>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Kesehatan Operasional</h5>
                    <small class="text-muted">Frekuensi Aktivitas & Masalah</small>
                </div>
                <div class="card-body">
                    <div id="logChart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Update Lapangan</h5>
                    <span class="badge bg-label-primary rounded-pill">Terbaru</span>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <ul class="timeline timeline-dashed">
                        @forelse($recentLogs as $log)
                        <li class="timeline-item timeline-item-transparent pb-4 border-left-dashed">
                            <span class="timeline-point-wrapper">
                                <span class="timeline-point {{ $log->phase == 'Hama & Penyakit' ? 'timeline-point-danger' : ($log->phase == 'Panen' ? 'timeline-point-success' : 'timeline-point-info') }}"></span>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0 text-sm fw-bold">{{ $log->activity_description ?? $log->activity }}</h6> {{-- Pastikan nama kolom benar --}}
                                    <small class="text-muted text-xs">{{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge badge-center rounded-pill bg-label-secondary w-px-20 h-px-20 me-1">
                                        <i class="bx bx-leaf" style="font-size: 10px;"></i>
                                    </span>
                                    <small class="text-muted">
                                        {{ $log->plantingCycle->commodity->name ?? '-' }}
                                        ({{ $log->plantingCycle->bed->name ?? '-' }})
                                    </small>
                                </div>
                                @if($log->photo_path)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $log->photo_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $log->photo_path) }}" alt="Bukti" class="rounded w-100 object-cover" style="height: 100px;">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-muted">Belum ada jurnal lapangan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const borderColor = '#f5f5f9';
    const headingColor = '#566a7f';

    // 1. CASHFLOW CHART
    const cashflowEl = document.querySelector("#cashflowChart");
    if(cashflowEl) {
        new ApexCharts(cashflowEl, {
            series: [
                { name: 'Pemasukan', type: 'column', data: {!! json_encode($incomeData) !!} },
                { name: 'Pengeluaran Tunai', type: 'line', data: {!! json_encode($expenseData) !!} }
            ],
            chart: { height: 300, type: 'line', toolbar: { show: false } },
            stroke: { width: [0, 4], curve: 'smooth' },
            colors: ['#71dd37', '#ff3e1d'],
            plotOptions: { bar: { columnWidth: '40%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            labels: {!! json_encode($chartLabels) !!},
            xaxis: {
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: headingColor, fontSize: '13px' } }
            },
            yaxis: {
                labels: { formatter: (val) => (val/1000).toFixed(0) + 'k' }
            },
            grid: { borderColor: borderColor }
        }).render();
    }

    // 2. EXPENSE BREAKDOWN (Termasuk Stok)
    const expenseEl = document.querySelector("#expenseChart");
    if(expenseEl) {
        const labels = {!! json_encode($costCategories) !!};
        const series = {!! json_encode($costValues) !!};

        if(series.length > 0) {
            new ApexCharts(expenseEl, {
                series: series.map(s => parseFloat(s)),
                labels: labels,
                chart: { type: 'donut', height: 250 },
                stroke: { colors: ['#fff'] },
                colors: ['#696cff', '#03c3ec', '#ff3e1d', '#ffab00', '#71dd37'],
                legend: { position: 'bottom', show: false }, // Legend custom di HTML
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: { donut: { labels: { show: true, total: { show: true, label: 'Total', formatter: (w) => 'Rp ' + (w.globals.seriesTotals.reduce((a, b) => a + b, 0)/1000).toFixed(0) + 'k' } } } }
                }
            }).render();
        } else {
            expenseEl.innerHTML = "<div class='text-center text-muted py-5'>Belum ada data pengeluaran.</div>";
        }
    }

    // 3. LOG CHART
    const logEl = document.querySelector("#logChart");
    if(logEl) {
        const logStats = {!! json_encode($logStats) !!};
        const categories = Object.keys(logStats);
        const data = Object.values(logStats);

        if(categories.length > 0) {
            new ApexCharts(logEl, {
                series: [{ name: 'Frekuensi', data: data }],
                chart: { type: 'bar', height: 250, toolbar: { show: false } },
                plotOptions: {
                    bar: { horizontal: true, barHeight: '50%', borderRadius: 4 }
                },
                colors: ['#03c3ec'],
                xaxis: { categories: categories },
                grid: { borderColor: borderColor }
            }).render();
        } else {
            logEl.innerHTML = "<div class='text-center text-muted py-5'>Belum ada catatan jurnal.</div>";
        }
    }
});
</script>
@endsection
