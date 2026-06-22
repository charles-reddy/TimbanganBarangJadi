<div>
    <!-- START antrian hari ini-->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Antrian Hari ini</h1>
            <button class="btn btn-success" wire:click="export">
                <i class="bi bi-file-earmark-excel"></i> Export to Excel
            </button>
        </div>

        <!-- Filter Section -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <label for="tglmuat" class="form-label small text-muted">Tgl Muat</label>
                <input type="date" id="tglmuat" class="form-control" wire:model.live="tglmuat">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <label for="platno" class="form-label small text-muted">Plat No</label>
                <input type="text" id="platno" class="form-control" placeholder="Search plat no..."
                    wire:model.live="katakunci">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <label for="customer" class="form-label small text-muted">Customer</label>
                <input type="text" id="customer" class="form-control" placeholder="Search customer..."
                    wire:model.live="katacust">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <label for="sppb" class="form-label small text-muted">SPPB</label>
                <input type="text" id="sppb" class="form-control" placeholder="Search SPPB..."
                    wire:model.live="katasppb">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <label for="product" class="form-label small text-muted">Product</label>
                <select id="product" class="form-select" multiple wire:model.live="kataproduct" size="1">
                    <option value="">-- Select Products --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->itemCode }}">{{ $product->itemName }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <label for="shift" class="form-label small text-muted">Shift</label>
                <select id="shift" class="form-control" wire:model.live="shift">
                    <option value="">Semua Shift</option>
                    <option value="Shift 1">Shift 1 (08:00-12:00)</option>
                    <option value="Shift 2">Shift 2 (12:00-16:00)</option>
                    <option value="Shift 3">Shift 3 (16:00-20:00)</option>
                    <option value="Outside">Outside Shift</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-responsive">
        {{ $antriantdy->links() }}
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Tiket Muat</th>
                    <th>SPPB</th>
                    <th>Tgl Muat</th>
                    <th>Shift</th>
                    <th>Driver</th>
                    <th>Car ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Truck Type</th>
                    <th class="text-end">Weight (Kg)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalkg = 0;
                @endphp
                @foreach ($antriantdy as $key => $value)
                    <tr>
                        <td class="text-center">{{ $antriantdy->firstItem() + $key }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->sppbNo }}</td>
                        <td>{{ $value->tglMuat }}</td>
                        <td>
                            @if ($value->shift == 'Shift 1')
                                <span class="badge bg-primary">{{ $value->shift }}</span>
                            @elseif($value->shift == 'Shift 2')
                                <span class="badge bg-success">{{ $value->shift }}</span>
                            @elseif($value->shift == 'Shift 3')
                                <span class="badge bg-warning text-dark">{{ $value->shift }}</span>
                            @else
                                <span class="badge bg-danger">{{ $value->shift }}</span>
                            @endif
                        </td>
                        <td>{{ $value->tmDriver }}</td>
                        <td>{{ $value->tmCarID }}</td>
                        <td>{{ $value->custName }}</td>
                        <td>{{ $value->itemName }}</td>
                        <td>{{ $value->jenisTruk }}</td>
                        <td class="text-end">{{ number_format($value->tmQtyKg) }}</td>
                        @php
                            $totalkg = $totalkg + $value->tmQtyKg;
                        @endphp
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="10" class="text-end fw-bold">Total Weight:</td>
                    <td class="text-end fw-bold">{{ number_format($totalkg) }} Kg</td>
                </tr>
            </tfoot>
        </table>
        {{ $antriantdy->links() }}
    </div>
</div>
<!-- AKHIR antrian hari ini -->
</div>
