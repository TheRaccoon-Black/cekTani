@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold py-1 mb-0">
                <span class="text-muted fw-light">Operasional / Monitoring /</span> {{ $cycle->commodity->name }}
            </h4>
            <div class="text-muted small">
                Lokasi: <strong>{{ $cycle->bed->sector->name }}</strong> &rsaquo; {{ $cycle->bed->name }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sectors.beds.index', $cycle->bed->sector_id) }}" class="btn btn-label-secondary">
                <span class="tf-icons bx bx-arrow-back me-1"></span> Daftar Bedengan
            </a>

            <form action="{{ route('cycles.harvest', $cycle->id) }}" method="POST" onsubmit="return confirm('Selesaikan siklus ini dan panen?');">
                @csrf @method('PUT')
                <button type="submit" class="btn btn-success">
                    <span class="tf-icons bx bx-check-circle me-1"></span> Panen Selesai
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <h6 class="mb-2">Tanggal Tanam</h6>
                                <h4 class="mb-2">{{ \Carbon\Carbon::parse($cycle->start_date)->format('d M Y') }}</h4>
                                <p class="mb-0"><span class="text-muted me-2">Mulai</span></p>
                            </div>
                            <span class="avatar p-2 rounded bg-label-secondary"><i class="bx bx-calendar fs-4"></i></span>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                            <div>
                                <h6 class="mb-2">Umur Tanaman</h6>
                                <h4 class="mb-2 text-primary">{{ \Carbon\Carbon::parse($cycle->start_date)->diffInDays(now()) }} HST</h4>
                                <p class="mb-0"><span class="text-muted me-2">Hari Setelah Tanam</span></p>
                            </div>
                            <span class="avatar p-2 rounded bg-label-primary"><i class="bx bx-time-five fs-4"></i></span>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                            <div>
                                <h6 class="mb-2">Estimasi Panen</h6>
                                <h4 class="mb-2 text-success">{{ \Carbon\Carbon::parse($cycle->estimated_harvest_date)->format('d M Y') }}</h4>
                                <p class="mb-0 text-muted">Target</p>
                            </div>
                            <span class="avatar p-2 rounded bg-label-success"><i class="bx bx-target-lock fs-4"></i></span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-2">Populasi Awal</h6>
                                <h4 class="mb-2">{{ $cycle->initial_plant_count }}</h4>
                                <p class="mb-0"><span class="text-muted me-2">Tanaman</span></p>
                            </div>
                            <span class="avatar p-2 rounded bg-label-warning"><i class="bx bx-user fs-4"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $totalDays = \Carbon\Carbon::parse($cycle->start_date)->diffInDays($cycle->estimated_harvest_date);
            $currentDays = \Carbon\Carbon::parse($cycle->start_date)->diffInDays(now());
            $progress = ($totalDays > 0) ? ($currentDays / $totalDays) * 100 : 0;
            $progress = min($progress, 100);
        @endphp
        <div class="card-footer border-top p-0">
            <div class="progress rounded-0" style="height: 8px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-4 order-1 order-lg-0 mb-4">
            <div class="card sticky-top" style="top: 100px; z-index: 10;">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="card-title text-white mb-0 d-flex align-items-center">
                        <i class="bx bx-edit me-2"></i> Catat Jurnal Harian
                    </h5>
                </div>
                <div class="card-body mt-4">
                    <form action="{{ route('cycles.logs.store', $cycle->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal & Fase</label>
                            <div class="input-group">
                                <input type="date" name="log_date" value="{{ date('Y-m-d') }}" class="form-control">
                                <select name="phase" class="form-select bg-light">
                                    <option value="Perawatan">Perawatan</option>
                                    <option value="Vegetatif">Vegetatif</option>
                                    <option value="Generatif">Generatif</option>
                                    <option value="Hama & Penyakit">Hama/Penyakit</option>
                                    <option value="Panen">Panen</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Aktivitas Utama</label>
                            <input type="text" name="activity" class="form-control" placeholder="Cth: Semprot Hama, Pupuk Dasar" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Detail</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Dosis obat, kondisi daun, cuaca..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Bukti</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="bx bx-save me-1"></i> Simpan Catatan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 order-0 order-lg-1 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Kronologi Kegiatan</h5>
                </div>
                <div class="card-body">

                    @if($cycle->logs->count() > 0)
                        <ul class="timeline timeline-dashed mt-3">
                            @foreach($cycle->logs as $log)
                                @php
                                    $color = match($log->phase) {
                                        'Hama & Penyakit' => 'danger',
                                        'Panen' => 'success',
                                        'Generatif' => 'warning',
                                        default => 'primary'
                                    };
                                @endphp
                                <li class="timeline-item timeline-item-transparent pb-4 border-left-dashed">
                                    <span class="timeline-point-wrapper">
                                        <span class="timeline-point timeline-point-{{ $color }}"></span>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">{{ $log->activity }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($log->log_date)->translatedFormat('d M Y') }}</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-label-{{ $color }}">{{ $log->phase }}</span>
                                        </div>

                                        @if($log->notes)
                                            <div class="p-2 bg-lighter rounded text-muted mb-2 text-sm">
                                                <i class="bx bxs-quote-alt-left me-1 opacity-50"></i> {{ $log->notes }}
                                            </div>
                                        @endif

                                        @if($log->photo_path)
                                            <div class="mb-2">
                                                <a href="{{ asset('storage/' . $log->photo_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $log->photo_path) }}" alt="Bukti" class="rounded w-px-100 object-cover border" style="height: 100px;">
                                                </a>
                                            </div>
                                        @endif

                                        <div class="d-flex justify-content-end">
                                            <form action="{{ route('cycles.logs.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Hapus log ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-label-danger">
                                                    <i class="bx bx-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <span class="avatar avatar-xl rounded-circle bg-label-secondary p-4">
                                    <i class="bx bx-notepad fs-1"></i>
                                </span>
                            </div>
                            <h6 class="text-muted">Belum ada catatan jurnal.</h6>
                            <p class="small text-muted">Aktivitas yang Anda catat di form kiri akan muncul di sini.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

<style>
.timeline .timeline-item { position: relative; padding-left: 3rem; border-left: 1px solid #e7e7e8; }
.timeline .timeline-point-wrapper { position: absolute; left: -0.4rem; top: 0; z-index: 2; background-color: #fff; padding: 0.15rem; }
.timeline .timeline-point { display: block; height: 0.75rem; width: 0.75rem; border-radius: 50%; }
.timeline .timeline-event { position: relative; width: 100%; min-height: 3rem; }
.bg-lighter { background-color: #f5f5f9; }
/* Warna Titik */
.timeline-point-primary { background-color: #696cff; box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.2); }
.timeline-point-danger { background-color: #ff3e1d; box-shadow: 0 0 0 3px rgba(255, 62, 29, 0.2); }
.timeline-point-success { background-color: #71dd37; box-shadow: 0 0 0 3px rgba(113, 221, 55, 0.2); }
.timeline-point-warning { background-color: #ffab00; box-shadow: 0 0 0 3px rgba(255, 171, 0, 0.2); }
</style>
@endsection
