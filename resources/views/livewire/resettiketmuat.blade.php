<div>
    {{-- Flash Messages --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card-body">
        <div class="card-body table-responsive p-0">
            <div class="my-3 p-3 bg-body rounded shadow-sm">
                <div class="mb-3 row">
                    <div class="col">
                        <div class="card m-auto mt-3 text-white text-center bg-danger" style="max-width: 30rem;">
                            <h2>Reset Tiket Muat - Approved</h2>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <label for="tiketmuat" class="form-label small text-muted">Tiket Muat</label>
                        <input type="text" id="tiketmuat" class="form-control" placeholder="Search tiket muat..."
                            wire:model.live="katakunci">
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <label for="customer" class="form-label small text-muted">Customer</label>
                        <input type="text" id="customer" class="form-control" placeholder="Search customer..."
                            wire:model.live="katacust">
                    </div>
                    {{-- <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <label for="product" class="form-label small text-muted">Product</label>
                        <select id="product" class="form-select" multiple wire:model.live="kataproduct"
                            size="1">
                            <option value="">-- Select Products --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->itemCode }}">{{ $product->itemName }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div> --}}
                    {{-- <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <label for="tglmuat" class="form-label small text-muted">Tanggal Muat</label>
                        <input type="date" id="tglmuat" class="form-control" wire:model.live="tglMuat">
                    </div> --}}
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <label for="sudahpabrik" class="form-label small text-muted">Sudah di Pabrik</label>
                        <select id="sudahpabrik" class="form-select" wire:model.live="sudahPabrik">
                            <option value="">-- Semua --</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                {{ $datatiketmuat->links() }}

                <!-- Bulk Reset Button -->
                @if (count($selectedItems) > 0)
                    <div class="alert alert-info d-flex justify-content-between align-items-center" role="alert">
                        <div>
                            <i class="bi bi-check2-square me-2"></i>
                            <strong>{{ count($selectedItems) }}</strong> item dipilih
                        </div>
                        <button wire:click="confirmBulkReset" class="btn btn-danger btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i> Bulk Reset ({{ count($selectedItems) }})
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">
                                    <input type="checkbox" wire:model.live="selectAll" wire:click="toggleSelectAll"
                                        class="form-check-input" style="cursor: pointer;" title="Select All">
                                </th>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>Tiket Muat</th>
                                <th>SPPB No</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th class="text-end">Berat (Kg)</th>
                                <th class="text-center">Karung</th>
                                <th>Tgl Muat</th>
                                <th>Plat No</th>
                                <th>Transporter</th>
                                <th class="text-center">Approved</th>
                                <th class="text-center">Sudah di Pabrik</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalkg = 0;
                                $no = $datatiketmuat->firstItem();
                            @endphp
                            @forelse ($datatiketmuat as $key)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" wire:model.live="selectedItems"
                                            value="{{ $key->id }}" class="form-check-input"
                                            style="cursor: pointer;">
                                    </td>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{ $key->pendfNo }}</td>
                                    <td>{{ $key->sppbNo }}</td>
                                    <td>{{ $key->custName }}</td>
                                    <td>{{ $key->itemName }}</td>
                                    <td class="text-end">{{ number_format($key->tmQtyKg) }}</td>
                                    <td class="text-center">{{ $key->tmQtyKarung }}</td>
                                    <td>{{ $key->tglMuat }}</td>
                                    <td>{{ $key->tmCarID }}</td>
                                    <td>{{ $key->tmTranspName }}</td>
                                    <td class="text-center">
                                        @if ($key->isMktApp)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($key->isSecCek)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="confirmReset('{{ $key->id }}')"
                                            class="btn btn-danger btn-sm">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </button>
                                    </td>
                                </tr>
                                @php
                                    $totalkg += $key->tmQtyKg;
                                @endphp
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center text-muted py-4">
                                        No data found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($datatiketmuat->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Total Weight:</td>
                                    <td class="text-end fw-bold">{{ number_format($totalkg) }} Kg</td>
                                    <td colspan="7"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                    {{ $datatiketmuat->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Confirmation Modal -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <!-- Modal Header -->
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            @if ($isBulkMode)
                                Konfirmasi Bulk Reset ({{ count($resetData) }} Items)
                            @else
                                Konfirmasi Reset Approval
                            @endif
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 mb-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Perhatian!</strong> Tindakan ini akan mereset approval dan mengembalikan kuantitas
                            ke SPPB.
                        </div>

                        @if (!empty($resetData))
                            @if ($isBulkMode)
                                <!-- Bulk Reset Display -->
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">
                                            <i class="bi bi-list-check me-2"></i>
                                            Daftar Tiket yang akan direset ({{ count($resetData) }} items):
                                        </h6>
                                        <div style="max-height: 400px; overflow-y: auto;">
                                            @php
                                                $totalBeratBulk = 0;
                                            @endphp
                                            @foreach ($resetData as $index => $item)
                                                @php
                                                    $totalBeratBulk += $item['tmQtyKg'] ?? 0;
                                                @endphp
                                                <div class="card mb-2 border">
                                                    <div class="card-body p-2">
                                                        <div class="row align-items-center">
                                                            <div class="col-1 text-center">
                                                                <span
                                                                    class="badge bg-secondary">{{ $index + 1 }}</span>
                                                            </div>
                                                            <div class="col-11">
                                                                <div class="row small">
                                                                    <div class="col-md-6">
                                                                        <strong>{{ $item['pendfNo'] ?? '-' }}</strong><br>
                                                                        <span class="text-muted">SPPB:
                                                                            {{ $item['sppbNo'] ?? '-' }}</span><br>
                                                                        <span
                                                                            class="text-muted">{{ $item['custName'] ?? '-' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <span
                                                                            class="text-muted">{{ $item['itemName'] ?? '-' }}</span><br>
                                                                        <span
                                                                            class="text-muted">{{ $item['tmCarID'] ?? '-' }}</span><br>
                                                                        <span
                                                                            class="badge bg-primary">{{ number_format($item['tmQtyKg'] ?? 0) }}
                                                                            Kg</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="alert alert-info border-0 mt-3 mb-0" role="alert">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="bi bi-arrow-return-left me-2"></i>
                                                    <strong>Total Berat yang akan dikembalikan:</strong>
                                                </span>
                                                <span
                                                    class="badge bg-primary fs-5">{{ number_format($totalBeratBulk) }}
                                                    Kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Single Reset Display -->
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Detail Tiket Muat:</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted" style="width: 40%;"><strong>Tiket
                                                            Muat:</strong></td>
                                                    <td>{{ $resetData['pendfNo'] ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><strong>SPPB No:</strong></td>
                                                    <td>{{ $resetData['sppbNo'] ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><strong>Customer:</strong></td>
                                                    <td>{{ $resetData['custName'] ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><strong>Product:</strong></td>
                                                    <td>{{ $resetData['itemName'] ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><strong>Plat No:</strong></td>
                                                    <td>{{ $resetData['tmCarID'] ?? '-' }}</td>
                                                </tr>
                                                <tr class="border-top">
                                                    <td class="text-muted"><strong>Berat (Kg):</strong></td>
                                                    <td><span
                                                            class="badge bg-primary fs-6">{{ number_format($resetData['tmQtyKg'] ?? 0) }}
                                                            Kg</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><strong>Karung:</strong></td>
                                                    <td>{{ number_format($resetData['tmQtyKarung'] ?? 0) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 mt-3 mb-0" role="alert">
                                    <i class="bi bi-arrow-return-left me-2"></i>
                                    Berat <strong>{{ number_format($resetData['tmQtyKg'] ?? 0) }} Kg</strong> akan
                                    dikembalikan ke SPPB.
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="resetApproval">
                            <i class="bi bi-check-circle me-1"></i>
                            @if ($isBulkMode)
                                Ya, Reset {{ count($resetData) }} Items
                            @else
                                Ya, Reset Approval
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Custom Styles -->
    <style>
        .modal.show {
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content {
            animation: slideDown 0.3s ease-in-out;
            border-radius: 15px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
            transition: all 0.3s ease;
        }

        /* Checkbox styling */
        .form-check-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-check-input:hover {
            transform: scale(1.1);
            transition: all 0.2s ease;
        }

        /* Row hover effect when checkbox is checked */
        tr:has(.form-check-input:checked) {
            background-color: #fff3cd !important;
        }

        /* Bulk action alert animation */
        .alert {
            animation: slideInDown 0.3s ease-in-out;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Scrollbar styling for modal list */
        .card-body div::-webkit-scrollbar {
            width: 8px;
        }

        .card-body div::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .card-body div::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .card-body div::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</div>
