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



    <!-- START FORM SPPB-->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        {{-- @include('layouts.navbar') --}}
        <div >
            {{ now() }}
            </div>
        <div class="row">
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Create SPPB</h2>
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
                        <label for="nama" class="col-sm-2 col-form-label">SPPB No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="sppbNo" >
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="" class="col-sm-2 col-form-label">No Kontrak</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="kontrakNo" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="" class="col-sm-2 col-form-label">PO No</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="poNo" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="itemCode" class="col-sm-2 col-form-label">Item Code</label>
                        <div class="col-sm-10" wire:ignore>
                            <select class="js-example-basic-single w-50"  id="my-itemCode" wire:model="itemCode">
                                <option></option>
                                @foreach ($product as $item)
                                    <option value="{{ $item->itemCode }}">{{ $item->itemName }}</option>
                                @endforeach
                            </select>
                            <input type="text" class="form-control w-50 mt-2" wire:model="itemName" disabled>
                        </div>
                        
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="custID" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10" wire:ignore>
                            <select   class="js-example-basic-single w-50" id="my-custID"  wire:model="custID"  >
                                <option></option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->custID }}" >{{ $item->custName }}</option>
                                @endforeach
                                
                            </select>
                            <input type="text" class="form-control w-50 mt-2" wire:model="custName" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Qty (Kg)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="sppbQtyKg" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Qty (Karung)</label>
                        <div class="col-sm-10">
                            <input type="text" id="input" class="form-control w-50" wire:model="sppbQtyKarung" autocomplete="off">
                        </div>
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
        <h1>Data SPPB</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ...  SPPB No" wire:model.live="katakunci">
        </div>

        {{-- @if ($trscaleSelectedID) --}}
            {{-- <a wire:click="deleteConfirmation('')" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Del {{ count($trscaleSelectedID) }}  Data</a>   --}}
            {{-- <a wire:click="delete()" wire:confirm="Yakin Hapus data?"  class="btn btn-danger btn-sm mb-3">{{ count($trscaleSelectedID) }}  Data</a> --}}
        {{-- @endif --}}

        {{ $datasppb->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-ms-1">No</th>
                    <th class="col-ms-1 sort @if($sortColumn=='spmNo') {{ $sortDirection }}   @endif" wire:click="sort('sppbNo')" >No SPPB</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='tglSpm') {{ $sortDirection }}   @endif" wire:click="sort('tglSppb')" >Tgl SPPB</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('kontrakNo')" >No Kontrak</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='itemName') {{ $sortDirection }}   @endif" wire:click="sort('itemName')" >Nama Barang</th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='transpName') {{ $sortDirection }}   @endif" wire:click="sort('custName')" >Nama Customer </th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='qtyKg') {{ $sortDirection }}   @endif" wire:click="sort('sppbQtyKg')" >Qty Kg </th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='qtyKarung') {{ $sortDirection }}   @endif" wire:click="sort('sppbQtyKarung')" >Qty Karung </th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='qtyKg') {{ $sortDirection }}   @endif" wire:click="sort('openQtyKg')" >Open Qty Kg </th>
                    <th class="col-ms-1 sort desc @if($sortColumn=='qtyKarung') {{ $sortDirection }}   @endif" wire:click="sort('openQtyKarung')" >Open Qty Karung </th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($datasppb as $key => $value)
                <tr>
                    {{-- <td><input type="checkbox" wire:key="{{ $value->id }}" value="{{ $value->id }}" wire:model.live="trscaleSelectedID"></td> --}}
                    <td></td>
                    <td>{{ $datasppb->firstItem() + $key }}</td>
                    <td>{{ $value->sppbNo }}</td>
                    <td>{{ date('d-m-Y H:i',strtotime($value->tglSppb)) }}</td>
                    <td>{{ $value->kontrakNo }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ $value->sppbQtyKg }}</td>
                    <td>{{ $value->sppbQtyKarung }}</td>
                    <td>{{ $value->openQtyKg }}</td>
                    <td>{{ $value->openQtyKarung }}</td>
                    <td>
                        {{-- <a href="/cetakspm/{{ $value->id }} " class="btn btn-primary" target="_blank" >cetak</a> --}}
                         <a wire:click="edit({{ $value->id }})" class="btn btn-warning btn-sm">Edit</a>
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


                // $(document).ready(function() {
                //     $('#my-custID').select2({
                //         placeholder: "Seleccione el Género"
                //     }).prepend('<option selected=""></option>')
                //     $('#my-custID').on('change', function(e) {
                //         @this.set('custID', e.target.value);
                //     });
                // });

                $('#my-transpID').on('change',function(e) {
                    var data = $('#my-transpID').select2("val");
                    @this.set('transpID',data);
                })

                $('#my-itemCode').on('change',function(e) {
                    var data = $('#my-itemCode').select2("val");
                    @this.set('itemCode',data);
                })

                
            });
            
        </script>
    </div>
    <!-- AKHIR DATA SPM-->


    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            $('#my-custID').on('change',function(e) {
                var data = $('#my-custID').select2("val");
                @this.set('custID',data);
            })


            // $(document).ready(function() {
            //     $('#my-custID').select2({
            //         placeholder: "Seleccione el Género"
            //     }).prepend('<option selected=""></option>')
            //     $('#my-custID').on('change', function(e) {
            //         @this.set('custID', e.target.value);
            //     });
            // });

            $('#my-transpID').on('change',function(e) {
                var data = $('#my-transpID').select2("val");
                @this.set('transpID',data);
            })

            $('#my-itemCode').on('change',function(e) {
                var data = $('#my-itemCode').select2("val");
                @this.set('itemCode',data);
            })

            
        });
        
    </script>
</div>
