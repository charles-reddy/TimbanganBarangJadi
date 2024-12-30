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
        <div wire:poll.30s>
            {{ now() }}
        </div>
        <div class="row">
            <div class="col">
                <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Master Customer</h2>
                </div>
            </div>
        </div>
        <form>
            <div class="row">
                <div class="col">
                    
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label" >Nama Customer</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="custName" >
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="custAdd" >
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
                        <button type="button" class="btn btn-primary" name="submit" wire:click="update()"  wire:confirm="Yakin simpan data?"  >UPDATE</button>
                    @endif
                        <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                    
                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->


    <!-- START DATA Timbangan masuk-->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1>Data Timbang Masuk</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ..." wire:model.live="katakunci">
        </div>

        

        {{ $mcustomer->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md-4">No</th>
                    <th class="col-md-4 sort @if($sortColumn=='driver') {{ $sortDirection }}   @endif" wire:click="sort('driver')" >Nama Customer</th>
                    <th class="col-md-4 sort desc @if($sortColumn=='carID') {{ $sortDirection }}   @endif" wire:click="sort('carID')" >Alamat</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($mcustomer as $key => $value)
                <tr>
                    <td></td>
                    <td>{{ $mcustomer->firstItem() + $key }}</td>
                    <td>{{ $value->custName }}</td>
                    <td>{{ $value->custAdd }}</td>
                    
                    
                    <td>
                        <a wire:click="edit({{ $value->custID }})" class="btn btn-primary btn-sm">Edit</a>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <!-- AKHIR DATA Timbangan masuk -->
    
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Konfirmasi Hapus Data</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            Yakin akan menghapus data ini?
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
            <button type="button" class="btn btn-primary" wire:click="delete()" data-bs-dismiss="modal">Ya</button>
            </div>
        </div>
        </div>
    </div>
</div>
