<div>
    {{-- Success/Error Messages --}}
    @if (session()->has('success'))
        <div class="pt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="pt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <!-- Header Card -->
        <div class="card bg-warning text-dark mb-4">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">
                    <i class="bi bi-clipboard-check me-2"></i>Approval Multi Product
                </h3>
                <p class="mb-0 mt-2" style="font-size: 0.9rem;">Approve atau Reject transaksi yang out of range</p>
                @if ($pendingCount > 0)
                    <span class="badge bg-danger mt-2" style="font-size: 1rem;">
                        {{ $pendingCount }} Transaksi Menunggu Approval
                    </span>
                @endif
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label fw-bold">
                            <i class="bi bi-search"></i> Cari Transaksi
                        </label>
                        <input type="text" class="form-control" id="search" wire:model.live="search"
                            placeholder="Cari berdasarkan Trans No, Kendaraan, Driver...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="filterStatus" class="form-label fw-bold">
                            <i class="bi bi-funnel"></i> Filter Status
                        </label>
                        <select class="form-select" id="filterStatus" wire:model.live="filterStatus">
                            <option value="PENDING_APPROVAL">⏳ Pending Approval</option>
                            <option value="APPROVED">✓ Approved</option>
                            <option value="REJECTED">✗ Rejected</option>
                            <option value="ALL">Semua Status</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-info-circle"></i> Total Transaksi
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-warning text-dark">
                                <i class="bi bi-list-check"></i>
                            </span>
                            <input type="text" class="form-control fw-bold"
                                value="{{ $transactions->total() }} Transaksi" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Daftar Transaksi Approval
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 10%;">Trans No</th>
                                <th scope="col" style="width: 10%;">Kendaraan</th>
                                <th scope="col" style="width: 12%;">Driver</th>
                                <th scope="col" class="text-center" style="width: 6%;">Products</th>
                                <th scope="col" class="text-end" style="width: 8%;">Net (kg)</th>
                                <th scope="col" class="text-center" style="width: 8%;">Factor K</th>
                                <th scope="col" class="text-center" style="width: 10%;">Status</th>
                                <th scope="col" style="width: 12%;">Timbang Out</th>
                                <th scope="col" class="text-center" style="width: 24%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $trans)
                                <tr>
                                    <td class="align-middle">
                                        <span class="badge bg-primary">{{ $trans->trans_no }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ $trans->carID }}</strong>
                                    </td>
                                    <td class="align-middle">{{ $trans->driver }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-info">{{ $trans->details->count() }}</span>
                                    </td>
                                    <td class="text-end align-middle">
                                        <strong>{{ number_format($trans->net_weight, 2) }}</strong>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span
                                            class="badge bg-secondary">{{ number_format($trans->correction_factor, 4) }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($trans->status === 'PENDING_APPROVAL')
                                            <span class="badge bg-warning text-dark">⏳ Pending</span>
                                        @elseif ($trans->status === 'APPROVED')
                                            <span class="badge bg-success">✓ Approved</span>
                                        @elseif ($trans->status === 'REJECTED')
                                            <span class="badge bg-danger">✗ Rejected</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <small>{{ $trans->weigh_out_time ? $trans->weigh_out_time->format('d/m/Y H:i') : '-' }}</small><br>
                                        <small class="text-muted">{{ $trans->userOut->name ?? '-' }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-info me-1"
                                            wire:click="viewDetails({{ $trans->id }})" title="Lihat Detail">
                                            <i class="bi bi-eye">View</i>
                                        </button>
                                        @if ($trans->status === 'PENDING_APPROVAL')
                                            <button type="button" class="btn btn-sm btn-success me-1"
                                                wire:click="openApproveModal({{ $trans->id }})" title="Approve">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="openRejectModal({{ $trans->id }})" title="Reject">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        @elseif ($trans->status === 'REJECTED')
                                            <button type="button" class="btn btn-sm btn-warning"
                                                onclick="if(confirm('Reset transaksi ini untuk ditimbang keluar ulang?')) { @this.call('reweighTransaction', {{ $trans->id }}) }"
                                                title="Timbang Ulang">
                                                <i class="bi bi-arrow-clockwise"></i> Re-Weigh
                                            </button>
                                        @else
                                            <span class="badge bg-secondary">Sudah Diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Tidak ada transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    @if ($showApproveModal && $selectedTransaction)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-check-circle"></i> Approve Transaksi -
                            {{ $selectedTransaction->trans_no }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            wire:click="closeApproveModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Summary Info -->
                        <div class="alert alert-warning">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Produk Out of Range:
                            </h6>
                            <ul class="mb-0">
                                @foreach ($selectedTransaction->details as $detail)
                                    @if (!$detail->is_in_range)
                                        <li>
                                            <strong>{{ $detail->itemName }}</strong>:
                                            Avg {{ number_format($detail->avg_per_karung, 2) }} kg/karung
                                            (Range: {{ number_format($detail->gross_min, 2) }} -
                                            {{ number_format($detail->gross_max, 2) }})
                                            <span class="badge bg-danger">{{ $detail->deviation_status }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        <!-- Transaction Details -->
                        <table class="table table-sm table-bordered mb-3">
                            <tr>
                                <td width="30%"><strong>Kendaraan:</strong></td>
                                <td>{{ $selectedTransaction->carID }} - {{ $selectedTransaction->driver }}</td>
                            </tr>
                            <tr>
                                <td><strong>Net Weight:</strong></td>
                                <td>{{ number_format($selectedTransaction->net_weight, 2) }} kg</td>
                            </tr>
                            <tr>
                                <td><strong>Correction Factor:</strong></td>
                                <td>{{ number_format($selectedTransaction->correction_factor, 4) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Products:</strong></td>
                                <td>{{ $selectedTransaction->details->count() }} product</td>
                            </tr>
                        </table>

                        <!-- Remarks -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan (Optional)</label>
                            <textarea class="form-control" wire:model="remarks" rows="3" placeholder="Masukkan catatan approval..."></textarea>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> <strong>Dengan meng-approve transaksi ini, Anda
                                menyetujui bahwa:</strong><br>
                            • Produk yang out of range dapat diterima<br>
                            • Transaksi akan berstatus APPROVED<br>
                            • History approval akan tersimpan untuk audit
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeApproveModal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="button" class="btn btn-success" wire:click="approveTransaction">
                            <i class="bi bi-check-circle"></i> Ya, Approve Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if ($showRejectModal && $selectedTransaction)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-x-circle"></i> Reject Transaksi - {{ $selectedTransaction->trans_no }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            wire:click="closeRejectModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Summary Info -->
                        <div class="alert alert-danger">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Produk Out of Range:
                            </h6>
                            <ul class="mb-0">
                                @foreach ($selectedTransaction->details as $detail)
                                    @if (!$detail->is_in_range)
                                        <li>
                                            <strong>{{ $detail->itemName }}</strong>:
                                            Avg {{ number_format($detail->avg_per_karung, 2) }} kg/karung
                                            (Range: {{ number_format($detail->gross_min, 2) }} -
                                            {{ number_format($detail->gross_max, 2) }})
                                            <span class="badge bg-danger">{{ $detail->deviation_status }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        <!-- Transaction Details -->
                        <table class="table table-sm table-bordered mb-3">
                            <tr>
                                <td width="30%"><strong>Kendaraan:</strong></td>
                                <td>{{ $selectedTransaction->carID }} - {{ $selectedTransaction->driver }}</td>
                            </tr>
                            <tr>
                                <td><strong>Net Weight:</strong></td>
                                <td>{{ number_format($selectedTransaction->net_weight, 2) }} kg</td>
                            </tr>
                            <tr>
                                <td><strong>Correction Factor:</strong></td>
                                <td>{{ number_format($selectedTransaction->correction_factor, 4) }}</td>
                            </tr>
                        </table>

                        <!-- Remarks (Required for Reject) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Alasan Reject <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" wire:model="remarks" rows="4"
                                placeholder="Jelaskan alasan reject..." required></textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle"></i> <strong>Dengan me-reject transaksi
                                ini:</strong><br>
                            • Transaksi akan berstatus REJECTED<br>
                            • Perlu tindakan lebih lanjut untuk menangani produk yang ditolak<br>
                            • History rejection akan tersimpan untuk audit
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeRejectModal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="rejectTransaction">
                            <i class="bi bi-x-circle"></i> Ya, Reject Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if ($showDetailModal && $selectedTransaction)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-info-circle"></i> Detail Transaksi - {{ $selectedTransaction->trans_no }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Status Badge -->
                        <div class="text-center mb-3">
                            @if ($selectedTransaction->status === 'PENDING_APPROVAL')
                                <h4><span class="badge bg-warning text-dark">⏳ Pending Approval</span></h4>
                            @elseif ($selectedTransaction->status === 'APPROVED')
                                <h4><span class="badge bg-success">✓ Approved</span></h4>
                            @elseif ($selectedTransaction->status === 'REJECTED')
                                <h4><span class="badge bg-danger">✗ Rejected</span></h4>
                            @endif
                        </div>

                        <!-- Transaction Info -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-primary">Informasi Timbangan</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td width="40%"><strong>Trans No:</strong></td>
                                        <td>{{ $selectedTransaction->trans_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tare Weight:</strong></td>
                                        <td>{{ number_format($selectedTransaction->tare_weight, 2) }} kg</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Gross Weight:</strong></td>
                                        <td>{{ number_format($selectedTransaction->gross_weight, 2) }} kg</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Net Weight:</strong></td>
                                        <td><strong
                                                class="text-success">{{ number_format($selectedTransaction->net_weight, 2) }}
                                                kg</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Theoretical:</strong></td>
                                        <td>{{ number_format($selectedTransaction->theoretical_weight, 2) }} kg</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Correction Factor:</strong></td>
                                        <td><span
                                                class="badge bg-primary">{{ number_format($selectedTransaction->correction_factor, 4) }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Informasi Lainnya</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td width="40%"><strong>Kendaraan:</strong></td>
                                        <td>{{ $selectedTransaction->carID }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Driver:</strong></td>
                                        <td>{{ $selectedTransaction->driver }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td>{{ $selectedTransaction->custName }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Timbang In:</strong></td>
                                        <td>{{ $selectedTransaction->weigh_in_time ? $selectedTransaction->weigh_in_time->format('d/m/Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Timbang Out:</strong></td>
                                        <td>{{ $selectedTransaction->weigh_out_time ? $selectedTransaction->weigh_out_time->format('d/m/Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>User Out:</strong></td>
                                        <td>{{ $selectedTransaction->userOut->name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <h6 class="text-primary">Detail Produk</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Theoretical</th>
                                        <th class="text-end">Actual</th>
                                        <th class="text-end">Avg/Karung</th>
                                        <th class="text-end">Range Min</th>
                                        <th class="text-end">Range Max</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedTransaction->details as $detail)
                                        <tr class="{{ !$detail->is_in_range ? 'table-danger' : 'table-success' }}">
                                            <td>
                                                <small class="text-muted">{{ $detail->itemCode }}</small><br>
                                                <strong>{{ $detail->itemName }}</strong>
                                            </td>
                                            <td class="text-center">{{ $detail->qty_karung }}</td>
                                            <td class="text-end">{{ number_format($detail->theoretical_weight, 2) }}
                                            </td>
                                            <td class="text-end">{{ number_format($detail->actual_weight, 2) }}</td>
                                            <td class="text-end">
                                                <strong>{{ number_format($detail->avg_per_karung, 2) }}</strong>
                                            </td>
                                            <td class="text-end">{{ number_format($detail->gross_min, 2) }}</td>
                                            <td class="text-end">{{ number_format($detail->gross_max, 2) }}</td>
                                            <td class="text-center">
                                                @if ($detail->is_in_range)
                                                    <span class="badge bg-success">✓ In Range</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Out of Range</span><br>
                                                    <small class="text-muted">{{ $detail->deviation_status }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Approval History -->
                        @if ($selectedTransaction->approvals->count() > 0)
                            <h6 class="text-primary mt-3">History Approval</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Action</th>
                                            <th>Approver</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedTransaction->approvals as $approval)
                                            <tr>
                                                <td>{{ $approval->approved_at ? $approval->approved_at->format('d/m/Y H:i') : '-' }}
                                                </td>
                                                <td>
                                                    @if ($approval->action === 'APPROVED')
                                                        <span class="badge bg-success">✓ Approved</span>
                                                    @else
                                                        <span class="badge bg-danger">✗ Rejected</span>
                                                    @endif
                                                </td>
                                                <td>{{ $approval->approver->name ?? '-' }}</td>
                                                <td>{{ $approval->remarks ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">
                            <i class="bi bi-x-circle"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
