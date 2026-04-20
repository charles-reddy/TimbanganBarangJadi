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
            {{ now() }}
        </div>
        <div class="row">
            <div class="col">
                <div class="card m-auto mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Input Quota Harian Loading</h2>
                </div>
            </div>
        </div>
        <form>
            <div class="row">
                <div class="col">
                    <div class="mb-3 mt-3 row">
                        <label for="quotaTglDatang" class="col-sm-2 col-form-label">Tanggal Kedatangan</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control w-50" wire:model="quotaTglDatang"
                                id="quotaTglDatang">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="quota1" class="col-sm-2 col-form-label">Quota Shift 1</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control w-50" wire:model="quota1" id="quota1"
                                min="0">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="quota2" class="col-sm-2 col-form-label">Quota Shift 2</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control w-50" wire:model="quota2" id="quota2"
                                min="0">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="quota3" class="col-sm-2 col-form-label">Quota Shift 3</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control w-50" wire:model="quota3" id="quota3"
                                min="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    <button type="button" class="btn btn-primary" wire:click="save()">
                        {{ $updateData ? 'UPDATE' : 'SIMPAN' }}
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="clear()">CLEAR</button>
                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->

    <!-- START DATA QUOTA -->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h1>Data Quota Loading</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25"
                placeholder="Cari Tanggal (dd-mm-yyyy) atau ketik - untuk null" wire:model.live="katakunci">
            <small class="text-muted">Format: dd-mm-yyyy (contoh: 01-04-2026) atau ketik <strong>-</strong> untuk data
                tanggal kosong</small>
        </div>

        {{ $quotaData->links() }}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-md-1">No</th>
                    <th class="col-md-2">Tanggal Kedatangan</th>
                    <th class="col-md-1">Quota Shift 1</th>
                    <th class="col-md-1">Quota Shift 2</th>
                    <th class="col-md-1">Quota Shift 3</th>
                    <th class="col-md-1">Total</th>
                    <th class="col-md-2">Status</th>
                    <th class="col-md-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = ($quotaData->currentPage() - 1) * $quotaData->perPage() + 1;
                @endphp
                @foreach ($quotaData as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->quotaTglDatang ? \Carbon\Carbon::parse($item->quotaTglDatang)->format('d-m-Y') : '-' }}
                        </td>
                        <td>{{ $item->quota1 ?? '-' }}</td>
                        <td>{{ $item->quota2 ?? '-' }}</td>
                        <td>{{ $item->quota3 ?? '-' }}</td>
                        <td><strong>{{ ($item->quota1 ?? 0) + ($item->quota2 ?? 0) + ($item->quota3 ?? 0) > 0 ? number_format(($item->quota1 ?? 0) + ($item->quota2 ?? 0) + ($item->quota3 ?? 0), 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td>
                            @if ($item->isApprove)
                                <span class="badge bg-success">✓ Approved</span><br>
                                <small
                                    class="text-muted">{{ $item->approvedByName ?? 'User ID: ' . $item->approvedBy }}</small><br>
                                <small
                                    class="text-muted">{{ $item->approvedAt ? \Carbon\Carbon::parse($item->approvedAt)->format('d-m-Y H:i') : '' }}</small>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if (!$item->isApprove)
                                <button wire:click="edit({{ $item->id }})"
                                    class="btn btn-warning btn-sm mb-1">Edit</button>
                                <button wire:click="delete({{ $item->id }})"
                                    onclick="return confirm('Yakin hapus data ini?')"
                                    class="btn btn-danger btn-sm mb-1">Delete</button>

                                @if (auth()->user()->hasRole(['administrator', 'manager-logistik']))
                                    <button wire:click="approve({{ $item->id }})"
                                        class="btn btn-success btn-sm mb-1"
                                        onclick="return confirm('Approve quota harian ini?')">
                                        Approve
                                    </button>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $quotaData->links() }}
    </div>
    <!-- AKHIR DATA QUOTA -->
</div>
