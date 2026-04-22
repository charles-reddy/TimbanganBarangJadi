<div>

    {{-- START OF ERROR MESSAGE --}}
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
    {{-- END OF ERROR MESSAGE --}}

    <!-- START FORM -->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        {{-- @include('layouts.navbar') --}}
        <div class="mb-2 text-muted small">
            {{ now() }}
        </div>

        <!-- Header Card -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">Surat Perintah Muat</h3>
            </div>
        </div>
        <form>
            <!-- Row 1: SPM No & No Tiket Muat -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="spmNo" class="form-label fw-bold">SPM No</label>
                    @if ($isDisabled == true)
                        <input type="text" class="form-control" wire:model="spmNo1" disabled>
                    @else
                        <input type="text" class="form-control" wire:model="spmNo" disabled>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="tiketMuat" class="form-label fw-bold">No Tiket Muat</label>
                    @if ($isDisabled == true)
                        <input type="text" class="form-control" wire:model="tiketMuat1" disabled>
                    @else
                        <select class="js-example-basic-single form-control" id="my-tiketMuat" wire:model="tiketMuat">
                            <option></option>
                            @foreach ($datatm as $item)
                                <option value="{{ $item->id }}">{{ $item->pendfNo }} - {{ $item->tmTranspName }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            <!-- Row 2: SPPB No & Plat No -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="sppbNo" class="form-label fw-bold">SPPB No</label>
                    <input type="text" class="form-control" wire:model="sppbID" hidden>
                    <input type="text" class="form-control" wire:model="sppbNo" disabled>
                </div>
                <div class="col-md-6">
                    <label for="carID" class="form-label fw-bold">Plat No</label>
                    <input type="text" class="form-control" wire:model="carID" autocomplete="off" disabled>
                </div>
            </div>

            <!-- Row 3: Driver & Customer -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="driver" class="form-label fw-bold">Driver</label>
                    <input type="text" class="form-control" wire:model="driver" disabled>
                </div>
                <div class="col-md-6">
                    <label for="custName" class="form-label fw-bold">Customer</label>
                    <input type="text" class="form-control" wire:model="custID" hidden>
                    <input type="text" class="form-control" wire:model="custName" disabled>
                </div>
            </div>

            <!-- Row 4: DN No & Seal No -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="dnNo" class="form-label fw-bold">DN No</label>
                    <input type="text" class="form-control" wire:model="dnNo" autocomplete="off">
                </div>
                <div class="col-md-6">
                    <label for="sealNo" class="form-label fw-bold">Seal No</label>
                    <input type="text" class="form-control" wire:model="sealNo" autocomplete="off">
                </div>
            </div>

            <!-- Row 5: Container No & Item Code -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="kontainerNo" class="form-label fw-bold">Container No</label>
                    <input type="text" class="form-control" wire:model="kontainerNo" autocomplete="off">
                </div>
                <div class="col-md-6">
                    <label for="itemCode" class="form-label fw-bold">Item Code</label>
                    <input type="text" class="form-control" wire:model="itemCode" hidden>
                    <input type="text" class="form-control mb-2" wire:model="itemName" disabled>
                    <input type="text" class="form-control" wire:model="itemType" disabled>
                </div>
            </div>

            <!-- Row 6: Kemasan & Local/Export -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="packingID" class="form-label fw-bold">Kemasan</label>
                    @if ($isDisabled == true)
                        <input type="text" class="form-control" wire:model="packingID" disabled>
                    @else
                        <select class="js-example-basic-single form-control" id="my-packingID"
                            wire:model="packingID">
                            <option></option>
                            @foreach ($kemasan as $item)
                                <option value="{{ $item->packingID }}">{{ $item->packingName }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="isExport" class="form-label fw-bold">Local / Export</label>
                    <select class="js-example-basic-single form-control" id="my-isExport" wire:model="isExport">
                        <option>---Local / Export---</option>
                        <option value="1">Local</option>
                        <option value="2">Export</option>
                    </select>
                </div>
            </div>

            <!-- Row 7: Ekses SPPB Molases & Qty (Kg) -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="eksesMol" class="form-label fw-bold">Ekses SPPB Molases</label>
                    @if ($itemType == 'FG')
                        <select class="js-example-basic-single form-control" id="my-eksesMol" wire:model="eksesMol"
                            disabled>
                            <option value="0">---Ekses Molases---</option>
                            <option value="1">yes</option>
                        </select>
                    @else
                        <select class="js-example-basic-single form-control" id="my-eksesMol" wire:model="eksesMol">
                            <option value="0">---Ekses Molases---</option>
                            <option value="1">Yes</option>
                        </select>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="qtyKg" class="form-label fw-bold">Qty (Kg)</label>
                    @if ($itemType == 'FG-L')
                        <input type="text" class="form-control" wire:model="qtyKg" autocomplete="off">
                    @else
                        <input type="text" class="form-control" wire:model="qtyKg" autocomplete="off" disabled>
                    @endif
                </div>
            </div>

            <!-- Row 8: Qty (Karung) & Jenis Truk -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="qtyKarung" class="form-label fw-bold">Qty (Karung)</label>
                    @if ($itemType == 'FG-L')
                        <input type="text" id="input" class="form-control" wire:model="qtyKarung"
                            autocomplete="off">
                    @else
                        <input type="text" id="input" class="form-control" wire:model="qtyKarung"
                            autocomplete="off" disabled>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="tmJenisTruk" class="form-label fw-bold">Jenis Truk</label>
                    <select class="js-example-basic-single form-control" id="tmJenisTruk" wire:model="tmJenisTruk">
                        <option></option>
                        @foreach ($jenistruk as $item)
                            <option value="{{ $item->id }}" selected>{{ $item->jenisTruk }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Hidden Fields -->
            <input type="text" class="form-control" wire:model="transID" hidden>
            <input type="text" class="form-control" wire:model="openQtyKg" autocomplete="off" disabled hidden>
            <input type="text" id="input" class="form-control" wire:model="openQtyKarung" autocomplete="off"
                disabled hidden>
            <input type="text" class="form-control" wire:model="AwalQtyKg" autocomplete="off" disabled hidden>
            <input type="text" class="form-control" wire:model="AwalOpenQtyKg" autocomplete="off" disabled
                hidden>
            <input type="text" id="input" class="form-control" wire:model="AwalQtyKarung" autocomplete="off"
                disabled hidden>
            <input type="text" id="input" class="form-control" wire:model="AwalOpenQtyKarung"
                autocomplete="off" disabled hidden>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mt-4 mb-4">
                @if ($updateData == false)
                    <button type="button" class="btn btn-primary px-4" name="submit" wire:click="store()">
                        <i class="bi bi-save me-1"></i>SIMPAN
                    </button>
                @else
                    <button type="button" class="btn btn-primary px-4" name="submit" wire:click="update()">
                        <i class="bi bi-arrow-repeat me-1"></i>UPDATE
                    </button>
                @endif
                <button type="button" class="btn btn-secondary px-4" name="submit" wire:click="clear()">
                    <i class="bi bi-x-circle me-1"></i>CLEAR
                </button>
            </div>

            <!-- START DATA SPM -->
            <div class="my-3 p-3 bg-body rounded shadow-sm">
                <h3 class="mb-4 fw-bold">Data SPM</h3>

                <!-- Search Box -->
                <div class="row mb-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Cari Car ID / SPPB No..."
                                wire:model.live="katakunci">
                        </div>
                    </div>
                </div>

                <!-- Pagination Top -->
                <div class="mb-3">
                    {{ $dataspm->links() }}
                </div>

                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th class="sort @if ($sortColumn == 'spmNo') {{ $sortDirection }} @endif"
                                    wire:click="sort('spmNo')">No SPM</th>
                                <th>Tiket Muat</th>
                                <th class="sort @if ($sortColumn == 'isExport') {{ $sortDirection }} @endif"
                                    wire:click="sort('isExport')">Local/Export</th>
                                <th class="sort @if ($sortColumn == 'tglSpm') {{ $sortDirection }} @endif"
                                    wire:click="sort('tglSpm')">Tgl SPM</th>
                                <th class="sort @if ($sortColumn == 'carID') {{ $sortDirection }} @endif"
                                    wire:click="sort('carID')">Plat No</th>
                                <th class="sort @if ($sortColumn == 'sppbNo') {{ $sortDirection }} @endif"
                                    wire:click="sort('sppbNo')">No SPPB</th>
                                <th class="sort @if ($sortColumn == 'itemName') {{ $sortDirection }} @endif"
                                    wire:click="sort('itemName')">Nama Barang</th>
                                <th class="text-end sort @if ($sortColumn == 'qtyKg') {{ $sortDirection }} @endif"
                                    wire:click="sort('qtyKg')">Qty Kg</th>
                                <th class="text-end sort @if ($sortColumn == 'qtyKarung') {{ $sortDirection }} @endif"
                                    wire:click="sort('qtyKarung')">Qty Karung</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataspm as $key => $value)
                                <tr>
                                    <td class="text-center">{{ $dataspm->firstItem() + $key }}</td>
                                    <td><span class="badge bg-info">{{ $value->spmNo }}</span></td>
                                    <td>{{ $value->pendfNo }}</td>
                                    <td>
                                        @php
                                            echo $value->isExport == 1 ? 'Local' : 'Export';
                                        @endphp
                                    </td>
                                    <td>{{ date('d-m-Y H:i', strtotime($value->tglSpm)) }}</td>
                                    <td><span class="badge bg-secondary">{{ $value->carID }}</span></td>
                                    <td>{{ $value->sppbNo }}</td>
                                    <td>{{ $value->itemName }}</td>
                                    <td class="text-end">{{ number_format($value->qtyKg, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($value->qtyKarung, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <a href="/cetakspm/{{ $value->id }}" class="btn btn-primary btn-sm"
                                            target="_blank">
                                            <i class="bi bi-printer me-1"></i>Cetak
                                        </a>
                                        @if (auth()->user()->hasrole('administrator') ||
                                                auth()->user()->hasrole('supervisor-timbangan-registrasi') ||
                                                auth()->user()->hasrole('manager-logistik'))
                                            <a wire:click="edit({{ $value->id }})" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bottom -->
                <div class="mt-3">
                    {{ $dataspm->links() }}
                </div>
                <script>
                    $(document).ready(function() {
                        $('.js-example-basic-single').select2();

                        $('#my-custID').on('change', function(e) {
                            var data = $('#my-custID').select2("val");
                            @this.set('custID', data);
                        })

                        $('#my-tiketMuat').on('change', function(e) {
                            var data = $('#my-tiketMuat').select2("val");
                            @this.set('tiketMuat', data);
                        })

                        $('#my-tiketID').on('change', function(e) {
                            var data = $('#my-tiketID').select2("val");
                            @this.set('tiketID', data);
                        })


                        $('#my-transpID').on('change', function(e) {
                            var data = $('#my-transpID').select2("val");
                            @this.set('transpID', data);
                        })

                        $('#my-packingID').on('change', function(e) {
                            var data = $('#my-packingID').select2("val");
                            @this.set('packingID', data);
                        })

                        $('#my-itemCode').on('change', function(e) {
                            var data = $('#my-itemCode').select2("val");
                            @this.set('itemCode', data);
                        })

                        $('#my-isExport').on('change', function(e) {
                            var data = $('#my-isExport').select2("val");
                            @this.set('isExport', data);
                        })


                        $('#my-eksesMol').on('change', function(e) {
                            var data = $('#my-eksesMol').select2("val");
                            @this.set('eksesMol', data);
                        })


                    });
                </script>
            </div>
            <!-- AKHIR DATA SPM-->



        </form>
    </div>
    <!-- AKHIR FORM -->

    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            $('#my-itemCode').on('change', function(e) {
                var data = $('#my-itemCode').select2("val");
                @this.set('itemCode', data);
            })
            $('#my-transpID').on('change', function(e) {
                var data = $('#my-transpID').select2("val");
                @this.set('transpID', data);
            })



        });
    </script>


</div>
