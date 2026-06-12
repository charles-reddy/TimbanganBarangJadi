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
        {{-- @include('layouts.navbar') --}}
        <div wire:poll.30s>
            {{ now() }}
        </div>
        <div class="row">
            <div class="row">
                <div class="col">
                    <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                        <h2>TIMBANG MASUK MATERIAL</h2>
                    </div>
                </div>
            </div>

            <!-- MODE SELECTOR -->
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <div class="btn-group" role="group" aria-label="Entry Mode">
                        <button type="button"
                            class="btn {{ $entryMode == 'registered' ? 'btn-primary' : 'btn-outline-primary' }}"
                            wire:click="switchMode('registered')">
                            📋 Dengan Registrasi
                        </button>
                        <button type="button"
                            class="btn {{ $entryMode == 'manual' ? 'btn-warning' : 'btn-outline-warning' }}"
                            wire:click="switchMode('manual')">
                            ✍️ Input Manual
                        </button>
                    </div>
                    <p class="text-muted mt-2">
                        @if ($entryMode == 'registered')
                            <small>Data otomatis terisi dari registrasi (H-3)</small>
                        @else
                            <small>Isi semua field secara manual - untuk truk tanpa registrasi</small>
                        @endif
                    </p>
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

                        @if ($entryMode == 'registered')
                            <!-- MODE 1: Dengan Registrasi -->
                            <div class="mb-3 row">
                                <label for="custID" class="col-sm-2 col-form-label">No Registrasi <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10" wire:ignore>
                                    <select class="js-example-basic-single w-50" id="my-regNo" wire:model="regNo">
                                        <option></option>
                                        @foreach ($datareg1 as $item)
                                            <option value="{{ $item->id }}">{{ $item->carID }} -
                                                {{ $item->suppName }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3 mt-3 row">
                            <label for="nama" class="col-sm-2 col-form-label">Driver
                                @if ($entryMode == 'manual')
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control w-50" wire:model="driver"
                                    {{ $entryMode == 'registered' ? 'disabled' : '' }}
                                    placeholder="{{ $entryMode == 'manual' ? 'Masukkan nama driver' : '' }}">
                            </div>
                        </div>


                        <div class="mb-3 row">
                            <label for="carID" class="col-sm-2 col-form-label">Car ID
                                @if ($entryMode == 'manual')
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control w-25" wire:model="carID"
                                    {{ $entryMode == 'registered' ? 'disabled' : '' }}
                                    placeholder="{{ $entryMode == 'manual' ? 'Masukkan nomor polisi' : '' }}">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="carID" class="col-sm-2 col-form-label">Supplier
                                @if ($entryMode == 'manual')
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="col-sm-10">
                                @if ($entryMode == 'registered')
                                    <input type="text" class="form-control w-50" wire:model="suppID" disabled>
                                @else
                                    <div wire:ignore>
                                        <select class="js-supplier-select w-50" id="my-supplier" wire:model="suppIDRaw">
                                            <option value="">-- Pilih Supplier --</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->suppID }}">{{ $supplier->suppName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="mb-3 row">
                            <label for="carID" class="col-sm-2 col-form-label">Item Desc.
                                @if ($entryMode == 'manual')
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="col-sm-10">
                                @if ($entryMode == 'registered')
                                    <input type="text" class="form-control w-50" wire:model="itemCode" disabled>
                                @else
                                    <div wire:ignore>
                                        <select class="js-product-select w-50" id="my-product" wire:model="itemCodeRaw">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->itemCode }}">{{ $product->itemName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="doNo" class="col-sm-2 col-form-label">DO No</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" wire:model='doNo'
                                    {{ $entryMode == 'registered' ? 'disabled' : '' }}
                                    placeholder="{{ $entryMode == 'manual' ? 'Opsional' : '' }}">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="poNo" class="col-sm-2 col-form-label">PO No</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" wire:model='poNo'
                                    {{ $entryMode == 'registered' ? 'disabled' : '' }}
                                    placeholder="{{ $entryMode == 'manual' ? 'Opsional' : '' }}">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="remarks" class="col-sm-2 col-form-label">Remarks</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" wire:model="remarks">
                            </div>
                        </div>


                    </div>
                    <div class="col">
                        <div class="card mt-3 " style="width: 25rem;">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm5">
                                        <div>
                                            <input type="text" class="form-control w-50" wire:model="timbangin"
                                                disabled>
                                        </div>
                                        <span></span>

                                        <div class="col">
                                            <button type="button" class="btn btn-primary mt-2"
                                                wire:click.prevent="timbang()">Timbang</button>
                                        </div>
                                        <div class="col">
                                            <select class="mb-2 mt-2" wire:model="timbanganID">
                                                <option value="">---pilih timbangan---</option>
                                                @foreach ($timbangan as $item)
                                                    <option value="{{ $item->timbanganID }}">
                                                        {{ $item->timbanganNama }}</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        {{-- <div class="mb-3 mt-3 row">
                                        
                                        <div class="col-sm-10">
                                            <img  src="http://10.20.12.208/cgi-bin/encoder?USER=apps&PWD=Tebumas12&GET_STREAM" id="video"  >
                                            <button id="take-snapshot" wire:click.prevent>take snapshot</button>
                                            
                                        </div>
                                    </div>

                                    <div class="mb-3 mt-3 row">
                                        
                                        <div class="col-sm-10" >
                                            <canvas id="canvas" ></canvas>
                                            <div id="dataurl-header">Image Data url</div>
                                            <textarea id="dataurl" readonly></textarea>
                                        </div>
                                    </div> --}}

                                    </div>

                                </div>
                            </div>







                        </div>

                    </div>

                </div>





                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-10">
                        <!-- Mode Badge -->
                        <div class="mb-2">
                            @if ($entryMode == 'registered')
                                <span class="badge bg-primary">📋 Mode: Dengan Registrasi</span>
                            @else
                                <span class="badge bg-warning text-dark">✍️ Mode: Input Manual</span>
                            @endif
                        </div>

                        <!-- Loading Indicator -->
                        <div wire:loading class="mb-2">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            <span class="text-muted ms-2">Memproses...</span>
                        </div>

                        @if ($updateData == false)
                            <button type="button" class="btn btn-primary" name="submit" wire:click="store()"
                                wire:loading.attr="disabled">SIMPAN</button>
                        @else
                            <button type="button" class="btn btn-primary" name="submit" wire:click="update()"
                                wire:loading.attr="disabled">UPDATE</button>
                        @endif
                        <button type="button" class="btn btn-secondary" name="submit"
                            wire:click="clear()">CLEAR</button>

                    </div>
                </div>
            </form>
        </div>
        <!-- AKHIR FORM -->

        <!-- START DATA -->
        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <h1>Data Timbang Masuk</h1>
            <div class="pb-3 pt-3">
                <input type="text" class="form-control mb-3 w-25" placeholder="Searching ..."
                    wire:model.live="katakunci">
            </div>

            @if ($trscaleSelectedID)
                {{-- <a wire:click="deleteConfirmation('')" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Del {{ count($trscaleSelectedID) }}  Data</a>   --}}
                <a wire:click="delete()" wire:confirm="Yakin Hapus data?"
                    class="btn btn-danger btn-sm mb-3">{{ count($trscaleSelectedID) }} Data</a>
            @endif

            {{ $datatim->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md-1">No</th>
                        <th class="col-md-2 sort @if ($sortColumn == 'driver') {{ $sortDirection }} @endif"
                            wire:click="sort('driver')">Driver</th>
                        <th class="col-md-2 sort desc @if ($sortColumn == 'carID') {{ $sortDirection }} @endif"
                            wire:click="sort('carID')">Car ID</th>
                        <th class="col-md-2 sort desc @if ($sortColumn == 'suppName') {{ $sortDirection }} @endif"
                            wire:click="sort('suppName')">Supplier</th>
                        <th class="col-md-2 sort desc @if ($sortColumn == 'itemCode') {{ $sortDirection }} @endif"
                            wire:click="sort('itemCode')">Item Name </th>
                        <th class="col-md-2 sort desc @if ($sortColumn == 'jam_in') {{ $sortDirection }} @endif"
                            wire:click="sort('jam_in')">Tgl Masuk </th>
                        <th class="col-md-2 sort desc @if ($sortColumn == 'timbangin') {{ $sortDirection }} @endif"
                            wire:click="sort('timbangin')">Bobot IN </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($datatim as $key => $value)
                        <tr>
                            {{-- <td><input type="checkbox" wire:key="{{ $value->id }}" value="{{ $value->id }}" wire:model.live="trscaleSelectedID"></td> --}}
                            <td></td>
                            <td>{{ $datatim->firstItem() + $key }}</td>
                            <td>{{ $value->driver }}</td>
                            <td>{{ $value->carID }}</td>
                            <td>{{ $value->suppName }}</td>
                            <td>{{ $value->itemName }}</td>
                            <td>{{ date('d-m-Y H:i', strtotime($value->jam_in)) }}</td>
                            <td>{{ $value->timbangin }}</td>

                            <td>
                                {{-- <a wire:click="edit({{ $value->id }})" class="btn btn-warning btn-sm">Edit</a>
                        <a wire:click="deleteConfirmation({{ $value->id }})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Del</a> --}}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            <script>
                $(document).ready(function() {
                    $('.js-example-basic-single').select2();
                    $('.js-supplier-select').select2({
                        placeholder: "-- Cari Supplier --",
                        allowClear: true
                    });
                    $('.js-product-select').select2({
                        placeholder: "-- Cari Produk --",
                        allowClear: true
                    });

                    // $('#my-custID').on('change',function(e) {
                    //     var data = $('#my-custID').select2("val");
                    //     @this.set('custID',data);
                    // })





                    // $(document).ready(function() {
                    //     $('#my-custID').select2({
                    //         placeholder: "Seleccione el Género"
                    //     }).prepend('<option selected=""></option>')
                    //     $('#my-custID').on('change', function(e) {
                    //         @this.set('custID', e.target.value);
                    //     });
                    // });

                    // $('#my-transpID').on('change',function(e) {
                    //     var data = $('#my-transpID').select2("val");
                    //     @this.set('transpID',data);
                    // })

                    $('#my-regNo').on('change', function(e) {
                        var data = $('#my-regNo').select2("val");
                        @this.set('regNo', data);
                    })

                    // Handle supplier change
                    $('#my-supplier').on('change', function(e) {
                        var data = $('#my-supplier').select2("val");
                        @this.set('suppIDRaw', data);
                    })

                    // Handle product change
                    $('#my-product').on('change', function(e) {
                        var data = $('#my-product').select2("val");
                        @this.set('itemCodeRaw', data);
                    })

                    // Reinitialize Select2 when Livewire updates the DOM
                    Livewire.hook('message.processed', (message, component) => {
                        $('.js-example-basic-single').select2();
                        $('.js-supplier-select').select2({
                            placeholder: "-- Cari Supplier --",
                            allowClear: true
                        });
                        $('.js-product-select').select2({
                            placeholder: "-- Cari Produk --",
                            allowClear: true
                        });

                        $('#my-regNo').on('change', function(e) {
                            var data = $('#my-regNo').select2("val");
                            @this.set('regNo', data);
                        })

                        $('#my-supplier').on('change', function(e) {
                            var data = $('#my-supplier').select2("val");
                            @this.set('suppIDRaw', data);
                        })

                        $('#my-product').on('change', function(e) {
                            var data = $('#my-product').select2("val");
                            @this.set('itemCodeRaw', data);
                        })
                    });

                    // $('#my-itemCode').on('change',function(e) {
                    //     var data = $('#my-itemCode').select2("val");
                    //     @this.set('itemCode',data);
                    // })




                });
            </script>
            <script>
                // capture cctv




                // capture.addEventListener('click', ()=>{
                //     const context = output.getContext('2d');

                //     output.width = 300;
                //     output.height = 200;
                //     context.drawImage(cctv, 0, 0, output.width, output.height);


                //     const imageData = new Image();

                //     imageData = context.getImageData(0, 0, 1, 1);
                //     imageData.crossOrigin = "Anonymous";
                //     console.log(imageData);

                // });








                // capture cctv
                // <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js">
            </script>



            <script>
                let video = document.querySelector("#video");
                let btn_take_snapshot = document.querySelector("#take-snapshot");
                let canvas = document.getElementById("canvas");
                let dataurl = document.querySelector("#dataurl");
                let dataurl_container = document.querySelector("#dataurl-container");
                canvas.crossOrigin = 'anonymous';
                btn_take_snapshot.addEventListener('click', function() {
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    let image_data_url = canvas.toDataURL('image/png');

                    var data = image_data_url.getImageData(0, 0, 0, 0);
                    dataurl.value = data;
                    dataurl_container.style.display = 'block';
                });
            </script>

        </div>
        <!-- AKHIR DATA -->

        <!-- Modal -->
        <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Konfirmasi Hapus Data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Yakin akan menghapus data ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                        <button type="button" class="btn btn-primary" wire:click="delete()"
                            data-bs-dismiss="modal">Ya</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
