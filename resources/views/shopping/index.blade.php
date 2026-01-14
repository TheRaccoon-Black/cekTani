@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-1 mb-0"><span class="text-muted fw-light">Operasional /</span> Rencana Belanja</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSessionModal">
            <i class="bx bx-plus me-1"></i> Buat Sesi Baru
        </button>
    </div>

    <div class="row g-4">
        @forelse($sessions as $session)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-top border-4 border-primary shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <a href="{{ route('shopping.show', $session->id) }}" class="text-decoration-none">{{ $session->name }}</a>
                        </h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <form action="{{ route('shopping.session.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Hapus sesi ini beserta isinya?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="bx bx-trash me-1"></i> Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <small class="text-muted d-block mb-3">Dibuat: {{ $session->created_at->format('d M Y') }}</small>

                    <div class="bg-light rounded p-2 mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Estimasi Total:</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($session->total_estimated, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">Item:</span>
                            <span class="small">{{ $session->items->where('is_purchased', true)->count() }} / {{ $session->items->count() }} terbeli</span>
                        </div>
                    </div>

                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $session->progress }}%"></div>
                    </div>

                    <a href="{{ route('shopping.show', $session->id) }}" class="btn btn-outline-primary w-100 btn-sm">
                        Buka Daftar <i class="bx bx-right-arrow-alt ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" width="100" class="mb-3 opacity-25">
            <h5>Belum ada sesi belanja.</h5>
            <p class="text-muted">Buat sesi untuk mulai merencanakan pembelian (Misal: "Pupuk Awal Tanam")</p>
        </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="createSessionModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form class="modal-content" action="{{ route('shopping.session.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Sesi Belanja Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Sesi</label>
                    <input type="text" name="name" class="form-control" placeholder="Cth: Belanja Obat Ulat" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Rencana</label>
                    <input type="date" name="planning_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">Buat</button>
            </div>
        </form>
    </div>
</div>
@endsection
