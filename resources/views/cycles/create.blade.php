@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-1 mb-4">
        <span class="text-muted fw-light">Operasional / Tanam Baru /</span> {{ $bed->name }}
    </h4>

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mulai Siklus Tanam</h5>
                    <small class="text-muted float-end">ID Bedengan: #{{ $bed->id }}</small>
                </div>

                <div class="card-body">
                    <form action="{{ route('cycles.store', $bed->id) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Komoditas <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-leaf"></i></span>
                                <select name="commodity_id" id="commoditySelect" class="form-select" required onchange="calculateHarvest()">
                                    <option value="" data-days="0" selected disabled>-- Pilih Tanaman --</option>
                                    @foreach($commodities as $c)
                                        <option value="{{ $c->id }}" data-days="{{ $c->harvest_duration_days }}">
                                            {{ $c->name }} (Panen ±{{ $c->harvest_duration_days }} hari)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text">Pastikan bibit sudah siap sebelum memulai pencatatan.</div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Tanam</label>
                                <input type="date" name="start_date" id="startDate"
                                    value="{{ date('Y-m-d') }}"
                                    class="form-control" required onchange="calculateHarvest()">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Estimasi Panen</label>
                                <input type="text" id="displayHarvest" class="form-control bg-label-primary text-primary fw-bold" readonly placeholder="-">
                                <small id="daysLabel" class="text-primary d-block mt-1"></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Populasi Awal</label>
                            <div class="input-group">
                                <input type="number" name="initial_plant_count"
                                    value="{{ $bed->max_capacity }}"
                                    class="form-control" required min="1">
                                <span class="input-group-text">Pohon / Bibit</span>
                            </div>
                            <div class="form-text text-muted">
                                Kapasitas maks bedengan ini: <strong>{{ $bed->max_capacity }}</strong> tanaman.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="{{ route('sectors.beds.index', $bed->sector_id) }}" class="btn btn-label-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-check-circle me-1"></i> Simpan & Mulai
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card mb-4 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-2">
                            <span class="avatar-initial rounded bg-white bg-opacity-25"><i class="bx bx-map-pin text-white"></i></span>
                        </div>
                        <h5 class="mb-0 text-white">Lokasi Tanam</h5>
                    </div>

                    <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-2">
                            <i class="bx bx-buildings me-2"></i>
                            <span>Lahan: <strong>{{ $bed->sector->land->name }}</strong></span>
                        </li>
                        <li class="d-flex mb-2">
                            <i class="bx bx-grid-alt me-2"></i>
                            <span>Sektor: <strong>{{ $bed->sector->name }}</strong></span>
                        </li>
                        <li class="d-flex">
                            <i class="bx bx-ruler me-2"></i>
                            <span>Ukuran: {{ $bed->length }}m x {{ $bed->width }}m</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">💡 Tips Memulai</h5>
                </div>
                <div class="card-body">
                    <ul class="ps-3 mb-0 text-muted small">
                        <li class="mb-2">Pastikan tanah sudah diolah dan diberi pupuk dasar sebelum tanggal tanam.</li>
                        <li class="mb-2">Jika bibit berasal dari persemaian, hitung jumlah yang benar-benar hidup saat pindah tanam.</li>
                        <li>Estimasi panen dihitung otomatis berdasarkan data komoditas, namun bisa berubah tergantung cuaca.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateHarvest() {
        const commoditySelect = document.getElementById('commoditySelect');
        const startDateInput = document.getElementById('startDate');
        const displayHarvest = document.getElementById('displayHarvest');
        const daysLabel = document.getElementById('daysLabel');

        const selectedOption = commoditySelect.options[commoditySelect.selectedIndex];
        const durationDays = parseInt(selectedOption.getAttribute('data-days')) || 0;
        const startDateVal = startDateInput.value;

        if (durationDays > 0 && startDateVal) {
            const date = new Date(startDateVal);
            date.setDate(date.getDate() + durationDays);

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateString = date.toLocaleDateString('id-ID', options);

            displayHarvest.value = dateString;
            daysLabel.innerText = `(+${durationDays} Hari)`;
        } else {
            displayHarvest.value = "-";
            daysLabel.innerText = "";
        }
    }
</script>
@endsection
