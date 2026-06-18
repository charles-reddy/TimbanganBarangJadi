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



    {{-- form pembatalan --}}

    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div>
            {{ now() }} - {{ request()->ip() }}
        </div>
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Cancel Approval Tiket Muat </h2>
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
                            <input type="text" class="form-control w-50" wire:model="tiketMuat" disabled>
                        </div>
                    </div>

                </div>

            </div>






            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">

                    <button type="button" class="btn btn-primary" name="submit" wire:click="store()">Cancel</button>

                    <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>

                </div>
            </div>
        </form>
    </div>



    {{-- start report --}}
    <div class="card-body">
        <div class="card-body table-responsive p-0">
            <div class="my-3 p-3 bg-body rounded shadow-sm">
                <h1></h1>
                <div class="mb-3 row">
                    <div class="col">
                        <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 30rem;">
                            <h2>Tiket Muat - Approved </h2>
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
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label for="product" class="form-label small text-muted">Product</label>
                            <select id="product" class="form-select" multiple wire:model.live="kataproduct"
                                size="1">
                                <option value="">-- Select Products --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->itemCode }}">{{ $product->itemName }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label for="tglmuat" class="form-label small text-muted">Tanggal Muat</label>
                            <input type="date" id="tglmuat" class="form-control" wire:model.live="tglMuat">
                        </div>
                    </div>



                    {{ $datatiketmuat->links() }}

                    <div class="table-responsive">

                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
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
                                    <th class="text-center">Pilih</th>
                                    <th class="text-center">Cancel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalkg = 0;
                                    $no = $datatiketmuat->firstItem();
                                @endphp
                                @foreach ($datatiketmuat as $key)
                                    <tr>
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
                                            <a wire:click="edit('{{ $key->id }}')"
                                                class="btn btn-warning btn-sm">Pilih</a>
                                        </td>
                                        <td class="text-center">
                                            <a wire:click="cancel('{{ $key->id }}')"
                                                class="btn btn-danger btn-sm">Cancel</a>
                                        </td>
                                    </tr>
                                    @php
                                        $totalkg += $key->tmQtyKg;
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total Weight:</td>
                                    <td class="text-end fw-bold">{{ number_format($totalkg) }} Kg</td>
                                    <td colspan="8"></td>
                                </tr>
                            </tfoot>
                        </table>
                        {{ $datatiketmuat->links() }}
                    </div>
                    <div class="card-body table-responsive p-0">
                        <div class="row">

                            <form>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3 mt-3 row">
                                            <label for="nama" class="col-sm-2 col-form-label">Tiket Muat</label>

                                            <input type="text" class="form-control w-50" wire:model="transID"
                                                hidden>

                                            {{-- <div class="col-sm-10">
                                                        <input type="text" class="form-control w-25 mb-2" id="tta_number" wire:model="tiketMuat" disabled>
                                                            <a href="/cetaktiket/{{ $transID }} " class="btn btn-primary" target="_blank" >cetak</a>
                                                    </div> --}}
                                        </div>



                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="card border-primary mb-3" style="max-width: 200rem;">
                                                <div class="card-header  text-center">Foto SIM / KTP</div>
                                                <div class="card-body text-primary">
                                                    <img class="rounded float-start" style="width: 750px"
                                                        src="{{ $simKtp }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card border-primary mb-3" style="max-width: 200rem;">
                                                <div class="card-header  text-center">Foto STNK</div>
                                                <div class="card-body text-primary">
                                                    <img class="rounded float-start" style="width: 750px"
                                                        src="{{ $stnk }}">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
