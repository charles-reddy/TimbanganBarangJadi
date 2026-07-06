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
        <div>
            {{ now() }} - {{ $this->ip }}
        </div>
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Cek Tiket Muat </h2>
                </div>
            </div>
        </div>
        <form>
            <div class="row">
                <div class="col">
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden>ID Transaction</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="transID" hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Tiket Muat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="pendfNo" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="custName" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Tgl Muat</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control w-50" wire:model.live="tglMuat">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Shift</label>
                        <div class="col-sm-10">
                            <select class="form-control w-50" wire:model.live="shift">
                                <option value="">Pilih Shift</option>
                                <option value="Shift 1">Shift 1 (08:00 - 12:00)</option>
                                <option value="Shift 2">Shift 2 (12:00 - 16:00)</option>
                                <option value="Shift 3">Shift 3 (16:00 - 20:00)</option>
                            </select>
                            @if ($quotaInfo)
                                <small class="text-muted d-block">{!! $quotaInfo !!}</small>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden>Tgl Muat1</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control w-50" wire:model="tglMuat1" hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Plat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="tmCarID" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Sopir</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="tmDriver" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Qty (Kg)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="tmQtyKg" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">SPPB No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="sppbNo" disabled>
                        </div>
                    </div>
                </div>

            </div>



            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">

                    <button type="button" class="btn btn-primary" name="submit" wire:click="store()"
                        {{ $quotaSufficient ? '' : 'disabled' }}>
                        SIMPAN
                    </button>

                    <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>

                    @if (!$quotaSufficient && $shift && $tglMuat)
                        <br><small class="text-danger mt-2 d-block">⚠️ Tombol simpan dinonaktifkan karena kuota tidak
                            mencukupi</small>
                    @endif

                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->


    <!-- START DATA tiket muat-->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h1>Data Tiket Muat</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Tiket Muat"
                wire:model.live="katakunci">
        </div>



        {{ $datatm->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md">Tiket Muat</th>
                    <th class="col-md">Tgl Muat</th>
                    <th class="col-md">Jam Muat</th>
                    <th class="col-md">Shift</th>
                    <th class="col-md">Customer</th>
                    <th class="col-md">Plat No</th>
                    <th class="col-md">Sopir</th>
                    <th class="col-md">Tgl Muat sebelumnya</th>
                    <th class="col-md">Dirubah Oleh</th>
                    <th class="col-md">Tgl dirubah</th>
                    <th class="col-md">Pilih</th>


                </tr>
            </thead>
            <tbody>
                @foreach ($datatm as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $datatm->firstItem() + $key }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ date('d-m-Y', strtotime($value->tglMuat)) }}</td>
                        <td>{{ $value->jamMuat ? date('H:i', strtotime($value->jamMuat)) : '-' }}</td>
                        <td>
                            @php
                                $jamMuatTime = $value->jamMuat ? date('H:i:s', strtotime($value->jamMuat)) : null;
                                $shift = 'Outside';
                                if ($jamMuatTime) {
                                    if ($jamMuatTime >= '08:00:00' && $jamMuatTime < '12:00:00') {
                                        $shift = 'Shift 1';
                                    } elseif ($jamMuatTime >= '12:00:00' && $jamMuatTime < '16:00:00') {
                                        $shift = 'Shift 2';
                                    } elseif ($jamMuatTime >= '16:00:00' && $jamMuatTime < '20:00:00') {
                                        $shift = 'Shift 3';
                                    }
                                }
                            @endphp
                            {{ $shift }}
                        </td>
                        <td>{{ $value->custName }}</td>
                        <td>{{ $value->tmCarID }}</td>
                        <td>{{ $value->tmDriver }}</td>
                        <td>{{ $value->tglMuat1Log ?? '-' }}</td>
                        <td>{{ $value->updatedBy ?? '-' }}</td>
                        <td>{{ isset($value->created_at) ? date('d-m-Y H:i', strtotime($value->created_at)) : '-' }}
                        </td>

                        <td>
                            <a wire:click="edit({{ $value->id }})" class="btn btn-primary btn-sm">Pilih</a>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    <!-- AKHIR DATA Tiket Muat -->
</div>
