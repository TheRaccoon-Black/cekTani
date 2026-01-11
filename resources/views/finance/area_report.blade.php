@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-1 mb-3">
        <span class="text-muted fw-light">Keuangan /</span> Laporan Profitabilitas Wilayah
    </h4>

    <div class="card mb-4 sticky-top shadow-sm" style="z-index: 90; top: 10px;">
        <div class="card-body p-3">
            <form action="{{ route('finance.area_report') }}" method="GET" class="row g-2 align-items-center">

                <div class="col-md-3">
                    <label class="form-label d-none d-md-block text-muted text-xs">Pilih Lahan</label>
                    <select name="land_id" id="landSelect" class="form-select fw-bold text-primary border-primary" onchange="this.form.submit()">
                        <option value="">-- Pilih Lahan Utama --</option>
                        @foreach($lands as $l)
                            <option value="{{ $l->id }}" {{ $landId == $l->id ? 'selected' : '' }}>
                                🏢 {{ $l->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-none d-md-block text-muted text-xs">Filter Sektor</label>
                    <select name="sector_id" class="form-select" onchange="this.form.submit()" {{ !$landId ? 'disabled' : '' }}>
                        <option value="">-- Semua Sektor --</option>
                        @if($landId)
                            @foreach($lands->find($landId)->sectors as $s)
                                <option value="{{ $s->id }}" {{ $sectorId == $s->id ? 'selected' : '' }}>
                                    ⏹ {{ $s->name }} ({{ number_format($s->area_size) }} m²)
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label d-none d-md-block text-muted text-xs">Dari</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-2">
                    <label class="form-label d-none d-md-block text-muted text-xs">Sampai</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" onchange="this.form.submit()">
                </div>

                <div class="col-md-2 text-end d-flex align-items-end justify-content-end h-100">
                    <a href="{{ route('finance.area_report') }}" class="btn btn-label-secondary w-100" title="Reset Filter">
                        <i class="bx bx-refresh me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($landId)

        <div class="row mb-4 g-3">

            <div class="col-lg-8">
                <div class="card h-100 border-start border-4 border-primary">
                    <div class="card-header border-bottom py-2 d-flex justify-content-between align-items-center bg-light">
                        <div>
                            <h6 class="mb-0 fw-bold text-primary">{{ $selectedAreaName }}</h6>
                            <small class="text-muted">Luas Area: {{ number_format($totalAreaSize, 0, ',', '.') }} m²</small>
                        </div>

                        @if($allocationNote)
                            <span class="badge bg-label-warning text-wrap text-end" style="max-width: 200px; font-size: 10px; line-height: 1.2;">
                                <i class="bx bx-info-circle me-1"></i> {{ $allocationNote }}
                            </span>
                        @else
                            <span class="badge bg-label-secondary" style="font-size: 10px;">Data Real Tanpa Alokasi</span>
                        @endif
                    </div>

                    <div class="card-body pt-4">
                        <div class="row text-center">

                            <div class="col-4 border-end position-relative">
                                <span class="d-block text-muted text-xs uppercase fw-bold mb-1">Total Pemasukan</span>
                                <span class="text-success fw-bold fs-4">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>

                                @if($allocatedIncome > 0)
                                    <div class="text-xs text-muted mt-2 border-top pt-1 d-inline-block text-start">
                                        <div class="d-flex justify-content-between gap-2">
                                            <span>Langsung:</span> <strong>{{ number_format($directIncome/1000, 0) }}k</strong>
                                        </div>
                                        <div class="d-flex justify-content-between gap-2 text-warning">
                                            <span>+ Alokasi:</span> <strong>{{ number_format($allocatedIncome/1000, 0) }}k</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-4 border-end">
                                <span class="d-block text-muted text-xs uppercase fw-bold mb-1">Total Pengeluaran</span>
                                <span class="text-danger fw-bold fs-4">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>

                                @if($allocatedExpense > 0)
                                    <div class="text-xs text-muted mt-2 border-top pt-1 d-inline-block text-start">
                                        <div class="d-flex justify-content-between gap-2">
                                            <span>Langsung:</span> <strong>{{ number_format($directExpense/1000, 0) }}k</strong>
                                        </div>
                                        <div class="d-flex justify-content-between gap-2 text-warning">
                                            <span>+ Alokasi:</span> <strong>{{ number_format($allocatedExpense/1000, 0) }}k</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-4">
                                <span class="d-block text-muted text-xs uppercase fw-bold mb-1">Laba Bersih</span>
                                <h3 class="mb-0 {{ $profit >= 0 ? 'text-primary' : 'text-danger' }}">
                                    Rp {{ number_format($profit, 0, ',', '.') }}
                                </h3>
                                <small class="text-muted">
                                    {{ $totalIncome > 0 ? round(($profit/$totalIncome)*100, 1) : 0 }}% Margin
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100 bg-label-secondary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-white text-secondary"><i class="bx bx-ruler"></i></span>
                            </div>
                            <h6 class="card-title text-secondary mb-0">Analisis Produktivitas</h6>
                        </div>

                        <div class="bg-white p-3 rounded shadow-sm">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Biaya per m²:</span>
                                <span class="fw-bold text-danger">Rp {{ number_format($expensePerMeter, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Omzet per m²:</span>
                                <span class="fw-bold text-success">Rp {{ number_format($incomePerMeter, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-2 border-dashed">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-bold">Profit per m²:</span>
                                <span class="badge {{ $incomePerMeter - $expensePerMeter >= 0 ? 'bg-primary' : 'bg-danger' }}">
                                    Rp {{ number_format($incomePerMeter - $expensePerMeter, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2 fst-italic text-xs">
                            *Indikator efisiensi penggunaan lahan. Semakin tinggi profit/m², semakin produktif.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Rincian Transaksi Terkait</h5>
                <span class="badge bg-label-primary">{{ $transactions->count() }} Data</span>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Konteks Aset (Siklus/Bed)</th>
                            <th class="text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr>
                            <td style="width: 15%;">
                                <span class="fw-semibold">{{ \Carbon\Carbon::parse($t->transaction_date)->format('d M Y') }}</span>
                            </td>

                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-heading">{{ $t->category }}</span>
                                    @if($t->description)
                                        <small class="text-muted text-wrap" style="max-width: 300px; font-size: 0.8rem;">
                                            {{ Str::limit($t->description, 60) }}
                                        </small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if($t->bed)
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-info me-2">
                                            <i class="bx bx-grid-small"></i> {{ $t->bed->name }}
                                        </span>

                                        @if($t->plantingCycle)
                                            <div class="d-flex flex-column lh-1 ms-1">
                                                <small class="fw-bold text-success d-flex align-items-center">
                                                    <i class="bx bx-leaf me-1"></i> {{ $t->plantingCycle->commodity->name }}
                                                </small>
                                                @php
                                                    $logDate = \Carbon\Carbon::parse($t->transaction_date);
                                                    $startDate = \Carbon\Carbon::parse($t->plantingCycle->start_date);
                                                    $age = $startDate->diffInDays($logDate, false);
                                                @endphp
                                                <small class="text-muted" style="font-size: 10px;">
                                                    Umur saat itu: {{ $age >= 0 ? $age . ' HST' : 'Pre-tanam' }}
                                                </small>
                                            </div>
                                        @else
                                            <small class="text-muted fst-italic">Biaya Bedengan Umum</small>
                                        @endif
                                    </div>
                                @elseif($t->sector)
                                    <span class="badge bg-label-secondary">
                                        <i class="bx bx-grid-alt me-1"></i> Sektor {{ $t->sector->name }}
                                    </span>
                                @else
                                    <span class="badge bg-label-warning">
                                        <i class="bx bx-map me-1"></i> Lahan Umum
                                    </span>
                                    <small class="d-block text-xs text-muted mt-1">(Biaya ini dialokasikan ke sektor)</small>
                                @endif
                            </td>

                            <td class="text-end">
                                @if($t->type == 'income')
                                    <span class="fw-bold text-success">
                                        + Rp {{ number_format($t->amount, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="fw-bold text-danger">
                                        - Rp {{ number_format($t->amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="mb-3">
                                    <span class="avatar avatar-xl rounded-circle bg-label-secondary p-4">
                                        <i class="bx bx-search-alt fs-1"></i>
                                    </span>
                                </div>
                                <h6 class="text-muted">Tidak ada transaksi ditemukan.</h6>
                                <p class="small text-muted">Coba ubah filter tanggal atau pilih area lain.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 text-center">
                <div class="card p-5 shadow-sm">
                    <div class="mb-4">
                        <span class="avatar avatar-xl rounded-circle bg-label-primary p-4">
                            <i class="bx bx-bar-chart-alt-2 fs-1"></i>
                        </span>
                    </div>
                    <h4>Analisis Keuangan Wilayah</h4>
                    <p class="text-muted">
                        Pilih <strong>Lahan</strong> di menu filter bagian atas untuk mulai melihat laporan profitabilitas, efisiensi area, dan rincian transaksi secara spesifik.
                    </p>
                    <div class="alert alert-info d-inline-block text-start text-sm">
                        <i class="bx bx-bulb me-1"></i> <strong>Fitur Cerdas:</strong><br>
                        Biaya umum Lahan akan otomatis dialokasikan ke Sektor berdasarkan persentase luas area.
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
