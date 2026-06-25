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
        <div class="card bg-primary text-white mb-4">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">
                    <i class="bi bi-box-arrow-in-down me-2"></i>Timbang Masuk Multi Product
                </h3>
                <p class="mb-0 mt-2" style="font-size: 0.9rem;">Pilih SPM untuk ditimbang masuk (lebih dari 1 produk)
                </p>
            </div>
        </div>

        <!-- Search & Selection Info -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label fw-bold">
                            <i class="bi bi-search"></i> Cari SPM
                        </label>
                        <input type="text" class="form-control" id="search" wire:model.live="search"
                            placeholder="Cari berdasarkan No. Kendaraan, Driver, atau SPM No...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-check2-square"></i> SPM Dipilih
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white">
                                <i class="bi bi-collection"></i>
                            </span>
                            <input type="text" class="form-control fw-bold text-success"
                                value="{{ count($selectedSpms) }} SPM" readonly>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">&nbsp;</label>
                        <div class="d-grid gap-2">
                            @if (count($selectedSpms) > 0)
                                <button type="button" class="btn btn-primary" wire:click="openWeighInModal">
                                    <i class="bi bi-scale"></i> Proses Timbang Masuk
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="bi bi-scale"></i> Pilih SPM Dulu
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                @if (count($selectedSpms) > 0)
                    <div class="alert alert-info mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="bi bi-info-circle"></i> {{ count($selectedSpms) }} SPM telah
                                    dipilih</strong>
                                <button type="button" class="btn btn-sm btn-link"
                                    onclick="document.getElementById('selectedSpmsList').classList.toggle('d-none')">
                                    Lihat Detail
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="clearSelection">
                                <i class="bi bi-x-circle"></i> Clear
                            </button>
                        </div>
                        <div id="selectedSpmsList" class="mt-2 d-none">
                            <ul class="mb-0">
                                @foreach ($selectedSpmDetails as $spm)
                                    <li>
                                        <strong>{{ $spm->spmNo }}</strong> -
                                        {{ $spm->product->itemName ?? 'N/A' }}
                                        ({{ $spm->qtyKarung }} karung)
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- SPM Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Daftar SPM Tersedia
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 5%;">
                                    <i class="bi bi-check-square"></i>
                                </th>
                                <th scope="col" style="width: 10%;">SPM No</th>
                                <th scope="col" style="width: 15%;">Kendaraan</th>
                                <th scope="col" style="width: 15%;">Driver</th>
                                <th scope="col" style="width: 20%;">Product</th>
                                <th scope="col" class="text-center" style="width: 10%;">Qty Karung</th>
                                <th scope="col" style="width: 15%;">Customer</th>
                                <th scope="col" style="width: 10%;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($spms as $spm)
                                <tr class="{{ in_array($spm->id, $selectedSpms) ? 'table-success' : '' }}">
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="form-check-input"
                                            wire:click="toggleSpm({{ $spm->id }})"
                                            {{ in_array($spm->id, $selectedSpms) ? 'checked' : '' }}>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-info">{{ $spm->spmNo }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ $spm->carID }}</strong>
                                    </td>
                                    <td class="align-middle">{{ $spm->driver }}</td>
                                    <td class="align-middle">
                                        <small class="text-muted">{{ $spm->product->itemCode ?? 'N/A' }}</small><br>
                                        <strong>{{ $spm->product->itemName ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-secondary">{{ $spm->qtyKarung }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <small>{{ $spm->customer->custName ?? 'N/A' }}</small>
                                    </td>
                                    <td class="align-middle">
                                        <small>{{ $spm->created_at ? $spm->created_at->format('d/m/Y') : '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Tidak ada SPM tersedia</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $spms->links() }}
            </div>
        </div>
    </div>

    {{-- Weigh-In Modal --}}
    @if ($showModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-scale"></i> Timbang Masuk - {{ count($selectedSpms) }} Product
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="processWeighIn">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Driver <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('driver') is-invalid @enderror"
                                        wire:model="driver" required>
                                    @error('driver')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">No. Kendaraan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('carID') is-invalid @enderror"
                                        wire:model="carID" required>
                                    @error('carID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Customer ID <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('custID') is-invalid @enderror"
                                        wire:model="custID" required>
                                    @error('custID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Customer Name</label>
                                    <input type="text" class="form-control" wire:model="custName">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Transporter ID</label>
                                    <input type="text" class="form-control" wire:model="transpID">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Transporter Name</label>
                                    <input type="text" class="form-control" wire:model="transpName">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">DO No</label>
                                    <input type="text" class="form-control" wire:model="doNo">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">PO No</label>
                                    <input type="text" class="form-control" wire:model="poNo">
                                </div>
                            </div>

                            <!-- Manual Mode Toggle -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="manualMode"
                                        wire:model="manualMode">
                                    <label class="form-check-label" for="manualMode">
                                        <strong>Mode Manual</strong> <small class="text-muted">(Aktifkan jika API
                                            timbangan tidak tersedia)</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Weighing Scale Section -->
                            <div class="mb-3" x-data="{ manualMode: @entangle('manualMode') }">
                                <label class="form-label fw-bold">Pilih Timbangan
                                    <span class="text-danger" x-show="!manualMode">*</span>
                                </label>
                                <select class="form-select @error('timbanganID') is-invalid @enderror"
                                    wire:model="timbanganID" x-bind:disabled="manualMode">
                                    <option value="">---Pilih Timbangan---</option>
                                    @foreach ($timbangan as $item)
                                        <option value="{{ $item->timbanganID }}">{{ $item->timbanganNama }}</option>
                                    @endforeach
                                </select>
                                @error('timbanganID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3" x-data="{ manualMode: @entangle('manualMode') }">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Berat Timbangan (kg)</label>
                                    <input type="text" class="form-control" wire:model="timbangin"
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

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tare Weight (kg) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01"
                                    class="form-control @error('tareWeight') is-invalid @enderror"
                                    wire:model="tareWeight" required>
                                @error('tareWeight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Remarks</label>
                                <textarea class="form-control" wire:model="remarks" rows="3"></textarea>
                            </div>

                            <div class="alert alert-info">
                                <strong><i class="bi bi-info-circle"></i> Info:</strong><br>
                                • Total {{ count($selectedSpms) }} product akan ditimbang<br>
                                • Theoretical weight akan dihitung otomatis berdasarkan qty karung × weight standard
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="processWeighIn">
                            <i class="bi bi-save"></i> Proses Timbang Masuk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
