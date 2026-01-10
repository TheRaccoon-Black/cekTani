@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-1 mb-0">
            <span class="text-muted fw-light">Master Data / Komoditas /</span> Edit
        </h4>
        <a href="{{ route('commodities.index') }}" class="btn btn-label-secondary">
            <span class="tf-icons bx bx-arrow-back me-1"></span> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">Form Edit Komoditas</h5>
                    <small class="text-muted">ID: #{{ $commodity->id }}</small>
                </div>

                <div class="card-body mt-4">
                    <form action="{{ route('commodities.update', $commodity->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Tanaman <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-leaf"></i></span>
                                <input type="text" name="name"
                                    value="{{ old('name', $commodity->name) }}"
                                    class="form-control"
                                    placeholder="Contoh: Cabai Merah" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Varietas</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-purchase-tag"></i></span>
                                <input type="text" name="variety"
                                    value="{{ old('variety', $commodity->variety) }}"
                                    class="form-control"
                                    placeholder="Contoh: Lado F1">
                            </div>
                            <div class="form-text">Boleh dikosongkan jika tidak ada varietas khusus.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Estimasi Masa Panen</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-time-five"></i></span>
                                <input type="number" name="harvest_duration_days"
                                    value="{{ old('harvest_duration_days', $commodity->harvest_duration_days) }}"
                                    class="form-control fw-bold text-primary"
                                    required min="1">
                                <span class="input-group-text">Hari</span>
                            </div>
                            <div class="form-text">Dihitung sejak tanggal tanam hingga panen pertama.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('commodities.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
