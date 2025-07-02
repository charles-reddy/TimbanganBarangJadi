<div>
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
        <div wire:poll.5s>
            {{ now() }}
            </div>
        <div class="row">
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>REGISTRASI KIRIM MATERIAL</h2>
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
                        <label for="nama" class="col-sm-2 col-form-label">Driver</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="driver">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="carID" class="col-sm-2 col-form-label">Car ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-25" wire:model="carID">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="suppID" class="col-sm-2 col-form-label">Customer</label>
                        
                        <div class="col-sm-10" wire:ignore>
                            <select   class="js-example-basic-single w-50" id="my-suppID"  wire:model="suppID"  >
                                <option></option>
                                @foreach ($supplier as $item)
                                    <option value="{{ $item->suppID }}" selected >{{ $item->suppName }}</option>
                                @endforeach
                                
                            </select>
                            
                            
                        </div>
                        <label for="suppID" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                        
                            @if ( $isDisabled  == false)
                                <input type="text" class="form-control w-25" wire:model="suppID1" hidden>
                            @else
                                <input type="text" class="form-control w-25" wire:model="suppID1" disabled>
                            @endif
                           
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
                        </div>
                        <label for="suppID" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                        
                            @if ( $isDisabled  == false)
                                <input type="text" class="form-control w-25" wire:model="itemCode1" hidden>
                            @else
                                <input type="text" class="form-control w-25" wire:model="itemCode1" disabled>
                            @endif
                           
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="doNo" class="col-sm-2 col-form-label">DO No</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" wire:model='doNo' >
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="poNo" class="col-sm-2 col-form-label">po No</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" wire:model='poNo' >
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="remarks" class="col-sm-2 col-form-label">Remarks</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" wire:model="remarks">
                        </div>
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
        </form>
    </div>
    <!-- AKHIR FORM -->


     <!-- START DATA -->
     <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data Registrasi Timbang In Material</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ..." wire:model.live="katakunci">
        </div>

        

        {{ $datascale->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md-1">No</th>
                    <th class="col-md-2 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >Driver</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('carID')" >Car ID</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='suppName') {{ $sortDirection }}   @endif" wire:click="sort('suppName')" >Pengirim</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='itemName') Item Name $sortDirection }}   @endif" wire:click="sort('itemName')" >Item Name</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='jam_reg') ITgl Reg Name $sortDirection }}   @endif" wire:click="sort('jam_reg')" >Tgl Reg</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='doNo') Item Name $sortDirection }}   @endif" wire:click="sort('doNo')" >DO</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='poNo') PO $sortDirection }}   @endif" wire:click="sort('poNo')" >PO</th>
                    <th class="col-md-2 sort desc @if($sortColumn=='remarks') Item Name $sortDirection }}   @endif" wire:click="sort('remarks')" >Remarks</th>
                    
            </thead>
            <tbody>
                @foreach ($datascale as $key => $value)
                <tr>
                    {{-- <td><input type="checkbox" wire:key="{{ $value->id }}" value="{{ $value->id }}" wire:model.live="trscaleSelectedID"></td> --}}
                    <td></td>
                    <td>{{ $datascale->firstItem() + $key }}</td>
                    <td>{{ $value->driver }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->suppName }}</td>
                    <td>{{ $value->itemName }}</td>
                    <td>{{ date('d-m-Y H:i',strtotime($value->jam_reg)) }}</td>
                    <td>{{ $value->doNo }}</td>
                    <td>{{ $value->poNo }}</td>
                    <td>{{ $value->remarks }}</td>
                    
                    <td>
                        @if (auth()->user()->hasrole('administrator') || auth()->user()->hasrole('supervisor-timbangan-registrasi') || auth()->user()->hasrole('manager-logistik'))
                             <a wire:click="edit({{ $value->id }})" class="btn btn-warning btn-sm">Edit</a>
                        @endif
                        <!-- <a wire:click="deleteConfirmation({{ $value->id }})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Del</a>  -->
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
        <script>
            $(document).ready(function() {
                $('.js-example-basic-single').select2();

                $('#my-suppID').on('change',function(e) {
                    var data = $('#my-suppID').select2("val");
                    @this.set('suppID',data);
                })


                // $(document).ready(function() {
                //     $('#my-custID').select2({
                //         placeholder: "Seleccione el Género"
                //     }).prepend('<option selected=""></option>')
                //     $('#my-custID').on('change', function(e) {
                //         @this.set('custID', e.target.value);
                //     });
                // });

                
                $('#my-itemCode').on('change',function(e) {
                    var data = $('#my-itemCode').select2("val");
                    @this.set('itemCode',data);
                })

                
            });
            
        </script>
    </div>
    <!-- AKHIR DATA -->


</div>
