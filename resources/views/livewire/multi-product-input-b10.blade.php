<div>
    {{-- Success/Error Messages (Only show when modal is closed) --}}
    @if (!$showModal)
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
    @endif

    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <!-- Header Card -->
        <div class="card bg-success text-white mb-4">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>Input B10 Multi Product
                </h3>
                <p class="mb-0 mt-2" style="font-size: 0.9rem;">Input data B10 untuk transaksi yang sudah ditimbang
                    masuk</p>
            </div>
        </div>

        <!-- Filter Card -->
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
                        <label for="filterDate" class="form-label fw-bold">
                            <i class="bi bi-calendar"></i> Filter Tanggal
                        </label>
                        <input type="date" class="form-control" id="filterDate" wire:model.live="filterDate">
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi List -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-list-task"></i> Daftar Transaksi Menunggu Input B10
                </h5>
            </div>
            <div class="card-body p-0">
                @forelse ($headers as $header)
                    <div class="border-bottom p-3 hover-bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-2">
                                    <span class="badge bg-info">{{ $header->trans_no }}</span>
                                    <span class="ms-2"><strong>{{ $header->carID }}</strong></span>
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> Driver:
                                            <strong>{{ $header->driver }}</strong><br>
                                            <i class="bi bi-building"></i> Customer:
                                            {{ $header->custName ?? $header->custID }}<br>
                                            <i class="bi bi-box-seam"></i> Products:
                                            <strong>{{ $header->details->count() }} items</strong>
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-check"></i> Timbang Masuk:
                                            {{ $header->weigh_in_time ? $header->weigh_in_time->format('d/m/Y H:i') : '-' }}<br>
                                            <i class="bi bi-person-badge"></i> User:
                                            {{ $header->userIn->username ?? '-' }}<br>
                                            <i class="bi bi-truck"></i> Tare Weight:
                                            {{ number_format($header->tare_weight, 2) }} kg
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split"></i> Menunggu Input B10
                                    </span>
                                    @php
                                        $completedCount = $header->details
                                            ->filter(fn($d) => !empty($d->b10QtyKarung))
                                            ->count();
                                        $totalCount = $header->details->count();
                                    @endphp
                                    <small class="ms-2 text-muted">
                                        ({{ $completedCount }}/{{ $totalCount }} completed)
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-success"
                                    wire:click="openInputModal({{ $header->id }})">
                                    <i class="bi bi-pencil-square"></i> Input B10
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-2">Tidak ada transaksi yang menunggu input B10</p>
                    </div>
                @endforelse
            </div>
            <div class="card-footer">
                {{ $headers->links() }}
            </div>
        </div>
    </div>

    {{-- Input B10 Modal --}}
    @if ($showModal && $selectedHeader)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square"></i> Input B10 - Trans No: {{ $selectedHeader->trans_no }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Error/Success Messages Inside Modal --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="bi bi-exclamation-triangle-fill"></i> Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="bi bi-x-circle-fill"></i> Error!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <strong><i class="bi bi-info-circle"></i> Info Transaksi:</strong><br>
                            Driver: <strong>{{ $selectedHeader->driver }}</strong> |
                            Car ID: <strong>{{ $selectedHeader->carID }}</strong> |
                            Customer: {{ $selectedHeader->custName }}<br>
                            Tare Weight: {{ number_format($selectedHeader->tare_weight, 2) }} kg
                        </div>

                        <form wire:submit.prevent="saveB10Data">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 15%;">SPM No</th>
                                            <th style="width: 20%;">Product</th>
                                            <th style="width: 10%;" class="text-center">Qty SPM<br><small
                                                    class="text-muted">(info)</small></th>
                                            <th style="width: 10%;">B10 Qty<br><span class="text-danger">*</span></th>
                                            <th style="width: 12%;">Batch No<br><span class="text-danger">*</span>
                                            </th>
                                            <th style="width: 10%;">Container No</th>
                                            <th style="width: 10%;">Krani<br><span class="text-danger">*</span></th>
                                            <th style="width: 8%;">Foto<br><span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedHeader->details as $index => $detail)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-info">{{ $detail->spm->spmNo ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $detail->itemCode }}</small><br>
                                                    <strong>{{ $detail->itemName }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $detail->qty_karung }}</span>
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        class="form-control @error('b10Data.' . $detail->id . '.b10QtyKarung') is-invalid @enderror"
                                                        wire:model="b10Data.{{ $detail->id }}.b10QtyKarung"
                                                        placeholder="Qty">
                                                    @error('b10Data.' . $detail->id . '.b10QtyKarung')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        class="form-control @error('b10Data.' . $detail->id . '.b10BatchNo') is-invalid @enderror"
                                                        wire:model="b10Data.{{ $detail->id }}.b10BatchNo"
                                                        placeholder="Batch">
                                                    @error('b10Data.' . $detail->id . '.b10BatchNo')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        wire:model="b10Data.{{ $detail->id }}.kontainerNo"
                                                        placeholder="Container">
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        class="form-control @error('b10Data.' . $detail->id . '.krani') is-invalid @enderror"
                                                        wire:model="b10Data.{{ $detail->id }}.krani"
                                                        placeholder="Nama Krani">
                                                    @error('b10Data.' . $detail->id . '.krani')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="file"
                                                        class="form-control form-control-sm @error('uploadedFiles.' . $detail->id) is-invalid @enderror"
                                                        wire:model="uploadedFiles.{{ $detail->id }}"
                                                        accept="image/*">
                                                    @error('uploadedFiles.' . $detail->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror

                                                    {{-- Loading indicator --}}
                                                    <div wire:loading wire:target="uploadedFiles.{{ $detail->id }}">
                                                        <small class="text-primary">
                                                            <i class="bi bi-hourglass-split"></i> Uploading...
                                                        </small>
                                                    </div>

                                                    {{-- Image preview with click to enlarge --}}
                                                    @if (isset($uploadedFiles[$detail->id]))
                                                        <div wire:loading.remove
                                                            wire:target="uploadedFiles.{{ $detail->id }}"
                                                            class="mt-2">
                                                            <div class="position-relative d-inline-block">
                                                                <img src="{{ $uploadedFiles[$detail->id]->temporaryUrl() }}"
                                                                    class="img-thumbnail image-preview-enlarge"
                                                                    style="max-width: 120px; max-height: 120px; object-fit: cover; cursor: pointer;"
                                                                    alt="Preview"
                                                                    data-image-url="{{ $uploadedFiles[$detail->id]->temporaryUrl() }}"
                                                                    title="Klik untuk memperbesar">
                                                                <span class="position-absolute top-0 end-0 m-1">
                                                                    <i class="bi bi-zoom-in text-white bg-dark bg-opacity-75 rounded px-1"
                                                                        style="font-size: 0.8rem;"></i>
                                                                </span>
                                                            </div>
                                                            <div class="text-success mt-1">
                                                                <small>
                                                                    <i class="bi bi-check-circle-fill"></i>
                                                                    {{ $uploadedFiles[$detail->id]->getClientOriginalName() }}
                                                                </small>
                                                            </div>
                                                            <div class="text-muted">
                                                                <small>
                                                                    {{ number_format($uploadedFiles[$detail->id]->getSize() / 1024, 1) }}
                                                                    KB
                                                                </small>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-warning mt-3">
                                <strong><i class="bi bi-exclamation-triangle"></i> Perhatian:</strong><br>
                                • Field dengan tanda (<span class="text-danger">*</span>) wajib diisi untuk semua
                                product<br>
                                • <strong>B10 Qty</strong> adalah jumlah karung ACTUAL yang di-load (bisa berbeda dengan
                                Qty SPM)<br>
                                • Upload foto form loading untuk setiap product (max 1 MB)<br>
                                • Pastikan semua data sudah benar sebelum menyimpan
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="button" class="btn btn-success" wire:click="saveB10Data">
                            <i class="bi bi-save"></i> Simpan Semua Data B10
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
    </style>

    <script>
        // Global function untuk enlarge image preview
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('image-preview-enlarge')) {
                const imageSrc = e.target.getAttribute('data-image-url');
                if (imageSrc) {
                    showImageEnlargeModal(imageSrc);
                }
            }
        });

        function showImageEnlargeModal(imageSrc) {
            const modal = document.createElement('div');
            modal.className = 'modal fade show';
            modal.style.cssText = 'display: block; background: rgba(0,0,0,0.85); z-index: 9999;';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-transparent border-0">
                        <div class="modal-body text-center p-3">
                            <img src="${imageSrc}" class="img-fluid rounded shadow-lg" style="max-height: 80vh; max-width: 100%;" alt="Preview">
                            <div class="mt-3">
                                <button type="button" class="btn btn-light btn-lg" onclick="this.closest('.modal').remove()">
                                    <i class="bi bi-x-circle"></i> Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            modal.onclick = function(e) {
                if (e.target === modal) modal.remove();
            };
            document.body.appendChild(modal);
        }

        // Auto scroll to error messages inside modal
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.hook('message.processed', (message, component) => {
                // Scroll to error alerts in modal if exists
                setTimeout(() => {
                    const errorAlert = document.querySelector('.modal-body .alert-danger');
                    if (errorAlert) {
                        errorAlert.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 100);
            });
        });
    </script>
</div>
