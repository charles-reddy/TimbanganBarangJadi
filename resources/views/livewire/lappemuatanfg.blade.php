<div>
    <!-- START DATA Timbangan masuk-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data Timbang / Pemuatan(FG) </h1>
        <div class="row">
            <div class="col-sm-4" > 
                <label for="">Search</label>
                <input type="text" id="katakunciout" class="form-control mr-1 mb-3 w-50" placeholder="Type Driver or Car ID" wire:model.live="katakunciout">
                
            </div>
            <div class="col-sm-4 ms-2">
                <label for="">Filter by Date IN</label>
                <input type="date" id="tglin" class="form-control  mb-3 w-50"  wire:model.live="tglin">
            </div>
            <div>
                <button type="button" class="btn btn-primary" wire:click="clear()">Clear </button>
                <button type="button" class="btn btn-primary" wire:click="export_out()">Export</button>  
            </div>
        </div>

        @if ($trscaleSelectedID)
            {{-- <a wire:click="deleteConfirmation('')" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Del {{ count($trscaleSelectedID) }}  Data</a>   --}}
            <a wire:click="delete()" wire:confirm="Yakin Hapus data?"  class="btn btn-danger btn-sm mb-3">{{ count($trscaleSelectedID) }}  Data</a>
        
        @endif

        {{ $datascaleout->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md">No</th>
                    <th class="col-md-1 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >SO/SPPB</th>
                    <th class="col-md-1 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >PO</th>
                    <th class="col-md-1 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >Ekspedisi</th>
                    <th class="col-md-1 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >No.Seal</th>
                    <th class="col-md-1 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >Driver</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('carID')" >Car ID</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='custName') {{ $sortDirection }}   @endif" wire:click="sort('custName')" >Customer</th>
                    <th class="col-md-1 sort desc @if($sortColumn=='itemCode') {{ $sortDirection }}   @endif" wire:click="sort('itemCode')" >Item Name </th>
                    <th class="col-md sort desc @if($sortColumn=='timbangin') {{ $sortDirection }}   @endif" wire:click="sort('timbangin')" >Bobot IN </th>
                    <th class="col-md sort desc @if($sortColumn=='timbangout') {{ $sortDirection }}   @endif" wire:click="sort('timbangout')" >Bobot OUT </th>
                    <th class="col-md sort desc @if($sortColumn=='netto') {{ $sortDirection }}   @endif" wire:click="sort('netto')" >Netto </th>
                    <th class="col-md sort desc @if($sortColumn=='jam_in') {{ $sortDirection }}   @endif" wire:click="sort('jam_in')" >Date IN </th>
                    <th class="col-md sort desc @if($sortColumn=='jam_out') {{ $sortDirection }}   @endif" wire:click="sort('jam_out')" >Date Out </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datascaleout as $key => $value)
                <tr>
                    {{-- <td><input type="checkbox" wire:key="{{ $value->id }}" value="{{ $value->id }}" wire:model.live="trscaleSelectedID"></td> --}}
                    <td></td>
                    <td>{{ $datascaleout->firstItem() + $key }}</td>
                    <td>{{ $value->doNo }}</td>
                    <td>{{ $value->poNo }}</td>
                    <td>{{ $value->tmTranspName }}</td>
                    <td>{{ $value->sealNo }}</td>
                    <td>{{ $value->driver }}</td>
                    <td>{{ $value->carID }}</td>
                    <td>{{ $value->custName }}</td>
                    
                    <td>{{ $value->itemName }}</td>
                    <td>{{ $value->timbangin }}</td>
                    <td>{{ $value->timbangout }}</td>
                    <td>{{ $value->netto }}</td>
                    <td>
                        @php
                        if (is_null($value->jam_in)) {
                            echo '-';
                        } else {
                            echo date('d-m-Y H:i',strtotime($value->jam_in)) ;
                        }
                            
                        @endphp
                    </td>
                    <td>
                        @php
                            if (is_null($value->jam_out)) {
                                echo '-';
                            } else {
                                echo date('d-m-Y H:i',strtotime($value->jam_out));
                            }
                            
                        @endphp
                    <td>
                        {{-- <a wire:click="edit({{ $value->id }})" class="btn btn-primary btn-sm">Timbang Keluar</a> --}}
                        {{-- <a wire:click="deleteConfirmation({{ $value->id }})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Del</a> --}}
                    </td>
                </tr>
                @endforeach
                
            </tbody> 
        </table>
    </div>
    <!-- AKHIR DATA Timbangan masuk -->
</div>
