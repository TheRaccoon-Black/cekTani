@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-1 mb-0"><span class="text-muted fw-light">Operasional /</span> Gudang & Stok</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createItemModal">
            <i class="bx bx-plus me-1"></i> Barang Baru
        </button>
    </div>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga Rata2 (HPP)</th>
                        <th>Valuasi Aset</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td><span class="badge bg-label-info">{{ $item->category }}</span></td>
                        <td>
                            <span class="fw-bold {{ $item->stock <= $item->min_stock_alert ? 'text-danger' : 'text-success' }}">
                                {{ $item->stock }} {{ $item->unit }}
                            </span>
                            @if($item->stock <= $item->min_stock_alert)
                                <i class="bx bx-error-circle text-danger" title="Stok Menipis!"></i>
                            @endif
                        </td>
                        <td>Rp {{ number_format($item->avg_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->stock * $item->avg_price, 0, ',', '.') }}</td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-label-primary"
                                onclick="openPurchaseModal({{ $item->id }}, '{{ $item->name }}', '{{ $item->unit }}')"
                                title="Beli Stok (Restock)">
                                <i class="bx bx-cart-add"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Gudang kosong.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="createItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" placeholder="NPK Mutiara" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="Pupuk">Pupuk</option>
                            <option value="Pestisida">Pestisida/Obat</option>
                            <option value="Benih">Benih/Bibit</option>
                            <option value="Perlengkapan">Perlengkapan</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Satuan</label>
                        <select name="unit" class="form-select">
                            <option value="kg">Kilogram (kg)</option>
                            <option value="liter">Liter (L)</option>
                            <option value="sachet">Sachet/Bungkus</option>
                            <option value="pcs">Pcs/Buah</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Alert Min. Stok</label>
                        <input type="number" name="min_stock_alert" class="form-control" value="5">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="purchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form id="purchaseForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Belanja Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Beli stok untuk: <strong id="itemName" class="text-primary"></strong></p>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Beli (<span id="itemUnit"></span>)</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Beli Satuan (Rp)</label>
                        <input type="number" name="price_per_unit" class="form-control" placeholder="Contoh: 15000" required>
                        <div class="form-text text-xs">Harga per kg/liter/pcs.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Beli & Catat Keuangan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPurchaseModal(id, name, unit) {
        document.getElementById('itemName').innerText = name;
        document.getElementById('itemUnit').innerText = unit;
        document.getElementById('purchaseForm').action = "/inventory/" + id + "/purchase";
        new bootstrap.Modal(document.getElementById('purchaseModal')).show();
    }
</script>
@endsection
