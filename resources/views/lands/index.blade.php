@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-4">
        <div>
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Operasional /</span> Manajemen Lahan
            </h4>
            <p class="text-muted mb-0">Kelola aset tanah, pemetaan sektor, dan monitoring lokasi.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('dashboard.map') }}" class="btn btn-outline-primary">
                <span class="tf-icons bx bx-map-alt me-1"></span> Mode Peta Global
            </a>
            <a href="{{ route('lands.create') }}" class="btn btn-primary">
                <span class="tf-icons bx bx-plus me-1"></span> Lahan Baru
            </a>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-normal mb-0 text-muted text-uppercase">Total Luas Aset</h6>
                        <span class="badge bg-label-primary rounded p-2"><i class="bx bx-area"></i></span>
                    </div>
                    <h3 class="mb-0">{{ number_format($totalAreaHa, 2) }} <small class="text-muted fs-6">Hektar</small></h3>
                    <small class="text-muted">Setara {{ number_format($totalAreaM2, 0, ',', '.') }} m²</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-normal mb-0 text-muted text-uppercase">Jumlah Lahan</h6>
                        <span class="badge bg-label-success rounded p-2"><i class="bx bx-map-pin"></i></span>
                    </div>
                    <h3 class="mb-0">{{ $totalLands }} <small class="text-muted fs-6">Lokasi</small></h3>
                    <small class="text-muted">Tersebar di berbagai titik</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card h-100 border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-normal mb-0 text-muted text-uppercase">Pembagian Area</h6>
                        <span class="badge bg-label-info rounded p-2"><i class="bx bx-grid-alt"></i></span>
                    </div>
                    <h3 class="mb-0">{{ $totalSectors }} <small class="text-muted fs-6">Sektor / Blok</small></h3>
                    <small class="text-muted">Total pecahan area tanam aktif</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">

        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="mb-0">Daftar Lahan</h5>

            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('lands.index') }}" method="GET" class="d-flex">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama lahan..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        @if($lands->isEmpty())
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <div class="avatar avatar-xl bg-label-secondary rounded-circle mx-auto p-4 mb-2">
                        <i class="bx bx-landscape fs-1"></i>
                    </div>
                </div>
                <h5>Data Lahan Kosong</h5>
                <p class="text-muted">Anda belum memetakan lahan pertanian. Mulai sekarang untuk manajemen yang lebih baik.</p>
                <a href="{{ route('lands.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Gambar Lahan Pertama
                </a>
            </div>
        @else
            <div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Identitas Lahan</th>
                <th>Status Operasional</th> <th>Pemanfaatan Area</th>
                <th>Profitabilitas (All Time)</th> <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @foreach($lands as $land)
            @php
                // 1. LOGIKA UTILISASI AREA
                $usedArea = $land->sectors->sum('area_size');
                $percentage = ($land->area_size > 0) ? ($usedArea / $land->area_size) * 100 : 0;
                $progressColor = $percentage > 90 ? 'bg-danger' : ($percentage > 50 ? 'bg-info' : 'bg-primary');

                // 2. LOGIKA STATUS AKTIF & POPULASI
                // Mengecek apakah ada bedengan di lahan ini yang punya activePlantingCycle
                $activePlants = 0;
                $isActive = false;

                foreach($land->sectors as $sector) {
                    foreach($sector->beds as $bed) {
                        if($bed->activePlantingCycle) {
                            $isActive = true;
                            $activePlants += $bed->activePlantingCycle->current_plant_count;
                        }
                    }
                }

                // 3. LOGIKA KEUANGAN PER LAHAN
                $income = $land->transactions->where('type', 'income')->sum('amount');
                $expense = $land->transactions->where('type', 'expense')->sum('amount');
                $profit = $income - $expense;
            @endphp
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-leaf"></i>
                            </span>
                        </div>
                        <div>
                            <strong class="text-heading d-block">{{ $land->name }}</strong>
                            <small class="text-muted d-flex align-items-center">
                                <i class="bx bx-map-pin me-1" style="font-size: 10px;"></i>
                                {{ Str::limit($land->address ?? 'Tanpa Alamat', 20) }}
                            </small>
                        </div>
                    </div>
                </td>

                <td>
                    @if($isActive)
                        <span class="badge bg-label-primary mb-1">
                            <i class="bx bx-loader-circle me-1 animate-spin"></i> Aktif Berproduksi
                        </span>
                        <div class="d-flex align-items-center text-primary" style="font-size: 0.8rem;">
                            <i class="bx bx-spa me-1"></i> {{ number_format($activePlants) }} Tanaman
                        </div>
                    @else
                        <span class="badge bg-label-secondary mb-1">
                            <i class="bx bx-sleepy me-1"></i> Istirahat / Kosong
                        </span>
                        <div class="text-muted" style="font-size: 0.8rem;">Tidak ada aktivitas tanam</div>
                    @endif
                </td>

                <td style="min-width: 150px;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-semibold">{{ $land->sectors->count() }} Sektor</small>
                        <small class="text-muted">{{ number_format($percentage, 0) }}% Area</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                    </div>
                    <small class="text-muted d-block mt-1">
                        Luas: {{ number_format($land->area_size, 0, ',', '.') }} m²
                    </small>
                </td>

                <td>
                    <div class="d-flex flex-column">
                        <span class="fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($profit, 0, ',', '.') }}
                        </span>
                        <small class="text-muted" style="font-size: 10px;">
                            <span class="text-success"><i class="bx bx-up-arrow-alt"></i> {{ number_format($income/1000, 0) }}k</span> |
                            <span class="text-danger"><i class="bx bx-down-arrow-alt"></i> {{ number_format($expense/1000, 0) }}k</span>
                        </small>
                    </div>
                </td>

                <td class="text-end">
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="dropdown-header text-uppercase text-muted fs-tiny">Navigasi</div>
                            <a class="dropdown-item" href="{{ route('lands.show', $land->id) }}">
                                <i class="bx bx-map-alt me-2 text-primary"></i> Peta Satelit
                            </a>
                            <a class="dropdown-item" href="{{ route('lands.map_sectors', $land->id) }}">
                                <i class="bx bx-grid-alt me-2 text-info"></i> Layout Sektor
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('lands.edit', $land->id) }}">
                                <i class="bx bx-edit-alt me-2"></i> Edit Info
                            </a>
                            <form action="{{ route('lands.destroy', $land->id) }}" method="POST" onsubmit="return confirm('Hapus lahan?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bx bx-trash me-2"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

            <div class="card-footer py-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Menampilkan {{ $lands->firstItem() }} - {{ $lands->lastItem() }} dari {{ $lands->total() }} data</small>
                    {{ $lands->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
