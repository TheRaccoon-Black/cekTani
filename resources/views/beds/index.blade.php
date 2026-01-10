@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold py-1 mb-0">
                <span class="text-muted fw-light">Manajemen Sektor /</span> Data Bedengan
            </h4>
            <div class="text-muted small">
                Lokasi: <strong>{{ $sector->land->name }}</strong> &rsaquo; <strong class="text-primary">{{ $sector->name }}</strong>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('lands.map_sectors', $sector->land_id) }}" class="btn btn-label-secondary">
                <span class="tf-icons bx bx-map-alt me-1"></span> Kembali ke Peta
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">

        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Bedengan</h5>
                    <span class="badge bg-label-primary">{{ $sector->beds->count() }} Unit</span>
                </div>

                @if($sector->beds->count() > 0)
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Bedengan</th>
                                    <th>Dimensi</th>
                                    <th>Kapasitas</th>
                                    <th>Status Tanam</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($sector->beds as $bed)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-secondary">
                                                    <i class="bx bx-grid-small"></i>
                                                </span>
                                            </div>
                                            <strong>{{ $bed->name }}</strong>
                                        </div>
                                        <small class="text-muted ms-4 ps-1">ID: #{{ $bed->id }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary">
                                            {{ $bed->length }}m &times; {{ $bed->width }}m
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">{{ $bed->max_capacity }}</span>
                                        <small class="text-muted">Pohon</small>
                                    </td>
                                    <td>
                                        @php
                                            $activeCycle = $bed->plantingCycles->where('status', 'active')->first();
                                        @endphp

                                        @if($activeCycle)
                                            @php
                                                $harvestDate = \Carbon\Carbon::parse($activeCycle->estimated_harvest_date);
                                                $daysLeft = now()->diffInDays($harvestDate, false);
                                                $statusColor = $daysLeft < 0 ? 'danger' : ($daysLeft < 7 ? 'warning' : 'success');
                                            @endphp

                                            <div class="d-flex flex-column">
                                                <a href="{{ route('cycles.show', $activeCycle->id) }}" class="fw-bold text-primary hover-underline">
                                                    {{ $activeCycle->commodity->name }}
                                                </a>
                                                <small class="text-{{ $statusColor }}">
                                                    @if($daysLeft < 0) Telat {{ abs((int)$daysLeft) }} Hari
                                                    @elseif($daysLeft == 0) Panen Hari Ini!
                                                    @else Panen {{ (int)$daysLeft }} Hari Lagi
                                                    @endif
                                                </small>
                                            </div>
                                        @else
                                            <a href="{{ route('cycles.create', $bed->id) }}" class="btn btn-xs btn-outline-primary">
                                                <i class="bx bx-plus me-1"></i> Mulai Tanam
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if($activeCycle)
                                                    <a class="dropdown-item" href="{{ route('cycles.show', $activeCycle->id) }}">
                                                        <i class="bx bx-book-open me-1 text-primary"></i> Jurnal Harian
                                                    </a>
                                                @else
                                                    <a class="dropdown-item" href="{{ route('cycles.create', $bed->id) }}">
                                                        <i class="bx bx-seedling me-1 text-success"></i> Tanam Baru
                                                    </a>
                                                @endif

                                                <a class="dropdown-item" href="{{ route('beds.history', $bed->id) }}">
                                                    <i class="bx bx-history me-1"></i> Riwayat Panen
                                                </a>
                                                <a class="dropdown-item" href="{{ route('beds.edit', $bed->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit Data
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('beds.destroy', $bed->id) }}" method="POST" onsubmit="return confirm('Hapus bedengan ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bx bx-trash me-1"></i> Hapus
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
                @else
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <span class="avatar avatar-xl rounded-circle bg-label-secondary p-4">
                                <i class="bx bx-layer-plus fs-1"></i>
                            </span>
                        </div>
                        <h6 class="text-muted">Belum ada bedengan di sektor ini.</h6>
                        <p class="small text-muted">Tambahkan bedengan baru melalui form di samping kanan.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="card-title text-white mb-0 text-sm text-uppercase"><i class="bx bx-plus me-1"></i> Bedengan Baru</h5>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ route('beds.store', $sector->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama / Kode</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-tag"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Bed A-01" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Panjang (m)</label>
                                <input type="number" step="0.1" name="length" id="input_panjang" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Lebar (m)</label>
                                <input type="number" step="0.1" name="width" id="input_lebar" class="form-control" placeholder="0" required>
                            </div>
                        </div>

                        <div class="bg-label-warning p-3 rounded mb-3 border border-warning">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="fw-bold text-warning text-uppercase"><i class="bx bx-calculator"></i> Hitung Populasi</small>
                            </div>
                            <div class="input-group input-group-sm mb-2">
                                <input type="number" id="jarak_tanam" class="form-control" placeholder="Jarak Tanam (cm)">
                                <button type="button" class="btn btn-warning" onclick="hitungPopulasi()">Hitung</button>
                            </div>
                            <small class="text-muted fst-italic" style="font-size: 10px;">*Opsional: Masukkan jarak tanam dalam cm.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kapasitas Maksimal</label>
                            <div class="input-group">
                                <input type="number" name="max_capacity" id="input_kapasitas" class="form-control fw-bold text-primary" placeholder="0" required>
                                <span class="input-group-text">Pohon</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-save me-1"></i> Simpan Bedengan
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function hitungPopulasi() {
        let p = parseFloat(document.getElementById('input_panjang').value) || 0;
        let l = parseFloat(document.getElementById('input_lebar').value) || 0;
        let jarak = parseFloat(document.getElementById('jarak_tanam').value) || 0;

        if (p <= 0 || l <= 0 || jarak <= 0) {
            alert("Harap isi Panjang, Lebar, dan Jarak Tanam dengan benar.");
            return;
        }

        // Rumus sederhana: Luas Bedengan (cm2) / (Jarak x Jarak)
        // Asumsi tanam persegi/bujur sangkar
        let luasBedCm = (p * 100) * (l * 100);
        let luasTanamCm = jarak * jarak;
        let hasil = Math.floor(luasBedCm / luasTanamCm);

        let inputCap = document.getElementById('input_kapasitas');
        inputCap.value = hasil;

        // Efek Visual
        inputCap.classList.add('bg-label-success');
        setTimeout(() => inputCap.classList.remove('bg-label-success'), 1000);
    }
</script>
@endsection
