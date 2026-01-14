@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold py-1 mb-0"><span class="text-muted fw-light">Financial /</span> Executive Dashboard</h4>
            <small class="text-muted">Analisis performa & kesehatan arus kas.</small>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('finance.index') }}" method="GET" class="d-flex gap-2">
                <select name="land_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Lahan</option>
                    @foreach($lands as $l)
                        <option value="{{ $l->id }}" {{ request('land_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="start_date" class="form-control form-select-sm" value="{{ $startDate }}" onchange="this.form.submit()">
                <input type="date" name="end_date" class="form-control form-select-sm" value="{{ $endDate }}" onchange="this.form.submit()">
            </form>
            <a href="{{ route('finance.create') }}" class="btn btn-primary btn-sm"><i class="bx bx-plus"></i></a>
        </div>
    </div>

    <div class="row mb-4 g-3">

        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text text-muted mb-1">Net Profit (Laba Bersih)</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2 {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }}">
                                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                                </h4>
                                <small class="{{ $profitGrowth >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                    <i class="bx {{ $profitGrowth >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                    {{ number_format(abs($profitGrowth), 1) }}%
                                </small>
                            </div>
                            <small class="text-muted">Dibandingkan periode lalu</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-wallet fs-3"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text text-muted mb-1">Total Pengeluaran</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2 text-danger">
                                    Rp {{ number_format($currentStats->expense, 0, ',', '.') }}
                                </h4>
                                <small class="{{ $expenseGrowth <= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                    <i class="bx {{ $expenseGrowth > 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                    {{ number_format(abs($expenseGrowth), 1) }}%
                                </small>
                            </div>
                            <small class="text-muted">Efisiensi Biaya</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-danger rounded p-2">
                                <i class="bx bx-trending-down fs-3"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="text-white text-opacity-75 mb-1">Potensi Panen (30 Hari)</p>
                            <h4 class="card-title mb-0 text-white">
                                ~ Rp {{ number_format($forecastIncome, 0, ',', '.') }}
                            </h4>
                            <small class="text-white text-opacity-75 mt-2 d-block">
                                Estimasi nilai tanaman aktif yang akan panen.
                            </small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-white bg-opacity-25 rounded p-2">
                                <i class="bx bx-radar fs-3 text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">

        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Tren Arus Kas</h5>
                        <small class="text-muted">Analisis Pemasukan vs Pengeluaran Harian</small>
                    </div>
                </div>
                <div class="card-body">
                    <div id="trendChart"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Struktur Biaya</h5>
                </div>
                <div class="card-body">
                    <div id="breakdownChart"></div>

                    <div class="mt-3">
                        @foreach($expenseBreakdown->take(3) as $exp)
                            @php
                                $percent = ($currentStats->expense > 0) ? ($exp->total / $currentStats->expense) * 100 : 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-chevron-right text-primary me-2"></i>
                                    <span class="text-muted">{{ $exp->category }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">{{ number_format($percent, 1) }}%</span>
                                    <small class="text-muted">Rp {{ number_format($exp->total/1000, 0) }}k</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Buku Besar (Detail)</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Tipe</th>
                        <th>Alokasi Lahan</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($transactions as $t)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d M Y') }}</td>
                        <td><span class="fw-bold">{{ $t->category }}</span></td>
                        <td>
                            <small class="text-muted" title="{{ $t->description }}">
                                {{ \Illuminate\Support\Str::limit($t->description, 30) }}
                            </small>
                        </td>
                        <td>
                            @if($t->type == 'income')
                                <span class="badge bg-label-success">Pemasukan</span>
                            @elseif($t->type == 'expense')
                                <span class="badge bg-label-danger">Pengeluaran</span>
                            @elseif($t->type == 'cost_allocation')
                                <span class="badge bg-label-warning">HPP / Internal</span>
                            @endif
                        </td>
                        <td>
                             <span class="badge bg-label-secondary fs-tiny">
                                {{ $t->land ? 'Lahan: ' . $t->land->name : 'Operasional Umum / Gudang' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($t->type == 'income')
                                <span class="fw-bold text-success">+ Rp {{ number_format($t->amount, 0, ',', '.') }}</span>
                            @elseif($t->type == 'expense')
                                <span class="fw-bold text-danger">- Rp {{ number_format($t->amount, 0, ',', '.') }}</span>
                            @elseif($t->type == 'cost_allocation')
                                {{-- Tampilkan kuning dan dalam kurung karena ini Non-Tunai --}}
                                <span class="fw-bold text-warning">(Rp {{ number_format($t->amount, 0, ',', '.') }})</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-icon btn-label-secondary"><i class="bx bx-pencil"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    // 1. TREND CHART (INCOME VS EXPENSE)
    const trendData = @json($dailyTrend);
    const trendOptions = {
        series: [
            { name: 'Pemasukan', data: trendData.map(d => d.income) },
            { name: 'Pengeluaran', data: trendData.map(d => d.expense) }
        ],
        chart: { height: 320, type: 'area', toolbar: { show: false } },
        colors: ['#71dd37', '#ff3e1d'],
        fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.1 } },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            categories: trendData.map(d => new Date(d.date).toLocaleDateString('id-ID', {day: '2-digit', month: 'short'})),
            axisBorder: { show: false }
        },
        yaxis: { labels: { formatter: (val) => (val/1000).toFixed(0) + 'k' } },
        grid: { borderColor: '#f1f1f1' }
    };
    new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

    // 2. BREAKDOWN EXPENSE (DONUT)
    const breakdownData = @json($expenseBreakdown);
    const breakdownOptions = {
        series: breakdownData.map(d => parseInt(d.total)),
        labels: breakdownData.map(d => d.category),
        chart: { type: 'donut', height: 280 },
        colors: ['#696cff', '#8592a3', '#ffab00', '#ff3e1d', '#03c3ec'],
        legend: { show: false }, // Kita pakai custom legend HTML
        plotOptions: {
            pie: { donut: { labels: { show: true, total: { show: true, label: 'Total', formatter: (w) => 'Rp ' + (w.globals.seriesTotals.reduce((a, b) => a + b, 0)/1000).toFixed(0) + 'k' } } } }
        }
    };
    new ApexCharts(document.querySelector("#breakdownChart"), breakdownOptions).render();
</script>
@endsection
