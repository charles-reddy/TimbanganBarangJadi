<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Laporan Timbangan (Single & Multi-Product)</h2>
            <p class="text-muted">Dashboard dan laporan gabungan timbangan single product dan multi-product</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Transaksi</h6>
                            <h3 class="mb-0">{{ number_format($summary['total_transactions']) }}</h3>
                        </div>
                        <div class="text-primary fs-1">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Single Product</h6>
                            <h3 class="mb-0">{{ number_format($summary['single_product_count']) }}</h3>
                        </div>
                        <div class="text-success fs-1">
                            <i class="bi bi-box"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Multi Product</h6>
                            <h3 class="mb-0">{{ number_format($summary['multi_product_count']) }}</h3>
                        </div>
                        <div class="text-info fs-1">
                            <i class="bi bi-boxes"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Netto</h6>
                            <h3 class="mb-0">{{ number_format($summary['total_net_weight'], 0) }}</h3>
                            <small class="text-muted">kg</small>
                        </div>
                        <div class="text-warning fs-1">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Dari</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Sampai</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipe Transaksi</label>
                    <select wire:model.live="transType" class="form-select">
                        <option value="ALL">Semua</option>
                        <option value="SINGLE">Single Product</option>
                        <option value="MULTI">Multi Product</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="No. Transaksi, Polisi, Driver...">
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button wire:click="exportExcel" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Data Transaksi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>No. Polisi</th>
                            <th>Driver</th>
                            <th>Customer</th>
                            <th>Produk</th>
                            <th class="text-end">Netto (kg)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $trans)
                            <tr>
                                <td>
                                    <strong>{{ $trans->trans_no }}</strong>
                                </td>
                                <td>
                                    @if ($trans->trans_type == 'SINGLE')
                                        <span class="badge bg-success">Single</span>
                                    @else
                                        <span class="badge bg-info">Multi</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($trans->weigh_out_time)->format('d/m/Y H:i') }}</small>
                                </td>
                                <td><strong>{{ $trans->carID }}</strong></td>
                                <td>{{ $trans->driver }}</td>
                                <td>{{ $trans->custName }}</td>
                                <td>
                                    <small>{{ Str::limit($trans->itemName ?? '-', 30) }}</small>
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($trans->net_weight, 2) }}</strong>
                                </td>
                                <td>
                                    @if ($trans->status == 'COMPLETED' || $trans->status == 'APPROVED')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>{{ $trans->status }}
                                        </span>
                                    @elseif($trans->status == 'PENDING_APPROVAL')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock me-1"></i>Pending
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $trans->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada data</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
