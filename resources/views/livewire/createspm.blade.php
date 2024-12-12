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
            <span class="font-medium">Danger alert!</span> {{ session("error") }} 
            </div>
        </div>
    </div>
    @endif
    {{-- END OF ERROR MESSAGE --}}

    <!-- START FORM -->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        {{-- @include('layouts.navbar') --}}
        <div >
            {{ now() }}
            </div>
        <div class="row">
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Surat Perintah Muat</h2>
                </div>
            </div>
        </div>
        <form>
            <div class="row">
                <div class="col">
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">SPM No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="spmNo" disabled>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="itemCode" class="col-sm-2 col-form-label">SPPB No</label>
                        <div class="col-sm-10" wire:ignore>
                            <select class="js-example-basic-single w-50"  id="my-sppbNo" wire:model="sppbNo">
                                <option></option>
                                @foreach ($listsppb as $item)
                                    <option value="{{ $item->id }}">{{ $item->sppbNo }} - {{ $item->custName }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control w-50 mt-2" wire:model="itemName" disabled> --}}
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="" class="col-sm-2 col-form-label">No Registrasi</label>
                        <div class="col-sm-10" wire:ignore>
                            <select class="js-example-basic-single w-50"  id="my-tiketID" wire:model="tiketID">
                                <option></option>
                                @foreach ($antrian as $item)
                                    <option value="{{ $item->no }}">{{ $item->token }} - {{ $item->nodo }} - {{ $item->cust }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control w-50 mt-2" wire:model="itemName" disabled> --}}
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Plat No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="carID" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Driver</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="driver" >
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Seal No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="sealNo" autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Container No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="kontainerNo" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="itemCode" class="col-sm-2 col-form-label">Item Code</label>
                        <div class="col-sm-10" >
                            <select class="js-example-basic-single w-50"  id="my-itemCode" wire:model="itemCode">
                                <option></option>
                                @foreach ($product as $item)
                                    <option value="{{ $item->itemCode }}">{{ $item->itemName }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control w-50 mt-2" wire:model="itemName" disabled> --}}
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="packingID" class="col-sm-2 col-form-label">Kemasan</label>
                        <div class="col-sm-10" >
                            <select class="js-example-basic-single w-50"  id="my-packingID" wire:model="packingID">
                                <option></option>
                                @foreach ($kemasan as $item)
                                    <option value="{{ $item->packingID }}">{{ $item->packingName }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control w-50 mt-2" wire:model="itemName" disabled> --}}
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="transpID" class="col-sm-2 col-form-label">Transporter</label>
                        <div class="col-sm-10" wire:ignore>
                            <select class="js-example-basic-single w-50" id="my-transpID" wire:model="transpID">
                                <option></option>
                                @foreach ($transporter as $item)
                                    <option value="{{ $item->transpID }}">{{ $item->transpName }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control w-50 mt-2" wire:model="transpName" disabled> --}}
                        </div>
                    </div> 
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Qty (Kg)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="qtyKg" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Qty (Karung)</label>
                        <div class="col-sm-10">
                            <input type="text" id="input" class="form-control w-50" wire:model="qtyKarung" autocomplete="off">
                        </div>
                    </div>
                    
                    

                </div>
                
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    {{-- @if ($updateData == false) --}}
                        <button type="button" class="btn btn-primary" name="submit" wire:click="store()">SIMPAN</button>
                    {{-- @else
                        <button type="button" class="btn btn-primary" name="submit" wire:click="update()">UPDATE</button>
                    @endif --}}
                        <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                    
                </div>
    </div>
    <!-- START DATA SPM -->
    
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data SPM</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Car ID / SPPB No" wire:model.live="katakunci">
        </div>

        {{-- @if ($trscaleSelectedID) --}}
            {{-- <a wire:click="deleteConfirmation('')" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Del {{ count($trscaleSelectedID) }}  Data</a>   --}}
            {{-- <a wire:click="delete()" wire:confirm="Yakin Hapus data?"  class="btn btn-danger btn-sm mb-3">{{ count($trscaleSelectedID) }}  Data</a> --}}
        {{-- @endif --}}

        {{ $dataspm->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-ms-1">No</th>
                    <th class="col-ms-1 sort @if($sortColumn=='spmNo') {{ $sortDirection }}   @endif" wire:click="sort('spmNo')" >No SPM</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='tglSpm') {{ $sortDirection }}   @endif" wire:click="sort('tglSpm')" >Tgl SPM</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('carID')" >Plat No</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='sppbNo') {{ $sortDirection }}   @endif" wire:click="sort('sppbNo')" >No SPPB</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='itemName') {{ $sortDirection }}   @endif" wire:click="sort('itemName')" >Nama Barang</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='transpName') {{ $sortDirection }}   @endif" wire:click="sort('transpName')" >Nama Transporter </th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='qtyKg') {{ $sortDirection }}   @endif" wire:click="sort('qtyKg')" >Qty Kg </th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='qtyKarung') {{ $sortDirection }}   @endif" wire:click="sort('qtyKarung')" >Qty Karung </th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($dataspm as $key => $value)
                <tr>
                    {{-- <td><input type="checkbox" wire:key="{{ $value->id }}" value="{{ $value->id }}" wire:model.live="trscaleSelectedID"></td> --}}
                    <td></td>
                    <td>{{ $dataspm->firstItem() + $key }}</td>
                    <td>{{ $value->spmNo }}</td>
                    <td>{{ date('d-m-Y H:i',strtotime($value->tglSpm)) }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->sppbNo }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->transpName }}</td>
                    <td>{{ $value->qtyKg }}</td>
                    <td>{{ $value->qtyKarung }}</td>
                    <td>
                        <a href="/cetakspm/{{ $value->id }} " class="btn btn-primary" target="_blank" >cetak</a>
                    </td>
                </tr> 
                @endforeach
                
            </tbody>
        </table>
        <script>
            $(document).ready(function() {
                $('.js-example-basic-single').select2();

                $('#my-custID').on('change',function(e) {
                    var data = $('#my-custID').select2("val");
                    @this.set('custID',data);
                })

                $('#my-sppbNo').on('change',function(e) {
                    var data = $('#my-sppbNo').select2("val");
                    @this.set('sppbNo',data);
                })

                $('#my-tiketID').on('change',function(e) {
                    var data = $('#my-tiketID').select2("val");
                    @this.set('tiketID',data);
                })
                

                $('#my-transpID').on('change',function(e) {
                    var data = $('#my-transpID').select2("val");
                    @this.set('transpID',data);
                })

                $('#my-packingID').on('change',function(e) {
                    var data = $('#my-packingID').select2("val");
                    @this.set('packingID',data);
                })

                $('#my-itemCode').on('change',function(e) {
                    var data = $('#my-itemCode').select2("val");
                    @this.set('itemCode',data);
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

                $('#my-itemCode').on('change',function(e) {
                    var data = $('#my-itemCode').select2("val");
                    @this.set('itemCode',data);
                })
                $('#my-transpID').on('change',function(e) {
                    var data = $('#my-transpID').select2("val");
                    @this.set('transpID',data);
                })

                
               
        });
    </script>

   
</div>
