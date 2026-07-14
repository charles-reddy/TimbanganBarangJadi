<div>
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        {{-- Success/Error/Warning Messages (Only show when modal is closed) --}}
        @if (!$showModal)
            @if (session()->has('success'))
                <div class="pt-3">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> {!! session('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if (session()->has('warning'))
                <div class="pt-3">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Perhatian!</strong> {!! session('warning') !!}
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

        <!-- Header Card -->
        <div class="card bg-warning text-dark mb-4">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">
                    <i class="bi bi-arrow-repeat me-2"></i>Koreksi B10 - Transaksi Out of Range
                </h3>
                <p class="mb-0 mt-2" style="font-size: 0.9rem;">Koreksi qty karung B10 jika average out of range</p>
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
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Daftar Transaksi Out of Range
                </h5>
            </div>
            <div class="card-body p-0">
                @forelse ($headers as $header)
                    <div class="border-bottom p-3 hover-bg-light">
                        <div class="row">
                            <div class="col-md-9">
                                <h6 class="mb-2">
                                    <span class="badge bg-info">{{ $header->trans_no }}</span>
                                    <span class="ms-2"><strong>{{ $header->carID }}</strong></span>
                                </h6>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> Driver:
                                            <strong>{{ $header->driver }}</strong><br>
                                            <i class="bi bi-building"></i> Customer:
                                            {{ $header->custName ?? $header->custID }}<br>
                                            <i class="bi bi-calendar-check"></i> Timbang Keluar:
                                            {{ $header->weigh_out_time ? $header->weigh_out_time->format('d/m/Y H:i') : '-' }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="bi bi-speedometer"></i> Gross:
                                            {{ number_format($header->gross_weight, 2) }} kg<br>
                                            <i class="bi bi-truck"></i> Tare:
                                            {{ number_format($header->tare_weight, 2) }} kg<br>
                                            <i class="bi bi-check2-square"></i> Net:
                                            <strong>{{ number_format($header->net_weight, 2) }} kg</strong><br>
                                            <i class="bi bi-calculator"></i> Correction Factor:
                                            {{ number_format($header->correction_factor, 4) }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Range Info -->
                                <div class="alert alert-danger mb-2 py-2">
                                    <strong><i class="bi bi-x-circle"></i> OUT OF RANGE</strong><br>
                                    <small>
                                        Range: {{ number_format($header->total_range_min, 2) }} -
                                        {{ number_format($header->total_range_max, 2) }} kg |
                                        Net: {{ number_format($header->net_weight, 2) }} kg
                                        @if ($header->net_weight < $header->total_range_min)
                                            <span class="badge bg-danger">Terlalu Rendah</span>
                                        @elseif ($header->net_weight > $header->total_range_max)
                                            <span class="badge bg-danger">Terlalu Tinggi</span>
                                        @endif
                                    </small>
                                </div>

                                <!-- Product List -->
                                <div class="mt-2">
                                    <strong>Products:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach ($header->details as $detail)
                                            <li>
                                                <small>
                                                    <strong>{{ $detail->itemName }}</strong>:
                                                    Avg {{ number_format($detail->avg_per_karung, 2) }} kg/karung
                                                    (Range: {{ $detail->gross_min }}-{{ $detail->gross_max }})
                                                    @if ($detail->avg_per_karung < $detail->gross_min || $detail->avg_per_karung > $detail->gross_max)
                                                        <span class="badge bg-danger">Out of Range</span>
                                                    @else
                                                        <span class="badge bg-success">OK</span>
                                                    @endif
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="button" class="btn btn-warning mb-2 w-100"
                                    wire:click="openKoreksiModal({{ $header->id }})">
                                    <i class="bi bi-pencil-square"></i> Koreksi B10
                                </button>
                                {{-- <button type="button" class="btn btn-outline-primary w-100"
                                    onclick="if(confirm('Submit untuk approval tanpa koreksi?')) @this.call('submitForApproval')">
                                    <i class="bi bi-send"></i> Submit Approval
                                </button> --}}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                        <p class="text-muted mt-2">Tidak ada transaksi yang perlu koreksi B10</p>
                    </div>
                @endforelse
            </div>
            <div class="card-footer">
                {{ $headers->links() }}
            </div>
        </div>

        {{-- Koreksi Modal --}}
        @if ($showModal && $selectedHeader)
            <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">
                                <i class="bi bi-arrow-repeat"></i> Koreksi B10 - Trans No:
                                {{ $selectedHeader->trans_no }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModal"></button>
                        </div>
                        <div class="modal-body">
                            {{-- Error/Success Messages Inside Modal --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="bi bi-exclamation-triangle-fill"></i> Terdapat
                                        kesalahan:</strong>
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

                            @if (session()->has('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong><i class="bi bi-exclamation-circle-fill"></i> Perhatian!</strong>
                                    {{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Info Transaksi -->
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong><i class="bi bi-info-circle"></i> Info Transaksi:</strong><br>
                                        Driver: <strong>{{ $selectedHeader->driver }}</strong> |
                                        Car ID: <strong>{{ $selectedHeader->carID }}</strong><br>
                                        Net Weight: <strong>{{ number_format($selectedHeader->net_weight, 2) }}
                                            kg</strong>
                                        |
                                        Correction Factor: {{ number_format($selectedHeader->correction_factor, 4) }}
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <strong class="text-danger">Current Status: OUT OF RANGE</strong><br>
                                        Range: {{ number_format($selectedHeader->total_range_min, 2) }} -
                                        {{ number_format($selectedHeader->total_range_max, 2) }} kg
                                    </div>
                                </div>
                            </div>

                            <form wire:submit.prevent="saveKoreksi">
                                <!-- Koreksi Table -->
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 20%;">Product</th>
                                                <th style="width: 10%;" class="text-center">B10 Qty<br>Sekarang</th>
                                                <th style="width: 10%;" class="text-center">Avg Now<br>(kg/karung)
                                                </th>
                                                <th style="width: 10%;" class="text-center">Range<br>(kg/karung)</th>
                                                <th style="width: 10%;" class="text-center">Status</th>
                                                <th style="width: 12%;">B10 Qty<br>Baru</th>
                                                <th style="width: 10%;" class="text-center">Avg Baru<br>(preview)</th>
                                                <th style="width: 8%;">Bukti Foto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selectedHeader->details as $index => $detail)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>
                                                        <small class="text-muted">{{ $detail->itemCode }}</small><br>
                                                        <strong>{{ $detail->itemName }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge bg-secondary">{{ $detail->b10QtyKarung }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ number_format($detail->avg_per_karung, 2) }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <small>{{ $detail->gross_min }} -
                                                            {{ $detail->gross_max }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($detail->avg_per_karung < $detail->gross_min || $detail->avg_per_karung > $detail->gross_max)
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-x-circle"></i> Out
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle"></i> OK
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            wire:model.live="corrections.{{ $detail->id }}"
                                                            min="1">
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($previewCalculation[$detail->id]))
                                                            <strong
                                                                class="{{ $previewCalculation[$detail->id]['inRange'] ? 'text-success' : 'text-danger' }}">
                                                                {{ $previewCalculation[$detail->id]['newAvg'] }}
                                                            </strong>
                                                            @if ($previewCalculation[$detail->id]['inRange'])
                                                                <br><small class="text-success">✓ In Range</small>
                                                            @else
                                                                <br><small class="text-danger">✗ Out</small>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{-- Foto Bukti 1 --}}
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-1">Foto Bukti 1 <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="file"
                                                                class="form-control form-control-sm @error('buktiFiles.' . $detail->id . '.0') is-invalid @enderror"
                                                                wire:model="buktiFiles.{{ $detail->id }}.0"
                                                                accept="image/*">
                                                            @error('buktiFiles.' . $detail->id . '.0')
                                                                <div class="text-danger small">{{ $message }}</div>
                                                            @enderror

                                                            {{-- Preview Foto 1 --}}
                                                            @if (isset($buktiFiles[$detail->id][0]))
                                                                <div class="mt-1" wire:loading.remove
                                                                    wire:target="buktiFiles.{{ $detail->id }}.0">
                                                                    @php
                                                                        try {
                                                                            $tempUrl = $buktiFiles[
                                                                                $detail->id
                                                                            ][0]->temporaryUrl();
                                                                        } catch (\Exception $e) {
                                                                            $tempUrl = null;
                                                                        }
                                                                    @endphp
                                                                    @if ($tempUrl)
                                                                        <img src="{{ $tempUrl }}"
                                                                            class="img-thumbnail bukti-preview-enlarge"
                                                                            style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                                            data-image-url="{{ $tempUrl }}"
                                                                            data-image-label="Bukti Foto 1"
                                                                            title="Klik untuk memperbesar">
                                                                        <small class="text-success d-block"><i
                                                                                class="bi bi-check-circle"></i>
                                                                            Terupload</small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            <div wire:loading
                                                                wire:target="buktiFiles.{{ $detail->id }}.0">
                                                                <small class="text-primary"><i
                                                                        class="bi bi-hourglass-split"></i>
                                                                    Uploading...</small>
                                                            </div>
                                                        </div>

                                                        {{-- Foto Bukti 2 --}}
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-1">Foto Bukti 2
                                                                (Opsional)</label>
                                                            <input type="file"
                                                                class="form-control form-control-sm @error('buktiFiles.' . $detail->id . '.1') is-invalid @enderror"
                                                                wire:model="buktiFiles.{{ $detail->id }}.1"
                                                                accept="image/*">
                                                            @error('buktiFiles.' . $detail->id . '.1')
                                                                <div class="text-danger small">{{ $message }}</div>
                                                            @enderror

                                                            {{-- Preview Foto 2 --}}
                                                            @if (isset($buktiFiles[$detail->id][1]))
                                                                <div class="mt-1" wire:loading.remove
                                                                    wire:target="buktiFiles.{{ $detail->id }}.1">
                                                                    @php
                                                                        try {
                                                                            $tempUrl = $buktiFiles[
                                                                                $detail->id
                                                                            ][1]->temporaryUrl();
                                                                        } catch (\Exception $e) {
                                                                            $tempUrl = null;
                                                                        }
                                                                    @endphp
                                                                    @if ($tempUrl)
                                                                        <img src="{{ $tempUrl }}"
                                                                            class="img-thumbnail bukti-preview-enlarge"
                                                                            style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                                            data-image-url="{{ $tempUrl }}"
                                                                            data-image-label="Bukti Foto 2"
                                                                            title="Klik untuk memperbesar">
                                                                        <small class="text-success d-block"><i
                                                                                class="bi bi-check-circle"></i>
                                                                            Terupload</small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            <div wire:loading
                                                                wire:target="buktiFiles.{{ $detail->id }}.1">
                                                                <small class="text-primary"><i
                                                                        class="bi bi-hourglass-split"></i>
                                                                    Uploading...</small>
                                                            </div>
                                                        </div>

                                                        {{-- Foto Bukti 3 --}}
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-1">Foto Bukti 3
                                                                (Opsional)</label>
                                                            <input type="file"
                                                                class="form-control form-control-sm @error('buktiFiles.' . $detail->id . '.2') is-invalid @enderror"
                                                                wire:model="buktiFiles.{{ $detail->id }}.2"
                                                                accept="image/*">
                                                            @error('buktiFiles.' . $detail->id . '.2')
                                                                <div class="text-danger small">{{ $message }}</div>
                                                            @enderror

                                                            {{-- Preview Foto 3 --}}
                                                            @if (isset($buktiFiles[$detail->id][2]))
                                                                <div class="mt-1" wire:loading.remove
                                                                    wire:target="buktiFiles.{{ $detail->id }}.2">
                                                                    @php
                                                                        try {
                                                                            $tempUrl = $buktiFiles[
                                                                                $detail->id
                                                                            ][2]->temporaryUrl();
                                                                        } catch (\Exception $e) {
                                                                            $tempUrl = null;
                                                                        }
                                                                    @endphp
                                                                    @if ($tempUrl)
                                                                        <img src="{{ $tempUrl }}"
                                                                            class="img-thumbnail bukti-preview-enlarge"
                                                                            style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                                            data-image-url="{{ $tempUrl }}"
                                                                            data-image-label="Bukti Foto 3"
                                                                            title="Klik untuk memperbesar">
                                                                        <small class="text-success d-block"><i
                                                                                class="bi bi-check-circle"></i>
                                                                            Terupload</small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            <div wire:loading
                                                                wire:target="buktiFiles.{{ $detail->id }}.2">
                                                                <small class="text-primary"><i
                                                                        class="bi bi-hourglass-split"></i>
                                                                    Uploading...</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Live Preview -->
                                @if (isset($previewCalculation['total']))
                                    <div
                                        class="alert {{ $previewCalculation['total']['inRange'] ? 'alert-success' : 'alert-warning' }}">
                                        <strong><i class="bi bi-calculator"></i> Preview Perhitungan Baru:</strong><br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                • Net Weight:
                                                <strong>{{ number_format($previewCalculation['total']['netWeight'], 2) }}
                                                    kg</strong><br>
                                                • Total Range Min:
                                                {{ number_format($previewCalculation['total']['totalRangeMin'], 2) }}
                                                kg<br>
                                                • Total Range Max:
                                                {{ number_format($previewCalculation['total']['totalRangeMax'], 2) }}
                                                kg
                                            </div>
                                            <div class="col-md-6 text-end">
                                                @if ($previewCalculation['total']['inRange'])
                                                    <h5 class="text-success mb-0">
                                                        <i class="bi bi-check-circle"></i> DALAM RANGE!
                                                    </h5>
                                                    <small>Setelah koreksi, transaksi akan otomatis COMPLETED</small>
                                                @else
                                                    <h5 class="text-warning mb-0">
                                                        <i class="bi bi-exclamation-triangle"></i> MASIH OUT OF RANGE
                                                    </h5>
                                                    <small>Anda bisa koreksi lagi atau submit untuk approval</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Alasan Koreksi -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        Alasan Koreksi <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('correctionReason') is-invalid @enderror" wire:model="correctionReason"
                                        rows="3" placeholder="Jelaskan alasan koreksi qty karung (minimal 10 karakter)"></textarea>
                                    @error('correctionReason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-warning">
                                    <strong><i class="bi bi-exclamation-triangle"></i> Perhatian:</strong><br>
                                    • Ubah <strong>B10 Qty Baru</strong> untuk product yang perlu dikoreksi<br>
                                    • Preview akan menampilkan perhitungan average yang baru<br>
                                    • <strong>Foto Bukti 1 WAJIB</strong> untuk setiap product yang dikoreksi<br>
                                    • Foto Bukti 2 dan 3 bersifat opsional (bisa diupload jika perlu)<br>
                                    • Jika setelah koreksi masih out of range, Anda bisa koreksi lagi atau submit untuk
                                    approval<br>
                                    • Semua history koreksi akan tersimpan untuk audit
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                            <button type="button" class="btn btn-warning" wire:click="saveKoreksi">
                                <i class="bi bi-save"></i> Simpan Koreksi & Recalculate
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
            // Global function untuk enlarge bukti image preview
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('bukti-preview-enlarge')) {
                    const imageSrc = e.target.getAttribute('data-image-url');
                    const imageLabel = e.target.getAttribute('data-image-label') || 'Preview';
                    if (imageSrc) {
                        showBuktiEnlargeModal(imageSrc, imageLabel);
                    }
                }
            });

            function showBuktiEnlargeModal(imageSrc, imageLabel) {
                const modal = document.createElement('div');
                modal.className = 'modal fade show';
                modal.style.cssText = 'display: block; background: rgba(0,0,0,0.85); z-index: 9999;';
                modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-transparent border-0">
                        <div class="modal-body text-center p-3">
                            <div class="mb-3">
                                <span class="badge bg-success fs-6">${imageLabel}</span>
                            </div>
                            <img src="${imageSrc}" class="img-fluid rounded shadow-lg" style="max-height: 75vh; max-width: 100%;" alt="${imageLabel}">
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
                        const errorAlert = document.querySelector(
                            '.modal-body .alert-danger, .modal-body .alert-warning');
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
</div>
