@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-1 mb-4">
        <span class="text-muted fw-light">Operasional /</span> Master Komoditas
    </h4>

    <div class="row">

        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Katalog Tanaman</h5>
                    <span class="badge bg-label-primary">{{ $commodities->count() }} Item</span>
                </div>

                @if(session('success'))
                    <div class="px-4">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                @if($commodities->count() > 0)
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Komoditas</th>
                                    <th>Varietas</th>
                                    <th class="text-center">Masa Panen</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($commodities as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-success">
                                                    {{ substr($item->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="fw-bold text-heading">{{ $item->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->variety)
                                            <span class="text-muted">{{ $item->variety }}</span>
                                        @else
                                            <span class="text-muted fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-info">
                                            <i class="bx bx-time-five me-1"></i> {{ $item->harvest_duration_days }} Hari
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('commodities.edit', $item->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <form action="{{ route('commodities.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus {{ $item->name }}? Data tidak bisa dikembalikan.');">
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
                                <i class="bx bxs-tree fs-1"></i>
                            </span>
                        </div>
                        <h6 class="text-muted mb-1">Katalog Kosong</h6>
                        <p class="small text-muted">Tambahkan jenis tanaman yang Anda budidayakan.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card sticky-top" style="top: 100px; z-index: 10;">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="card-title text-white mb-0 d-flex align-items-center text-sm text-uppercase">
                        <i class="bx bx-plus-circle me-2"></i> Tambah Baru
                    </h5>
                </div>
                <div class="card-body mt-4">
                    <form action="{{ route('commodities.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Tanaman</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-purchase-tag"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="Cth: Cabai Merah" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Varietas (Opsional)</label>
                            <input type="text" name="variety" class="form-control" placeholder="Cth: Lado F1">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Estimasi Panen</label>
                            <div class="input-group">
                                <input type="number" name="harvest_duration_days" class="form-control" placeholder="90" required min="1">
                                <span class="input-group-text">Hari</span>
                            </div>
                            <div class="form-text text-muted">Dihitung sejak tanggal tanam bibit.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="bx bx-save me-1"></i> Simpan Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
