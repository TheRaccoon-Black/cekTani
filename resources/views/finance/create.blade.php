@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-1 mb-4">
        <span class="text-muted fw-light">Keuangan /</span> Catat Transaksi Baru
    </h4>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white p-3">
                    <h5 class="mb-0 text-white"><i class="bx bx-money me-2"></i> Form Keuangan</h5>
                </div>

                <div class="card-body mt-4">
                    <form action="{{ route('finance.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Transaksi</label>
                                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Transaksi</label>
                                <select name="type" class="form-select bg-light fw-bold text-primary">
                                    <option value="expense" selected>🔴 Pengeluaran (Biaya)</option>
                                    <option value="income">🟢 Pemasukan (Omzet)</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-label-secondary p-3 rounded mb-4 border border-secondary">
                            <h6 class="mb-3 text-uppercase text-xs fw-bold text-muted">Tingkatan Alokasi Biaya</h6>

                            <div class="mb-3">
                                <label class="form-label">Lahan Utama <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-map"></i></span>
                                    <select name="land_id" id="landSelect" class="form-select" onchange="updateScope()" required>
                                        <option value="" selected disabled>-- Pilih Lahan --</option>
                                        @foreach($lands as $land)
                                            <option value="{{ $land->id }}">{{ $land->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block mb-2">Detail Alokasi:</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="scope_level" id="scope1" value="land" checked onchange="toggleDropdowns()">
                                    <label class="btn btn-outline-primary" for="scope1">Lahan (Umum)</label>

                                    <input type="radio" class="btn-check" name="scope_level" id="scope2" value="sector" onchange="toggleDropdowns()">
                                    <label class="btn btn-outline-primary" for="scope2">Sektor (Area)</label>

                                    <input type="radio" class="btn-check" name="scope_level" id="scope3" value="bed" onchange="toggleDropdowns()">
                                    <label class="btn btn-outline-primary" for="scope3">Bedengan (Spesifik)</label>
                                </div>
                                <div class="form-text mt-2 text-xs text-muted" id="scopeHelp">
                                    Biaya umum seperti PBB, Gaji Satpam, Listrik Gubuk.
                                </div>
                            </div>

                            <div id="sectorContainer" class="mb-3 d-none">
                                <label class="form-label">Pilih Sektor</label>
                                <select name="sector_id" id="sectorSelect" class="form-select" onchange="filterBeds()">
                                    <option value="">-- Pilih Sektor --</option>
                                </select>
                            </div>

                            <div id="bedContainer" class="mb-3 d-none">
                                <label class="form-label">Pilih Bedengan</label>
                                <select name="bed_id" id="bedSelect" class="form-select">
                                    <option value="">-- Pilih Bedengan --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Kategori / Keterangan</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-tag"></i></span>
                                <input type="text" name="category" list="categories" class="form-control" placeholder="Cth: Pupuk NPK Mutiara" required>
                                <datalist id="categories">
                                    <option value="Pupuk">
                                    <option value="Pestisida">
                                    <option value="Tenaga Kerja">
                                    <option value="Bibit">
                                    <option value="Panen">
                                    <option value="Infrastruktur">
                                </datalist>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Nominal (Rp)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="number" name="amount" class="form-control fw-bold text-end fs-4" placeholder="0" required min="0">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Keterangan</label>
                            <div class="input-group input-group-merge">
                                <textarea name="description" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bx bx-save me-1"></i> Simpan Transaksi
                            </button>
                            <a href="{{ route('finance.index') }}" class="btn btn-label-secondary text-center">Batal</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const landsData = {!! json_encode($lands) !!};

    function toggleDropdowns() {
        const scope = document.querySelector('input[name="scope_level"]:checked').value;
        const helpText = document.getElementById('scopeHelp');

        const sectorBox = document.getElementById('sectorContainer');
        const bedBox = document.getElementById('bedContainer');

        if (scope === 'land') {
            sectorBox.classList.add('d-none');
            bedBox.classList.add('d-none');
            helpText.innerText = "Biaya umum seperti PBB, Gaji Satpam, Listrik Gubuk.";
        } else if (scope === 'sector') {
            sectorBox.classList.remove('d-none');
            bedBox.classList.add('d-none');
            helpText.innerText = "Biaya per area, misal: Penyemprotan satu blok, Perbaikan irigasi sektor.";
        } else {
            sectorBox.classList.remove('d-none'); // Butuh pilih sektor dulu
            bedBox.classList.remove('d-none');
            helpText.innerText = "Biaya spesifik tanaman, misal: Pupuk kocor per lubang, Panen bedengan A.";
        }
    }

    function updateScope() {
        const landId = document.getElementById('landSelect').value;
        const sectorSelect = document.getElementById('sectorSelect');
        sectorSelect.innerHTML = '<option value="">-- Pilih Sektor --</option>';

        if (landId) {
            const land = landsData.find(l => l.id == landId);
            if (land && land.sectors) {
                land.sectors.forEach(s => {
                    sectorSelect.add(new Option(s.name, s.id));
                });
            }
        }
        filterBeds();
    }

    function filterBeds() {
        const landId = document.getElementById('landSelect').value;
        const sectorId = document.getElementById('sectorSelect').value;
        const bedSelect = document.getElementById('bedSelect');
        bedSelect.innerHTML = '<option value="">-- Pilih Bedengan --</option>';

        if (landId && sectorId) {
            const land = landsData.find(l => l.id == landId);
            const sector = land.sectors.find(s => s.id == sectorId);
            if (sector && sector.beds) {
                sector.beds.forEach(b => {
                    bedSelect.add(new Option(b.name, b.id));
                });
            }
        }
    }
</script>
@endsection
