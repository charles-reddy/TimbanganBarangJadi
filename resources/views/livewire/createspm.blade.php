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
                        <label for="nama" class="col-sm-2 col-form-label" hidden>ID Transaction</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="transID" hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">SPM No</label>
                        <div class="col-sm-10">
                        @if ( $isDisabled  == true)
                            <input type="text" class="form-control w-50" wire:model="spmNo1" disabled>
                        @else
                            <input type="text" class="form-control w-50" wire:model="spmNo" disabled>
                        @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="" class="col-sm-2 col-form-label">No Tiket Muat</label>
                        <div class="col-sm-10"  >
                        @if ( $isDisabled  == true)
                            <input type="text" class="form-control w-50 mt-2" wire:model="tiketMuat1" disabled>
                        @else
                            <select class="js-example-basic-single w-50"  id="my-tiketMuat" wire:model="tiketMuat">

                                <option></option>
                                @foreach ($datatm as $item)
                                    <option value="{{ $item->id }}">{{ $item->pendfNo }} - {{ $item->tmTranspName }} </option>
                                @endforeach
                            </select>
                        @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="itemCode" class="col-sm-2 col-form-label">SPPB No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50 mt-2" wire:model="sppbID" hidden> 
                            <input type="text" class="form-control w-50 mt-2" wire:model="sppbNo" disabled> 
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Plat No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="carID" autocomplete="off" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Driver</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control w-50" wire:model="driver" disabled >
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="itemCode" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10" >
                            <input type="text" class="form-control w-50 mt-2" wire:model="custID" hidden>
                            <input type="text" class="form-control w-50 mt-2" wire:model="custName" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">DN No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="dnNo" autocomplete="off">
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
                            <input type="text" class="form-control w-50 mt-2" wire:model="itemCode" hidden>
                            <input type="text" class="form-control w-50 mt-2" wire:model="itemName" disabled>
                            <input type="text" class="form-control w-50 mt-2" wire:model="itemType" disabled>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="packingID" class="col-sm-2 col-form-label">Kemasan</label>
                        <div class="col-sm-10"  >
                        @if ( $isDisabled  == true)
                            <input type="text" class="form-control w-50 mt-2" wire:model="packingID" disabled>
                        @else
                            <select class="js-example-basic-single w-50"  id="my-packingID" wire:model="packingID" style="border-radius: 10px;">
                                <option></option>
                                @foreach ($kemasan as $item)
                                    <option value="{{ $item->packingID }}">{{ $item->packingName }}</option>
                                @endforeach
                            </select>
                        @endif
                        </div>
                    </div>
                    <div class="mb-3  row">
                        <label for="isApp" class="col-sm-2 col-form-label">Local / Export</label>
                        <div class="col-sm-10" >
                        
                            <select class="js-example-basic-single w-25"  id="my-isExport" wire:model="isExport" style="border-radius: 10px;">
                                    <option >---Local / Export---</option>
                                    <option value="1">Local</option>
                                    <option value="2">Export</option>
                            </select>
                        
                        </div>
                    </div>

                    <div class="mb-3  row">
                        <label for="isApp" class="col-sm-2 col-form-label">Ekses SPPB Molases</label>
                        <div class="col-sm-10" >
                        
                            @if($itemType == 'FG')
                                <select class="js-example-basic-single w-25"  id="my-eksesMol" wire:model="eksesMol" style="border-radius: 10px;" disabled>
                                    <option value="0" >---Ekses Molases---</option>
                                        <option value="1">yes</option>
                                        
                                </select>
                            @else
                                <select class="js-example-basic-single w-25"  id="my-eksesMol" wire:model="eksesMol" style="border-radius: 10px;">
                                        <option value="0" >---Ekses Molases---</option>
                                        <option value="1">Yes</option>
                                        
                                </select>
                            @endif
                        
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Qty (Kg)</label>
                        <div class="col-sm-10">
                        @if($itemType == 'FG-L')
                            <input type="text" class="form-control w-50" wire:model="qtyKg"  autocomplete="off" >
                        @else
                            <input type="text" class="form-control w-50" wire:model="qtyKg"  autocomplete="off" disabled >
                        @endif
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Qty (Karung)</label>
                        <div class="col-sm-10">
                            @if($itemType == 'FG-L')
                                <input type="text" id="input" class="form-control w-50" wire:model="qtyKarung" autocomplete="off" >
                            @else
                                <input type="text" id="input" class="form-control w-50" wire:model="qtyKarung" autocomplete="off" disabled >
                            @endif
                                
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row" >
                        <label  class="col-sm-2 col-form-label">Jenis Truk</label>
                        <div class="col-sm-10" >
                        
                            <select class="js-example-basic-single w-50"  id="tmJenisTruk" wire:model="tmJenisTruk" style="border-radius: 10px;">
                                <option></option>
                                @foreach ($jenistruk as $item)
                                    <option value="{{ $item->id }}" selected>{{ $item->jenisTruk }}</option>
                                @endforeach
                            </select>
                        
                        </div>
                    </div>

                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden >Open Qty (Kg)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="openQtyKg"  autocomplete="off" disabled hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden>Open Qty (Karung)</label>
                        <div class="col-sm-10">
                            <input type="text" id="input" class="form-control w-50" wire:model="openQtyKarung" autocomplete="off" disabled hidden>
                        </div>
                    </div>

                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden> qty SPM awal (Kg)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="AwalQtyKg" autocomplete="off" disabled hidden>
                        
                        </div>
                    </div>

                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden>Open Qty awal (Kg)</label>
                        <div class="col-sm-10">
                        
                            <input type="text" class="form-control w-50" wire:model="AwalOpenQtyKg"  autocomplete="off" disabled hidden>
                        </div>
                    </div>

                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden>Qty SPM (Karung)</label>
                        <div class="col-sm-10">
                        
                            <input type="text" id="input" class="form-control w-50" wire:model="AwalQtyKarung" autocomplete="off" disabled hidden>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" hidden>Open Qty Awal (Karung)</label>
                        <div class="col-sm-10">
                            <input type="text" id="input" class="form-control w-25" wire:model="AwalOpenQtyKarung" autocomplete="off" disabled hidden>
                        </div>
                    </div>
                    
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                     @if ($updateData == false)
                        <button type="button" class="btn btn-primary" name="submit" wire:click="store()">SIMPAN</button>
                    @else
                        <button type="button" class="btn btn-primary" name="submit" wire:click="update()">UPDATE</button>
                    @endif
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
                    <th class="col-ms-1" >Tiket Muat</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='tglSpm') {{ $sortDirection }}   @endif" wire:click="sort('isExport')" >Local/Export</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='tglSpm') {{ $sortDirection }}   @endif" wire:click="sort('tglSpm')" >Tgl SPM</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('carID')" >Plat No</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='sppbNo') {{ $sortDirection }}   @endif" wire:click="sort('sppbNo')" >No SPPB</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='itemName') {{ $sortDirection }}   @endif" wire:click="sort('itemName')" >Nama Barang</th>
                    {{-- <th class="col-ms-1 sort desc @if($sortColumn=='transpName') {{ $sortDirection }}   @endif" wire:click="sort('transpName')" >Nama Transporter </th> --}}
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
                    <td>{{ $value->pendfNo }}</td>
                    <td>@php
                        echo (($value->isExport) == 1 ?  'local' :  'Export');
                        @endphp
                     </td>
                    <td>{{ date('d-m-Y H:i',strtotime($value->tglSpm)) }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->sppbNo }}</td>
                    <td>{{ $value->itemName }}</td>
                    {{-- <td>{{ $value->transpName }}</td> --}}
                    <td>{{ $value->qtyKg }}</td>
                    <td>{{ $value->qtyKarung }}</td>
                    
                    <td>
                        <a href="/cetakspm/{{ $value->id }} " class="btn btn-primary" target="_blank" >cetak</a> 
                        @if (auth()->user()->hasrole('administrator') || auth()->user()->hasrole('supervisor-timbangan-registrasi') || auth()->user()->hasrole('manager-logistik'))
                            <a wire:click="edit({{ $value->id }})" class="btn btn-warning btn-sm">Edit</a>
                        @endif
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

                $('#my-tiketMuat').on('change',function(e) {
                    var data = $('#my-tiketMuat').select2("val");
                    @this.set('tiketMuat',data);
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

                $('#my-isExport').on('change',function(e) {
                    var data = $('#my-isExport').select2("val");
                    @this.set('isExport',data);
                })


                $('#my-eksesMol').on('change',function(e) {
                    var data = $('#my-eksesMol').select2("val");
                    @this.set('eksesMol',data);
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
