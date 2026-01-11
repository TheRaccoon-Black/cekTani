@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-1 mb-4">
        <span class="text-muted fw-light">Analisis /</span> A/B Testing & Komparasi
    </h4>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('analysis.comparison') }}" method="GET">
                <div class="row align-items-end g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-primary">Siklus A (Acuan)</label>
                        <select name="cycle_a" class="form-select" required>
                            <option value="">-- Pilih Siklus --</option>
                            @foreach($cycles as $c)
                                <option value="{{ $c->id }}" {{ request('cycle_a') == $c->id ? 'selected' : '' }}>
                                    {{ $c->commodity->name }} ({{ $c->bed->name }}) - {{ \Carbon\Carbon::parse($c->start_date)->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 text-center d-none d-md-block">
                        <div class="avatar avatar-md mx-auto bg-label-secondary rounded-circle">
                            <span class="fs-4">VS</span>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold text-danger">Siklus B (Pembanding)</label>
                        <select name="cycle_b" class="form-select">
                            <option value="">-- Pilih Siklus --</option>
                            @foreach($cycles as $c)
                                <option value="{{ $c->id }}" {{ request('cycle_b') == $c->id ? 'selected' : '' }}>
                                    {{ $c->commodity->name }} ({{ $c->bed->name }}) - {{ \Carbon\Carbon::parse($c->start_date)->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 text-center mt-3">
                        <a href="{{ route('analysis.comparison') }}" class="btn btn-label-secondary me-2">Reset</a>
                        <button type="submit" class="btn btn-primary px-5"><i class="bx bx-analyse me-1"></i> Bandingkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($dataA && $dataB)
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-top border-4 border-primary text-center">
                    <div class="card-body">
                        <h5 class="text-primary mb-1">{{ $dataA['title'] }}</h5>
                        <small class="text-muted d-block">{{ $dataA['subtitle'] }}</small>
                        <span class="badge bg-label-primary mt-2">{{ $dataA['date_info'] }}</span>

                        <div class="row mt-4">
                            <div class="col-6 border-end">
                                <small class="text-muted text-uppercase font-xs">Net Profit</small>
                                <h4 class="mb-0 {{ $dataA['net_profit'] > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($dataA['net_profit']/1000, 0) }}k
                                </h4>
                            </div>
                            <div class="col-6">
                                <small class="text-muted text-uppercase font-xs">ROI</small>
                                <h4 class="mb-0 fw-bold">{{ number_format($dataA['roi'], 1) }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-top border-4 border-danger text-center">
                    <div class="card-body">
                        <h5 class="text-danger mb-1">{{ $dataB['title'] }}</h5>
                        <small class="text-muted d-block">{{ $dataB['subtitle'] }}</small>
                        <span class="badge bg-label-danger mt-2">{{ $dataB['date_info'] }}</span>

                        <div class="row mt-4">
                            <div class="col-6 border-end">
                                <small class="text-muted text-uppercase font-xs">Net Profit</small>
                                <h4 class="mb-0 {{ $dataB['net_profit'] > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($dataB['net_profit']/1000, 0) }}k
                                </h4>
                            </div>
                            <div class="col-6">
                                <small class="text-muted text-uppercase font-xs">ROI</small>
                                <h4 class="mb-0 fw-bold">{{ number_format($dataB['roi'], 1) }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Peta Kekuatan (Radar)</h5>
                        <small class="text-muted">Semakin luas area, semakin unggul.</small>
                    </div>
                    <div class="card-body">
                        <div id="radarChart" style="min-height: 300px;"></div>
                        <div class="text-center mt-3 small text-muted">
                            <span class="text-primary">● Siklus A</span> vs <span class="text-danger">● Siklus B</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Matriks Perbandingan Rinci</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-primary w-25">Siklus A</th>
                                    <th class="w-50">Indikator</th>
                                    <th class="text-danger w-25">Siklus B</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-lighter">
                                    <td colspan="3" class="text-start fw-bold text-xs text-uppercase ps-3">1. Profitabilitas & Efisiensi</td>
                                </tr>
                                <tr>
                                    <td class="{{ $dataA['cost_per_plant'] < $dataB['cost_per_plant'] ? 'bg-green-soft fw-bold text-success' : '' }}">
                                        Rp {{ number_format($dataA['cost_per_plant'], 0) }}
                                    </td>
                                    <td><small class="text-muted">Biaya per Pohon (Cost)</small></td>
                                    <td class="{{ $dataB['cost_per_plant'] < $dataA['cost_per_plant'] ? 'bg-green-soft fw-bold text-success' : '' }}">
                                        Rp {{ number_format($dataB['cost_per_plant'], 0) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="{{ $dataA['profit_per_plant'] > $dataB['profit_per_plant'] ? 'bg-green-soft fw-bold text-success' : '' }}">
                                        Rp {{ number_format($dataA['profit_per_plant'], 0) }}
                                    </td>
                                    <td><small class="text-muted">Profit per Pohon</small></td>
                                    <td class="{{ $dataB['profit_per_plant'] > $dataA['profit_per_plant'] ? 'bg-green-soft fw-bold text-success' : '' }}">
                                        Rp {{ number_format($dataB['profit_per_plant'], 0) }}
                                    </td>
                                </tr>

                                <tr class="bg-lighter">
                                    <td colspan="3" class="text-start fw-bold text-xs text-uppercase ps-3">2. Operasional</td>
                                </tr>
                                <tr>
                                    <td class="{{ $dataA['survival_rate'] > $dataB['survival_rate'] ? 'text-success fw-bold' : '' }}">
                                        {{ number_format($dataA['survival_rate'], 1) }}%
                                    </td>
                                    <td><small class="text-muted">Daya Hidup (Survival Rate)</small></td>
                                    <td class="{{ $dataB['survival_rate'] > $dataA['survival_rate'] ? 'text-success fw-bold' : '' }}">
                                        {{ number_format($dataB['survival_rate'], 1) }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rp {{ number_format($dataA['daily_burn_rate']/1000, 0) }}k</td>
                                    <td><small class="text-muted">Biaya Harian (Burn Rate)</small></td>
                                    <td>Rp {{ number_format($dataB['daily_burn_rate']/1000, 0) }}k</td>
                                </tr>

                                <tr class="bg-lighter">
                                    <td colspan="3" class="text-start fw-bold text-xs text-uppercase ps-3">3. Struktur Biaya (Dominan)</td>
                                </tr>
                                <tr>
                                    <td>Rp {{ number_format($dataA['breakdown']['pupuk']/1000, 0) }}k</td>
                                    <td><small class="text-muted">Total Pupuk</small></td>
                                    <td>Rp {{ number_format($dataB['breakdown']['pupuk']/1000, 0) }}k</td>
                                </tr>
                                <tr>
                                    <td>Rp {{ number_format($dataA['breakdown']['tenaga']/1000, 0) }}k</td>
                                    <td><small class="text-muted">Total Upah Kerja</small></td>
                                    <td>Rp {{ number_format($dataB['breakdown']['tenaga']/1000, 0) }}k</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @elseif(request('cycle_a'))
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="bx bx-info-circle me-2"></i>
            <div>
                Anda telah memilih <strong>{{ $dataA['title'] }}</strong>. Silakan pilih "Siklus B" di atas untuk mulai membandingkan.
            </div>
        </div>
    @endif

</div>

<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<style>
    .bg-green-soft { background-color: #e8fadf !important; }
    .bg-lighter { background-color: #f9f9f9; }
</style>

@if($dataA && $dataB)
<script>
    // Normalisasi Data untuk Radar Chart (Skala 0-100)
    // Agar grafik terlihat proporsional meskipun nilainya beda jauh
    function normalize(valA, valB) {
        let max = Math.max(valA, valB);
        if (max === 0) return [0, 0];
        return [ (valA / max) * 100, (valB / max) * 100 ];
    }

    let profitScore = normalize({{ $dataA['net_profit'] }}, {{ $dataB['net_profit'] }});
    let roiScore = normalize({{ $dataA['roi'] }}, {{ $dataB['roi'] }});
    let survivalScore = normalize({{ $dataA['survival_rate'] }}, {{ $dataB['survival_rate'] }});
    // Cost Efficiency: Biaya makin RENDAH makin BAGUS. Jadi dibalik.
    let costScore = normalize(1/({{ $dataA['cost_per_plant'] }}+1), 1/({{ $dataB['cost_per_plant'] }}+1));

    const radarOptions = {
        series: [{
            name: 'Siklus A',
            data: [profitScore[0], roiScore[0], survivalScore[0], costScore[0]]
        }, {
            name: 'Siklus B',
            data: [profitScore[1], roiScore[1], survivalScore[1], costScore[1]]
        }],
        chart: {
            height: 350,
            type: 'radar',
            toolbar: { show: false }
        },
        labels: ['Total Profit', 'ROI %', 'Daya Hidup', 'Hemat Biaya'],
        stroke: { width: 2 },
        fill: { opacity: 0.2 },
        colors: ['#696cff', '#ff3e1d'],
        markers: { size: 4 },
        yaxis: { show: false }, // Sembunyikan angka axis agar tidak bingung (karena ini skor relatif)
        tooltip: {
            y: { formatter: function(val) { return val.toFixed(0) + " Poin"; } }
        }
    };

    const chart = new ApexCharts(document.querySelector("#radarChart"), radarOptions);
    chart.render();
</script>
@endif
@endsection
