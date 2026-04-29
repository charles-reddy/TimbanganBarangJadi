<div>
    <!-- START antrian hari ini-->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h1> Antrian Hari ini</h1>
        <div class="row">
            <div class="col-sm-3">
                <label for=""></label>
                <input type="text" class="form-control mb-3 w-75" placeholder="Searching ... Plat no"
                    wire:model.live="katakunci">
            </div>
            <div class="col-sm-3">
                <input type="text" class="form-control mb-3 w-100" placeholder="Search base customer ..."
                    wire:model.live="katacust">
            </div>
            <div class="col-sm-3">
                <input type="text" class="form-control mb-3 w-100" placeholder="Search base SPPB ..."
                    wire:model.live="katasppb">
            </div>
            <div class="col-sm-3">
                <select class="form-control mb-3 w-75" wire:model.live="shift">
                    <option value="">Semua Shift</option>
                    <option value="Shift 1">Shift 1 (08:00-12:00)</option>
                    <option value="Shift 2">Shift 2 (12:00-16:00)</option>
                    <option value="Shift 3">Shift 3 (16:00-20:00)</option>
                    <option value="Outside">Outside Shift</option>
                </select>
            </div>

        </div>
        <div class="card-body table-responsive p-0">
            {{ $antriantdy->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md">Tiket Muat</th>
                        <th class="col-md">SPPB</th>
                        <th class="col-md">Tgl Muat</th>
                        <th class="col-md">Shift Tiket Muat</th>
                        <th class="col-md">Driver</th>
                        <th class="col-md">Car ID</th>
                        <th class="col-md">Customer</th>
                        <th class="col-md">Item </th>
                        <th class="col-md">Truck Type </th>
                        <th class="col-md">Weight </th>






                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalkg = 0;
                    @endphp
                    @foreach ($antriantdy as $key => $value)
                        <tr>
                            <td></td>
                            <td>{{ $antriantdy->firstItem() + $key }}</td>
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
                            <td>{{ $value->tmQtyKg }}</td>
                            @php
                                $totalkg = $totalkg + $value->tmQtyKg;
                            @endphp

                        </tr>
                    @endforeach

                </tbody>
            </table>
            <div class="offset-10">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Total = {{ number_format($totalkg) }}
            </div>
        </div>
    </div>
    <!-- AKHIR antrian hari ini -->
</div>
