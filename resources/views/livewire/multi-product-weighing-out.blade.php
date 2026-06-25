<div>
    {{-- Success/Error Messages --}}
    @if (session()->has('success'))
        <div class="pt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> {!! session('success') !!}
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
        <div class="card bg-success text-white mb-4">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">
                    <i class="bi bi-box-arrow-up me-2"></i>Timbang Keluar Multi Product
                </h3>
                <p class="mb-0 mt-2" style="font-size: 0.9rem;">Proses timbang keluar dan perhitungan faktor koreksi</p>
            </div>
        </div>

        <!-- Search Box -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label fw-bold">
                            <i class="bi bi-search"></i> Cari Transaksi
                        </label>
                        <input type="text" class="form-control" id="search" wire:model.live="search"
                            placeholder="Cari berdasarkan Trans No, No. Kendaraan, atau Driver...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-info-circle"></i> Total Menunggu Timbang Keluar
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white">
                                <i class="bi bi-hourglass-split"></i>
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
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Transaksi Menunggu Timbang Keluar
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 12%;">Trans No</th>
                                <th scope="col" style="width: 12%;">Kendaraan</th>
                                <th scope="col" style="width: 12%;">Driver</th>
                                <th scope="col" style="width: 15%;">Customer</th>
                                <th scope="col" class="text-center" style="width: 8%;">Products</th>
                                <th scope="col" class="text-end" style="width: 10%;">Tare (kg)</th>
                                <th scope="col" style="width: 13%;">Timbang Masuk</th>
                                <th scope="col" class="text-center" style="width: 18%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $trans)
                                <tr @if ($trans->approvals()->where('action', 'REJECTED')->exists()) class="table-warning" @endif>
                                    <td class="align-middle">
                                        <span class="badge bg-primary">{{ $trans->trans_no }}</span>
                                        @if ($trans->approvals()->where('action', 'REJECTED')->exists())
                                            <br><span class="badge bg-warning text-dark mt-1"
                                                title="Transaksi ini pernah di-reject sebelumnya">
                                                <i class="bi bi-arrow-clockwise"></i> Re-Weigh
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ $trans->carID }}</strong>
                                    </td>
                                    <td class="align-middle">{{ $trans->driver }}</td>
                                    <td class="align-middle">
                                        <small class="text-muted">{{ $trans->custID }}</small><br>
                                        <strong>{{ $trans->custName }}</strong>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-info">{{ $trans->details->count() }}</span>
                                    </td>
                                    <td class="text-end align-middle">
                                        <strong>{{ number_format($trans->tare_weight, 2) }}</strong>
                                    </td>
                                    <td class="align-middle">
                                        <small>{{ $trans->weigh_in_time ? $trans->weigh_in_time->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-info me-1"
                                            wire:click="viewDetails({{ $trans->id }})" title="Lihat Detail">
                                            <i class="bi bi-eye">Lihat</i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success me-1"
                                            wire:click="openWeighOutModal({{ $trans->id }})" title="Timbang Keluar">
                                            <i class="bi bi-scale">Timbang Keluar</i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="if(confirm('Yakin ingin membatalkan transaksi ini?')) { @this.call('cancelTransaction', {{ $trans->id }}) }"
                                            title="Batalkan">
                                            <i class="bi bi-x-circle">Batalkan</i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Tidak ada transaksi menunggu timbang keluar</p>
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

        <!-- Completed/Approved Transactions -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-check-circle"></i> Transaksi Selesai / Approved
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 10%;">Trans No</th>
                                <th scope="col" style="width: 10%;">Kendaraan</th>
                                <th scope="col" style="width: 10%;">Driver</th>
                                <th scope="col" style="width: 10%;">Customer</th>
                                <th scope="col" class="text-center" style="width: 7%;">Products</th>
                                <th scope="col" class="text-end" style="width: 8%;">Net (kg)</th>
                                <th scope="col" class="text-center" style="width: 7%;">K Factor</th>
                                <th scope="col" style="width: 10%;">Timbang Out</th>
                                <th scope="col" class="text-center" style="width: 8%;">Status</th>
                                <th scope="col" class="text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($completedTransactions as $trans)
                                <tr>
                                    <td class="align-middle">
                                        <span class="badge bg-primary">{{ $trans->trans_no }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ $trans->carID }}</strong>
                                    </td>
                                    <td class="align-middle">{{ $trans->driver }}</td>
                                    <td class="align-middle">
                                        <small>{{ $trans->custID }}</small>
                                    </td>
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
                                    <td class="align-middle">
                                        <small>{{ $trans->weigh_out_time ? $trans->weigh_out_time->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($trans->status === 'COMPLETED')
                                            <span class="badge bg-success">COMPLETED</span>
                                        @elseif($trans->status === 'APPROVED')
                                            <span class="badge bg-primary">APPROVED</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-info me-1"
                                            wire:click="viewDetails({{ $trans->id }})" title="Lihat Detail">
                                            <i class="bi bi-eye">Lihat</i>
                                        </button>
                                        <a href="/cetakoutmp/{{ $trans->id }}" class="btn btn-sm btn-primary"
                                            target="_blank" title="Cetak Struk">
                                            <i class="bi bi-printer"></i> Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Belum ada transaksi selesai</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $completedTransactions->links('pagination::bootstrap-5', ['pageName' => 'completedPage']) }}
            </div>
        </div>
    </div>

    {{-- Weigh-Out Modal --}}
    @if ($showModal && $selectedTransaction)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-scale"></i> Timbang Keluar - {{ $selectedTransaction->trans_no }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Transaction Info -->
                        <div class="card mb-3 bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Kendaraan:</strong>
                                            {{ $selectedTransaction->carID }}</p>
                                        <p class="mb-1"><strong>Driver:</strong> {{ $selectedTransaction->driver }}
                                        </p>
                                        <p class="mb-1"><strong>Customer:</strong>
                                            {{ $selectedTransaction->custName }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Tare Weight:</strong>
                                            {{ number_format($selectedTransaction->tare_weight, 2) }} kg</p>
                                        <p class="mb-1"><strong>Theoretical Weight:</strong>
                                            {{ number_format($selectedTransaction->theoretical_weight, 2) }} kg</p>
                                        <p class="mb-1"><strong>Jumlah Product:</strong>
                                            {{ $selectedTransaction->details->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty Karung</th>
                                        <th class="text-end">Weight Std</th>
                                        <th class="text-end">Theoretical</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedTransaction->details as $detail)
                                        <tr>
                                            <td>
                                                <small class="text-muted">{{ $detail->itemCode }}</small><br>
                                                <strong>{{ $detail->itemName }}</strong>
                                            </td>
                                            <td class="text-center">{{ $detail->qty_karung }}</td>
                                            <td class="text-end">{{ number_format($detail->weight_std, 2) }}</td>
                                            <td class="text-end">{{ number_format($detail->theoretical_weight, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Manual Mode Toggle -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="manualModeOut"
                                    wire:model="manualMode">
                                <label class="form-check-label" for="manualModeOut">
                                    <strong>Mode Manual</strong> <small class="text-muted">(Aktifkan jika API timbangan
                                        tidak tersedia)</small>
                                </label>
                            </div>
                        </div>

                        <!-- Weighing Scale Section -->
                        <form wire:submit.prevent="processWeighOut">
                            <div class="mb-3" x-data="{ manualMode: @entangle('manualMode') }">
                                <label class="form-label fw-bold">Pilih Timbangan
                                    <span class="text-danger" x-show="!manualMode">*</span>
                                </label>
                                <select class="form-select @error('timbanganoutID') is-invalid @enderror"
                                    wire:model="timbanganoutID" x-bind:disabled="manualMode">
                                    <option value="">---Pilih Timbangan---</option>
                                    @foreach ($timbangan as $item)
                                        <option value="{{ $item->timbanganID }}">{{ $item->timbanganNama }}</option>
                                    @endforeach
                                </select>
                                @error('timbanganoutID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3" x-data="{ manualMode: @entangle('manualMode') }">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Berat Timbangan (kg)</label>
                                    <input type="text" class="form-control" wire:model="timbangout"
                                        x-bind:readonly="!manualMode"
                                        x-bind:placeholder="manualMode ? 'Input manual...' : 'Readonly'">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" wire:click="timbang"
                                        x-bind:disabled="manualMode">
                                        <i class="bi bi-speedometer2 me-2"></i>Timbang
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3" x-data="{ manualMode: @entangle('manualMode') }">
                                <label class="form-label fw-bold">Netto (kg)</label>
                                <input type="text" class="form-control" wire:model="netto"
                                    x-bind:readonly="!manualMode"
                                    x-bind:placeholder="manualMode ? 'Input manual...' : 'Readonly'">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Gross Weight (kg) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01"
                                    class="form-control form-control-lg @error('grossWeight') is-invalid @enderror"
                                    wire:model="grossWeight" placeholder="Masukkan gross weight..." required
                                    autofocus>
                                @error('grossWeight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <strong><i class="bi bi-calculator"></i> Perhitungan Otomatis:</strong><br>
                                • Net Weight = Gross Weight - Tare Weight<br>
                                • Correction Factor (K) = Net Weight / Theoretical Weight<br>
                                • Actual Weight per Product = Theoretical Weight × K<br>
                                • Avg per Karung = Actual Weight / Qty Karung<br>
                                • Sistem akan otomatis validasi range untuk setiap product
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="button" class="btn btn-success" wire:click="processWeighOut">
                            <i class="bi bi-check-circle"></i> Proses Timbang Keluar
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
                        <!-- Header Info -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-primary">Informasi Kendaraan</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td width="40%"><strong>Trans No:</strong></td>
                                        <td>{{ $selectedTransaction->trans_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kendaraan:</strong></td>
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
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Informasi Timbangan</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td width="40%"><strong>Tare Weight:</strong></td>
                                        <td>{{ number_format($selectedTransaction->tare_weight, 2) }} kg</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Theoretical:</strong></td>
                                        <td>{{ number_format($selectedTransaction->theoretical_weight, 2) }} kg</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Timbang Masuk:</strong></td>
                                        <td>{{ $selectedTransaction->weigh_in_time ? $selectedTransaction->weigh_in_time->format('d/m/Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>User Masuk:</strong></td>
                                        <td>{{ $selectedTransaction->userIn->name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <h6 class="text-primary">Detail Produk</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th class="text-center">Qty Karung</th>
                                        <th class="text-end">Weight Std</th>
                                        <th class="text-end">Theoretical</th>
                                        <th class="text-end">Range Min</th>
                                        <th class="text-end">Range Max</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedTransaction->details as $detail)
                                        <tr>
                                            <td>{{ $detail->itemCode }}</td>
                                            <td>{{ $detail->itemName }}</td>
                                            <td class="text-center">{{ $detail->qty_karung }}</td>
                                            <td class="text-end">{{ number_format($detail->weight_std, 2) }}</td>
                                            <td class="text-end">{{ number_format($detail->theoretical_weight, 2) }}
                                            </td>
                                            <td class="text-end">{{ number_format($detail->gross_min, 2) }}</td>
                                            <td class="text-end">{{ number_format($detail->gross_max, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
