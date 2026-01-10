@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card mb-4">
        <div class="card-body p-3">
            <form action="{{ route('dashboard') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pilih Lahan (Scope)</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-map"></i></span>
                        <select name="land_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Lahan (Global)</option>
                            @foreach($lands as $land)
                                <option value="{{ $land->id }}" {{ $selectedLandId == $land->id ? 'selected' : '' }}>
                                    {{ $land->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Periode Tahun</label>
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        @for($y = date('Y'); $y >= date('Y')-4; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-inline-block text-start">
                        <small class="text-muted d-block">Filter Aktif:</small>
                        <span class="badge bg-label-primary">
                            {{ $selectedLandId ? 'Lahan Spesifik' : 'Semua Lahan' }}
                        </span>
                        <span class="badge bg-label-info">Tahun {{ $selectedYear }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($alerts->count() > 0)
        <div class="alert alert-danger alert-dismissible shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-error-circle bx-md me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">{{ $alerts->count() }} Tanaman Perlu Dipanen!</h6>
                    <span>Segera cek area bedengan untuk menghindari pembusukan.</span>
                </div>
                <a href="#harvest-section" class="btn btn-sm btn-danger ms-auto">Lihat Detail</a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
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
                    <small class="text-muted">Total: Inc - Exp</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="card-title mb-0">Total Biaya</h6>
                        <span class="avatar p-2 rounded bg-label-danger"><i class="bx bx-wallet"></i></span>
                    </div>
                    <h4 class="mb-1 text-danger">Rp {{ number_format($expenseTotal / 1000, 0, ',', '.') }}k</h4>
                    <small class="text-muted">Modal Keluar</small>
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
                    <small class="text-muted">Efisiensi Modal</small>
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
                    <small class="text-muted">Dimana uang dihabiskan?</small>
                </div>
                <div class="card-body">
                    <div id="expenseChart" style="min-height: 250px;"></div>

                    <div class="mt-3">
                        <ul class="p-0 m-0">
                            @foreach($costCategories->take(3) as $index => $cat)
                            <li class="d-flex mb-3 pb-1 align-items-center">
                                <div class="badge bg-label-secondary me-3 rounded p-2"><i class="bx bx-money"></i></div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $cat }}</h6>
                                    </div>
                                    <div class="user-progress">
                                        <h6 class="mb-0 text-danger">Rp {{ number_format($costValues[$index] / 1000, 0) }}k</h6>
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
                <div class="card-header">
                    <h5 class="card-title mb-0">Inventaris & Komoditas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4 mb-4">
                        <div class="col-4">
                            <div class="text-center p-3 border rounded border-dashed">
                                <span class="badge bg-label-primary rounded p-1 mb-2"><i class="bx bx-map"></i></span>
                                <h5 class="mb-0">{{ $totalLands }}</h5>
                                <small>Lahan</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 border rounded border-dashed">
                                <span class="badge bg-label-info rounded p-1 mb-2"><i class="bx bx-grid-alt"></i></span>
                                <h5 class="mb-0">{{ $totalSectors }}</h5>
                                <small>Sektor</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 border rounded border-dashed">
                                <span class="badge bg-label-warning rounded p-1 mb-2"><i class="bx bx-layer"></i></span>
                                <h5 class="mb-0">{{ $totalBeds }}</h5>
                                <small>Bed</small>
                            </div>
                        </div>
                    </div>

                    <div class="bg-label-primary p-3 rounded">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-white text-primary"><i class="bx bx-crown"></i></span>
                            </div>
                            <div>
                                <small class="fw-bold text-primary">KOMODITAS TERPOPULER</small>
                                <h5 class="mb-0 text-primary">{{ $topCommodityName }}</h5>
                                <small>Mendominasi kebun Anda saat ini.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Analisa Pasca Panen (Last 5 Cycles)</h5>
                        <small class="text-muted">Evaluasi performa tanaman yang baru dipanen</small>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanaman / Lokasi</th>
                                <th>Populasi</th>
                                <th class="text-end">Omzet Total</th>
                                <th class="text-end">Profit Bersih</th>
                                <th class="text-end">Rev/Pohon</th> </tr>
                        </thead>
                        <tbody>
                            @forelse($harvestHistory as $h)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ substr($h->commodity->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block">{{ $h->commodity->name }}</span>
                                            <small class="text-muted" style="font-size: 10px;">{{ $h->bed->sector->name }} - {{ $h->bed->name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $h->initial_plant_count }} Pmt</td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($h->transactions->where('type','income')->sum('amount') / 1000, 0) }}k
                                </td>
                                <td class="text-end">
                                    <span class="badge {{ $h->real_profit >= 0 ? 'bg-label-success' : 'bg-label-danger' }}">
                                        Rp {{ number_format($h->real_profit / 1000, 0) }}k
                                    </span>
                                </td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($h->rev_per_plant, 0, ',', '.') }}
                                    <i class="bx {{ $h->rev_per_plant > 5000 ? 'bx-trending-up text-success' : 'bx-trending-down text-warning' }}"></i>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data panen yang selesai.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer border-top p-3">
                    <small class="text-muted"><i class="bx bx-info-circle"></i> <strong>Rev/Pohon</strong> adalah rata-rata pendapatan kotor per satu tanaman. Indikator kualitas hasil panen.</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
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
                                    <h6 class="mb-0 text-sm fw-bold">{{ $log->activity }}</h6>
                                    <small class="text-muted text-xs">{{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge badge-center rounded-pill bg-label-secondary w-px-20 h-px-20 me-1">
                                        <i class="bx bx-leaf" style="font-size: 10px;"></i>
                                    </span>
                                    <small class="text-muted">{{ $log->plantingCycle->commodity->name ?? '-' }} ({{ $log->plantingCycle->bed->name ?? '-' }})</small>
                                </div>

                                @if($log->notes)
                                    <div class="bg-label-secondary p-2 rounded mt-2 text-xs text-secondary">
                                        "{{ Str::limit($log->notes, 60) }}"
                                    </div>
                                @endif

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
    const isDarkStyle = false; 
    const borderColor = '#f5f5f9';
    const headingColor = '#566a7f';

    const cashflowEl = document.querySelector("#cashflowChart");
    if(cashflowEl) {
        new ApexCharts(cashflowEl, {
            series: [
                { name: 'Pemasukan', type: 'column', data: {!! json_encode($incomeData) !!} },
                { name: 'Pengeluaran', type: 'line', data: {!! json_encode($expenseData) !!} }
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

    const expenseEl = document.querySelector("#expenseChart");
    if(expenseEl) {
        const labels = {!! json_encode($costCategories) !!};
        const series = {!! json_encode($costValues) !!};

        if(series.length > 0) {
            new ApexCharts(expenseEl, {
                series: series,
                labels: labels,
                chart: { type: 'polarArea', height: 250 },
                stroke: { colors: ['#fff'] },
                fill: { opacity: 0.8 },
                colors: ['#696cff', '#03c3ec', '#ff3e1d', '#ffab00', '#71dd37'],
                legend: { position: 'bottom' },
                yaxis: { show: false }
            }).render();
        } else {
            expenseEl.innerHTML = "<div class='text-center text-muted py-5'>Belum ada data pengeluaran.</div>";
        }
    }

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
                    bar: {
                        horizontal: true,
                        barHeight: '50%',
                        borderRadius: 4
                    }
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
