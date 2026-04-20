<div>
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h1 class="mb-4">Monitor Status Tiket Muat</h1>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Masukkan Nomor Tiket Muat (pendfNo)"
                        wire:model="pendfno" wire:keydown.enter="search">
                    <button class="btn btn-primary" type="button" wire:click="search">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <button class="btn btn-secondary" type="button" wire:click="clear">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Ticket Information -->
        @if ($ticketData)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Tiket: {{ $ticketData['ticket']->pendfNo }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Customer:</strong> {{ $ticketData['ticket']->custName ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Item:</strong> {{ $ticketData['ticket']->itemName ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>No Polisi:</strong> {{ $ticketData['ticket']->tmCarID ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Driver:</strong> {{ $ticketData['ticket']->tmDriver ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Jenis Truk:</strong> {{ $ticketData['ticket']->jenisTruk ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>No SPPB:</strong> {{ $ticketData['ticket']->sppbNo ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Qty Karung:</strong> {{ number_format($ticketData['ticket']->tmQtyKarung ?? 0) }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Qty Kg:</strong> {{ number_format($ticketData['ticket']->tmQtyKg ?? 0) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Grid -->
            <h4 class="mb-3">Status Tracking</h4>
            <div class="row g-3">
                <!-- 1. Security Check -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div
                        class="card h-100 {{ $ticketData['ticket']->isSecCek ? 'border-success' : 'border-secondary' }}">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                @if ($ticketData['ticket']->isSecCek)
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-secondary" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <h6 class="card-title">1. Security Check</h6>
                            @if ($ticketData['ticket']->isSecCekDate)
                                <small class="text-muted">
                                    {{ date('d-m-Y H:i', strtotime($ticketData['ticket']->isSecCekDate)) }}
                                </small>
                            @else
                                <small class="text-muted">Belum</small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. SPM Created -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="card h-100 {{ $ticketData['spm'] ? 'border-success' : 'border-secondary' }}">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                @if ($ticketData['spm'])
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-secondary" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <h6 class="card-title">2. SPM Dibuat</h6>
                            @if ($ticketData['spm'])
                                <small class="text-muted">
                                    {{ $ticketData['spm']->spmNo }}<br>
                                    {{ date('d-m-Y', strtotime($ticketData['spm']->tglSpm)) }}
                                </small>
                            @else
                                <small class="text-muted">Belum</small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 3. Jam In (Timbang Masuk) -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div
                        class="card h-100 {{ $ticketData['scale'] && $ticketData['scale']->jam_in ? 'border-success' : 'border-secondary' }}">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                @if ($ticketData['scale'] && $ticketData['scale']->jam_in)
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-secondary" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <h6 class="card-title">3. Jam In</h6>
                            @if ($ticketData['scale'] && $ticketData['scale']->jam_in)
                                <small class="text-muted">
                                    {{ date('d-m-Y H:i', strtotime($ticketData['scale']->jam_in)) }}<br>
                                    <strong>{{ number_format($ticketData['scale']->timbangin) }} kg</strong>
                                </small>
                            @else
                                <small class="text-muted">Belum</small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4. Loading Start -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div
                        class="card h-100 {{ $ticketData['scale'] && isset($ticketData['scale']->isLoadingDate) && $ticketData['scale']->isLoadingDate ? 'border-success' : 'border-secondary' }}">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                @if ($ticketData['scale'] && isset($ticketData['scale']->isLoadingDate) && $ticketData['scale']->isLoadingDate)
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-secondary" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <h6 class="card-title">4. Mulai Loading</h6>
                            @if ($ticketData['scale'] && isset($ticketData['scale']->isLoadingDate) && $ticketData['scale']->isLoadingDate)
                                <small class="text-muted">
                                    {{ date('d-m-Y H:i', strtotime($ticketData['scale']->isLoadingDate)) }}
                                </small>
                            @else
                                <small class="text-muted">Belum</small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 5. Loading Done -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div
                        class="card h-100 {{ $ticketData['scale'] && isset($ticketData['scale']->isLoadingDoneDate) && $ticketData['scale']->isLoadingDoneDate ? 'border-success' : 'border-secondary' }}">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                @if ($ticketData['scale'] && isset($ticketData['scale']->isLoadingDoneDate) && $ticketData['scale']->isLoadingDoneDate)
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-secondary" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <h6 class="card-title">5. Selesai Loading</h6>
                            @if ($ticketData['scale'] && isset($ticketData['scale']->isLoadingDoneDate) && $ticketData['scale']->isLoadingDoneDate)
                                <small class="text-muted">
                                    {{ date('d-m-Y H:i', strtotime($ticketData['scale']->isLoadingDoneDate)) }}
                                </small>
                            @else
                                <small class="text-muted">Belum</small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 6. Jam Out (Timbang Keluar) -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div
                        class="card h-100 {{ $ticketData['scale'] && $ticketData['scale']->jam_out ? 'border-success' : 'border-secondary' }}">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                @if ($ticketData['scale'] && $ticketData['scale']->jam_out)
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-secondary" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <h6 class="card-title">6. Jam Out</h6>
                            @if ($ticketData['scale'] && $ticketData['scale']->jam_out)
                                <small class="text-muted">
                                    {{ date('d-m-Y H:i', strtotime($ticketData['scale']->jam_out)) }}<br>
                                    <strong>{{ number_format($ticketData['scale']->timbangout ?? 0) }} kg</strong>
                                </small>
                            @else
                                <small class="text-muted">Belum</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            @if ($ticketData['scale'])
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Detail Timbangan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <strong>Berat Masuk:</strong> {{ number_format($ticketData['scale']->timbangin ?? 0) }}
                                kg
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Berat Keluar:</strong>
                                {{ number_format($ticketData['scale']->timbangout ?? 0) }} kg
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Netto:</strong> <span
                                    class="text-primary"><strong>{{ number_format($ticketData['scale']->netto ?? 0) }}
                                        kg</strong></span>
                            </div>
                            @if (isset($ticketData['scale']->avgkarung) && $ticketData['scale']->avgkarung)
                                <div class="col-md-4 mb-2">
                                    <strong>Avg Karung:</strong>
                                    {{ number_format($ticketData['scale']->avgkarung, 2) }} kg
                                </div>
                            @endif
                            @if (isset($ticketData['scale']->isLoadingDate) &&
                                    $ticketData['scale']->isLoadingDate &&
                                    isset($ticketData['scale']->jam_out) &&
                                    $ticketData['scale']->jam_out)
                                @php
                                    $loadingStart = \Carbon\Carbon::parse($ticketData['scale']->isLoadingDate);
                                    $loadingEnd = \Carbon\Carbon::parse($ticketData['scale']->jam_out);
                                    $duration = $loadingStart->diff($loadingEnd);

                                    $hours = $duration->h + $duration->days * 24;
                                    $minutes = $duration->i;
                                @endphp
                                <div class="col-md-4 mb-2">
                                    <div class="bg-primary text-white p-2 rounded text-center">
                                        <strong>Lama Waktu Loading:</strong><br>
                                        <span style="font-size: 1.1rem;">
                                            <strong>{{ $hours }} jam {{ $minutes }} menit</strong>
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @if ($notFound)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Tidak ada data untuk ditampilkan. Silakan cari tiket muat
                terlebih dahulu.
            </div>
        @endif
    </div>

    <style>
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card.border-success {
            border-width: 2px !important;
        }
    </style>
</div>
