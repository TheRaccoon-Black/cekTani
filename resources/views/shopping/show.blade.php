@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('shopping.index') }}" class="text-muted mb-1 d-inline-block"><i class="bx bx-arrow-back"></i> Kembali</a>
            <h4 class="fw-bold mb-0">{{ $session->name }}</h4>
            <small class="text-muted"><i class="bx bx-calendar"></i> Rencana: {{ \Carbon\Carbon::parse($session->planning_date)->format('d M Y') }}</small>
        </div>
        <div class="text-end">
            <small class="text-muted d-block">Total Estimasi Anggaran</small>
            <h3 class="text-primary mb-0">Rp {{ number_format($session->total_estimated, 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-label-primary">
                    <h6 class="mb-0 fw-bold"><i class="bx bx-plus"></i> Tambah Item Belanja</h6>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ route('shopping.item.store', $session->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                             <div class="btn-group w-100 btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="type" id="typeStock" value="stock" checked onchange="toggleType('stock')">
                                <label class="btn btn-outline-primary" for="typeStock">Stok (Gudang)</label>

                                <input type="radio" class="btn-check" name="type" id="typeDirect" value="direct" onchange="toggleType('direct')">
                                <label class="btn btn-outline-warning" for="typeDirect">Langsung (Lahan)</label>
                            </div>
                        </div>

                        <div id="stockInputs" class="mb-2">
                            <label class="form-label text-xs">Pilih Stok (Opsional)</label>
                            <select name="inventory_id" id="invSelect" class="form-select form-select-sm" onchange="checkNewItem(this)">
                                <option value="">-- Item Baru --</option>
                                @foreach($inventories as $inv)
                                    <option value="{{ $inv->id }}" data-unit="{{ $inv->unit }}">{{ $inv->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="directInputs" class="mb-3" style="display: none;">
                            <label class="form-label text-xs fw-bold text-warning">Alokasi Lokasi (Opsional)</label>

                            <select name="land_id" id="landSelect" class="form-select form-select-sm mb-2" onchange="filterSectors()">
                                <option value="">-- Pilih Lahan --</option>
                                @foreach($lands as $land)
                                    <option value="{{ $land->id }}">{{ $land->name }}</option>
                                @endforeach
                            </select>

                            <select name="sector_id" id="sectorSelect" class="form-select form-select-sm mb-2" onchange="filterBeds()" disabled>
                                <option value="">-- Semua Sektor --</option>
                            </select>

                            <select name="bed_id" id="bedSelect" class="form-select form-select-sm" disabled>
                                <option value="">-- Semua Bed --</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <input type="text" name="name" id="nameInput" class="form-control" placeholder="Nama Barang (Cth: Semen, Mulsa)" required>
                        </div>
                        <div class="mb-2">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-store"></i></span>
                                <input type="text" name="url" class="form-control" placeholder="Link / Nama Toko">
                            </div>
                        </div>
                        <div class="row g-1 mb-2">
                            <div class="col-6">
                                <input type="number" step="0.01" name="quantity" class="form-control" placeholder="Jml" required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="unit" id="unitInput" class="form-control" placeholder="Satuan" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="estimated_price" class="form-control" placeholder="Harga Est.">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-sm">Tambah ke List</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%"></th>
                                <th>Barang & Lokasi</th> <th class="text-center">Jml</th>
                                <th class="text-end">Harga/Sat</th>
                                <th class="text-end">Subtotal (Est)</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($session->items as $item)
                            <tr class="{{ $item->is_purchased ? 'bg-light text-muted' : '' }}">
                                <td>
                                    @if(!$item->is_purchased)
                                        <button class="btn btn-sm btn-icon btn-outline-success rounded-circle"
                                            onclick="openCheckoutModal({{ $item->id }}, '{{ $item->name }}', {{ $item->estimated_price }}, {{ $item->quantity }})">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    @else
                                        <i class="bx bx-check-circle text-success fs-4"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold {{ $item->is_purchased ? 'text-decoration-line-through' : '' }}">{{ $item->name }}</span>

                                        @if($item->url)
                                            <small class="text-muted">
                                                <i class="bx bx-store-alt" style="font-size: 0.7rem;"></i>
                                                @if(filter_var($item->url, FILTER_VALIDATE_URL))
                                                    <a href="{{ $item->url }}" target="_blank" class="text-primary text-decoration-underline">Buka Link</a>
                                                @else
                                                    {{ $item->url }}
                                                @endif
                                            </small>
                                        @endif

                                        <div class="mt-1">
                                            <span class="badge {{ $item->type == 'stock' ? 'bg-label-info' : 'bg-label-warning' }} text-tiny p-1" style="font-size: 0.6rem;">
                                                {{ $item->type == 'stock' ? 'Gudang' : 'Langsung' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ $item->quantity }} <span class="text-muted text-xs">{{ $item->unit }}</span>
                                </td>
                                <td class="text-end text-muted small">
                                    {{ number_format($item->estimated_price, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold {{ $item->is_purchased ? 'text-success' : 'text-dark' }}">
                                    Rp {{ number_format($item->quantity * $item->estimated_price, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if(!$item->is_purchased)
                                        <form action="{{ route('shopping.item.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-icon text-danger"><i class="bx bx-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada barang di sesi ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">TOTAL ANGGARAN:</td>
                                <td class="text-end fw-bold text-primary fs-5">Rp {{ number_format($session->total_estimated, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="checkoutForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">Konfirmasi Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Anda membeli: <strong id="modalItemName"></strong></p>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Real Dibeli</label>
                        <input type="number" step="0.01" name="actual_qty" id="modalQty" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Satuan Real (Rp)</label>
                        <input type="number" name="actual_price" id="modalPrice" class="form-control" required>
                        <div class="form-text text-xs">Harga per unit saat nota terbit.</div>
                    </div>

                    <div class="alert alert-warning text-xs mb-0 p-2">
                        <i class="bx bx-info-circle"></i> Stok & Saldo Kas akan otomatis terupdate.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function checkNewItem(select) {
        const nameInput = document.getElementById('nameInput');
        const unitInput = document.getElementById('unitInput');
        const selectedOption = select.options[select.selectedIndex];

        if (select.value) {
            nameInput.value = selectedOption.text;
            unitInput.value = selectedOption.getAttribute('data-unit');
        } else {
            nameInput.value = "";
            unitInput.value = "";
        }
    }

    function openCheckoutModal(id, name, estPrice, estQty) {
        document.getElementById('modalItemName').innerText = name;
        document.getElementById('modalPrice').value = estPrice;
        document.getElementById('modalQty').value = estQty;
        document.getElementById('checkoutForm').action = "/shopping/purchase/" + id;
        new bootstrap.Modal(document.getElementById('checkoutModal')).show();
    }
</script>
<script>
    const landsData = @json($lands);

    function toggleType(type) {
        if(type === 'stock') {
            document.getElementById('stockInputs').style.display = 'block';
            document.getElementById('directInputs').style.display = 'none';
            document.getElementById('nameInput').value = "";
        } else {
            document.getElementById('stockInputs').style.display = 'none';
            document.getElementById('directInputs').style.display = 'block';
            document.getElementById('invSelect').value = "";
        }
    }

    function checkNewItem(select) {
        const nameInput = document.getElementById('nameInput');
        const unitInput = document.getElementById('unitInput');
        const selectedOption = select.options[select.selectedIndex];

        if (select.value) {
            nameInput.value = selectedOption.text;
            unitInput.value = selectedOption.getAttribute('data-unit');
        } else {
            nameInput.value = "";
            unitInput.value = "";
        }
    }

    function filterSectors() {
        const landId = document.getElementById('landSelect').value;
        const sectorSelect = document.getElementById('sectorSelect');
        const bedSelect = document.getElementById('bedSelect');

        sectorSelect.innerHTML = '<option value="">-- Semua Sektor --</option>';
        bedSelect.innerHTML = '<option value="">-- Semua Bed --</option>';
        bedSelect.disabled = true;

        if (landId) {
            sectorSelect.disabled = false;
            const land = landsData.find(l => l.id == landId);
            if (land && land.sectors) {
                land.sectors.forEach(sector => {
                    sectorSelect.innerHTML += `<option value="${sector.id}">${sector.name}</option>`;
                });
            }
        } else {
            sectorSelect.disabled = true;
        }
    }

    function filterBeds() {
        const landId = document.getElementById('landSelect').value;
        const sectorId = document.getElementById('sectorSelect').value;
        const bedSelect = document.getElementById('bedSelect');

        bedSelect.innerHTML = '<option value="">-- Semua Bed --</option>';

        if (sectorId) {
            bedSelect.disabled = false;
            const land = landsData.find(l => l.id == landId);
            const sector = land.sectors.find(s => s.id == sectorId);
            if (sector && sector.beds) {
                sector.beds.forEach(bed => {
                    bedSelect.innerHTML += `<option value="${bed.id}">${bed.name}</option>`;
                });
            }
        } else {
            bedSelect.disabled = true;
        }
    }

    function openCheckoutModal(id, name, estPrice, estQty) {
        document.getElementById('modalItemName').innerText = name;
        document.getElementById('modalPrice').value = estPrice;
        document.getElementById('modalQty').value = estQty;
        document.getElementById('checkoutForm').action = "/shopping/purchase/" + id;
        new bootstrap.Modal(document.getElementById('checkoutModal')).show();
    }
</script>
@endsection
