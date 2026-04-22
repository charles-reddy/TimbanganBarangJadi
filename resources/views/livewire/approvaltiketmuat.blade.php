<div>
    @if (session('error'))
        <div class="pt-3">
            <div class="alert alert-danger">
                <span class="sr-only">WARNING</span>
                <div>
                    <span class="font-medium">Danger alert!</span> {{ session('error') }}
                </div>
            </div>
        </div>
    @endif


    @if ($errors->any())
        <div class="pt-3">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

    @endif


    @if (session()->has('message'))
        <div class="pt-3">
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        </div>
    @endif

    <!-- START FORM -->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="mb-2 text-muted small">
            {{ now() }}
        </div>

        <!-- Header Card -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">Approval Tiket Muat</h3>
            </div>
        </div>

        <form>
            <input type="text" wire:model="transID" hidden>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">SPPB</label>
                        <input type="text" class="form-control" wire:model="sppbNo" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer</label>
                        <input type="text" class="form-control" wire:model="custName" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tgl Muat</label>
                        <input type="text" class="form-control" wire:model="tglMuat" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Barang</label>
                        <input type="text" class="form-control" wire:model="itemName" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Berat (Kg)</label>
                        <input type="text" class="form-control" wire:model="qtyKg" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sak / Karung</label>
                        <input type="text" class="form-control" wire:model="qtyKarung" disabled>
                    </div>
                </div>
            </div>


            <!-- Foto SIM/KTP dan STNK -->
            <div class="row g-3 mb-4">
                <div class="col-lg-6">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white text-center fw-bold">
                            Foto SIM / KTP
                        </div>
                        <div class="card-body p-2">
                            <img class="img-fluid w-100 rounded" src="{{ $simKtp }}" alt="SIM/KTP">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white text-center fw-bold">
                            Foto STNK
                        </div>
                        <div class="card-body p-2">
                            <img class="img-fluid w-100 rounded" src="{{ $stnk }}" alt="STNK">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 justify-content-center justify-content-md-start">
                <button type="button" class="btn btn-primary px-4" wire:click="store()">
                    <i class="bi bi-check-circle me-1"></i>APPROVE
                </button>
                <button type="button" class="btn btn-secondary px-4" wire:click="clear()">
                    <i class="bi bi-x-circle me-1"></i>CLEAR
                </button>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->

    <!-- START DATA tiket muat-->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h3 class="mb-4 fw-bold">Data Tiket Muat</h3>

        <!-- Search Box -->
        <div class="row mb-3">
            <div class="col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Cari SPPB..." wire:model.live="katakunci">
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mb-3">
            {{ $datatiketmuat->links() }}
        </div>

        <!-- Responsive Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Tiket Muat</th>
                        <th>SPPB</th>
                        <th>Tgl Muat</th>
                        <th>Barang</th>
                        <th class="text-end">Berat (Kg)</th>
                        <th class="text-end">Sak/Karung</th>
                        <th>Customer</th>
                        <th>Transporter</th>
                        <th>Plat No</th>
                        <th>Sopir</th>
                        <th>No HP</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datatiketmuat as $key => $value)
                        <tr>
                            <td class="text-center">{{ $datatiketmuat->firstItem() + $key }}</td>
                            <td><span class="badge bg-info">{{ $value->pendfNo }}</span></td>
                            <td>{{ $value->sppbNo }}</td>
                            <td>{{ date('d-m-Y', strtotime($value->tglMuat)) }}</td>
                            <td>{{ $value->itemName }}</td>
                            <td class="text-end">{{ number_format($value->tmQtyKg, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($value->tmQtyKarung, 0, ',', '.') }}</td>
                            <td>{{ $value->custName }}</td>
                            <td>{{ $value->tmTranspName }}</td>
                            <td><span class="badge bg-secondary">{{ $value->tmCarID }}</span></td>
                            <td>{{ $value->tmDriver }}</td>
                            <td>{{ $value->noHPDriver }}</td>
                            <td class="text-center">
                                <button wire:click="edit({{ $value->id }})" class="btn btn-primary btn-sm">
                                    <i class="bi bi-check-circle me-1"></i>Approval
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Bottom -->
        <div class="mt-3">
            {{ $datatiketmuat->links() }}
        </div>
    </div>
    <!-- AKHIR DATA Tiket Muat -->

</div>
