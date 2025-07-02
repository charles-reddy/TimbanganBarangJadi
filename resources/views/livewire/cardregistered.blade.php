<div>
    <!-- START registered-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1> Truk Sudah Registrasi</h1>
        <div class="row">
                <div class="col-sm-4">
                <label for=""></label>
                    <input type="text" class="form-control mb-3 w-25" placeholder="Searching ... Plat no" wire:model.live="katakunci">
                </div>
                <!-- <div class="col-sm-2 ms-2">
                        <label for="">Filter by Date IN</label>
                        <input type="date" id="tglmuat" class="form-control  mb-3 w-50"  wire:model.live="tglmuat">
                </div> -->
        
        </div>
        <div class="card-body table-responsive p-0">
            {{ $registered->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md" >Tiket Muat</th>
                        <th class="col-md" >SPM No</th>
                        <th class="col-md" >Driver</th>
                        <th class="col-md" >Car ID</th>
                        <th class="col-md" >Customer</th>
                        <th class="col-md" >Item </th>
                        <th class="col-md" >Truck Type </th>
                        <th class="col-md" >Weight  </th>
                    
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registered as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $registered->firstItem() + $key }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->spmNo }}</td>
                        <td>{{ $value->driver }}</td>
                        <td>{{ $value->carID }}</td>
                        <td>{{ $value->custName }}</td>
                        <td>{{ $value->itemName }}</td>
                        <td>{{ $value->jenisTruk }}</td>
                        <td>{{ $value->qtyKg }}</td>
                        
                        
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>
    </div>
    <!-- AKHIR registered -->
</div>
