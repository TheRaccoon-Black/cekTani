@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-1 mb-0"><span class="text-muted fw-light">Operasional /</span> Kalender Tani</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
            <i class="bx bx-plus me-1"></i> Buat Jadwal
        </button>
    </div>

    <div class="card p-3">
        <div id='calendar'></div>
    </div>
</div>

<div class="modal fade" id="createScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('schedules.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Jadwal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12 mb-2">
                            <label class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Semprot Hama" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Kategori</label>
                            <select name="type" class="form-select">
                                <option value="general">Umum</option>
                                <option value="fertilizing">Pemupukan</option>
                                <option value="pest_control">Pengendalian Hama</option>
                                <option value="irrigation">Pengairan</option>
                                <option value="harvest">Panen</option>
                            </select>
                        </div>

                        <div class="col-12"><hr class="my-1"></div>

                        <div class="col-12 mb-2">
                            <label class="form-label">Pilih Lahan <span class="text-danger">*</span></label>
                            <select name="land_id" class="form-select" id="landSelect" required onchange="filterSectors()">
                                <option value="">-- Pilih Lahan --</option>
                                @foreach($lands as $land)
                                    <option value="{{ $land->id }}">{{ $land->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-2" id="sectorWrapper" style="display:none;">
                            <label class="form-label">Spesifik Sektor (Opsional)</label>
                            <select name="sector_id" class="form-select" id="sectorSelect" onchange="filterBeds()">
                                <option value="">-- Seluruh Lahan Terpilih --</option>
                                @foreach($lands as $land)
                                    @foreach($land->sectors as $sector)
                                        <option value="{{ $sector->id }}" data-land="{{ $land->id }}">
                                            Sektor {{ $sector->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            <div class="form-text text-xs">Biarkan kosong jika jadwal untuk seluruh lahan.</div>
                        </div>

                        <div class="col-12 mb-2" id="bedWrapper" style="display:none;">
                            <label class="form-label">Spesifik Bedengan (Opsional)</label>
                            <select name="bed_id" class="form-select" id="bedSelect">
                                <option value="">-- Seluruh Sektor Terpilih --</option>
                                @foreach($lands as $land)
                                    @foreach($land->sectors as $sector)
                                        @foreach($sector->beds as $bed)
                                            <option value="{{ $bed->id }}" data-sector="{{ $sector->id }}">
                                                Bed {{ $bed->name }}
                                                @if($bed->activePlantingCycle)
                                                    ({{ $bed->activePlantingCycle->commodity->name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </select>
                            <div class="form-text text-xs">Biarkan kosong jika jadwal untuk seluruh sektor.</div>
                        </div>

                        <div class="col-12 mt-2">
                            <label class="form-label">Catatan Awal</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Instruksi tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventTitle">Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase mb-0">Target Lokasi</label>
                    <p id="eventLocation" class="fw-bold text-primary mb-0"></p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase mb-0">Jadwal</label>
                    <p id="eventDate" class="mb-0"></p>
                </div>

                <div class="mb-3 p-2 bg-light rounded" id="notesWrapper" style="display:none;">
                    <label class="form-label text-muted small text-uppercase mb-0">Catatan / Hasil</label>
                    <p id="eventNotes" class="mb-0 small fst-italic text-dark"></p>
                </div>

                <hr>

                <form id="completeForm" method="POST">
                    @csrf @method('PUT')
                    <div id="actionWrapper">
                        <div class="mb-3">
                            <label class="form-label small">Catatan Penyelesaian</label>
                            <textarea name="notes" class="form-control form-control-sm" placeholder="Hasil pengerjaan..."></textarea>
                        </div>
                        <div class="mb-3 border-top pt-2 mt-2">
    <label class="form-label small fw-bold text-primary">Pakai Material (Opsional)</label>
    <div class="row g-2">
        <div class="col-8">
            <select name="inventory_id" class="form-select form-select-sm">
                <option value="">-- Pilih Barang --</option>
                @foreach($inventories as $inv)
                    <option value="{{ $inv->id }}">
                        {{ $inv->name }} (Sisa: {{ $inv->stock }} {{ $inv->unit }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-4">
            <input type="number" step="0.01" name="quantity" class="form-control form-select-sm" placeholder="Jml">
        </div>
    </div>
    <div class="form-text text-xs">Stok & Biaya akan terpotong otomatis.</div>
</div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-circle me-1"></i> Tandai Selesai
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary w-100" id="btnDisabled" style="display:none;" disabled>
                        <i class="bx bx-check-double me-1"></i> Sudah Selesai
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: @json($events),
            eventClick: function(info) {
                // 1. Set Judul & Tanggal
                document.getElementById('eventTitle').innerText = info.event.title;
                document.getElementById('eventDate').innerText = info.event.start.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

                // 2. Set Lokasi (Lengkap dari Controller)
                document.getElementById('eventLocation').innerText = info.event.extendedProps.location;

                // 3. Set Notes (Jika ada)
                var notes = info.event.extendedProps.notes;
                var notesWrapper = document.getElementById('notesWrapper');
                if (notes && notes.trim() !== "") {
                    notesWrapper.style.display = 'block';
                    document.getElementById('eventNotes').innerText = notes;
                } else {
                    notesWrapper.style.display = 'none';
                }

                // 4. Atur Form Action & Tombol
                var formAction = document.getElementById('actionWrapper');
                var btnDisabled = document.getElementById('btnDisabled');
                var form = document.getElementById('completeForm');

                form.action = "/schedules/" + info.event.id + "/complete";

                if(info.event.extendedProps.status === 'completed') {
                    formAction.style.display = 'none';
                    btnDisabled.style.display = 'block';
                } else {
                    formAction.style.display = 'block';
                    btnDisabled.style.display = 'none';
                }

                var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
                myModal.show();
            }
        });
        calendar.render();
    });

    // --- LOGIKA FILTER DROPDOWN BERTINGKAT ---

    function filterSectors() {
        var landId = document.getElementById('landSelect').value;
        var sectorWrapper = document.getElementById('sectorWrapper');
        var bedWrapper = document.getElementById('bedWrapper');
        var sectorSelect = document.getElementById('sectorSelect');

        // Reset Sektor & Bed
        sectorSelect.value = "";
        document.getElementById('bedSelect').value = "";
        bedWrapper.style.display = 'none';

        if(landId) {
            sectorWrapper.style.display = 'block';
            var options = sectorSelect.options;
            // Loop semua opsi sektor, sembunyikan yang bukan milik Land terpilih
            for (var i = 0; i < options.length; i++) {
                var opt = options[i];
                if (opt.value === "") continue; // Skip opsi default

                if (opt.getAttribute('data-land') == landId) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            }
        } else {
            sectorWrapper.style.display = 'none';
        }
    }

    function filterBeds() {
        var sectorId = document.getElementById('sectorSelect').value;
        var bedWrapper = document.getElementById('bedWrapper');
        var bedSelect = document.getElementById('bedSelect');

        // Reset Bed
        bedSelect.value = "";

        if(sectorId) {
            bedWrapper.style.display = 'block';
            var options = bedSelect.options;
            // Loop semua opsi bed, sembunyikan yang bukan milik Sektor terpilih
            for (var i = 0; i < options.length; i++) {
                var opt = options[i];
                if (opt.value === "") continue;

                if (opt.getAttribute('data-sector') == sectorId) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            }
        } else {
            bedWrapper.style.display = 'none';
        }
    }
</script>
@endsection
