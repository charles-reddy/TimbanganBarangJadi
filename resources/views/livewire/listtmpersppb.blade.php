<div>
    {{-- start report --}}
    <div class="card-body">
        <div class="card-body table-responsive p-0">
            <div class="my-3 p-3 bg-body rounded shadow-sm"  >
                <h1></h1>
                <div class="mb-3 row">
                    <div class="col">
                        <div class="card m-auto   mt-3 text-white text-center bg-primary" style="max-width: 30rem;">
                            <h2>Rekap SPPB - Tiket Muat  </h2>
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-sm-4" > 
                        
                                <input type="text" id="katakunci" class="form-control mr-1 mb-3 w-50" placeholder="search SPPB ......" wire:model.live="katakunci">
                                
                            </div>
                    </div>
                            
                            

                
                    {{-- laporan sppb         --}}
                        {{ $datasppb->links() }}
                    
                    <div class="d-flex justify-content-left">
                    
                        <table class="table table-striped table-sortable w-75 p-3 ">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="col-md-0">SPPB No</th>
                                    <th class="col-md-0">Customer</th>
                                    <th class="col-md-0">SPPB Qty</th>
                                    <th class="col-md-0">SPPB Karung</th>
                                    <th class="col-md-0">Open Qty</th>
                                    <th class="col-md-0">Open Karung</th>
                                    <th class="col-md-1"  wire:click="Pilih" >Pilih </th>
                                    
                                    
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach ($datasppb as $key )
                                <tr>
                                    <td></td>
                                    <td>{{ $key->sppbNo }}</td>
                                    <td>{{ $key->custName }}</td>
                                    <td>{{ $key->sppbQtyKg }}</td>
                                    <td>{{ $key->sppbQtyKarung }}</td>
                                    <td>{{ $key->openQtyKg }}</td>
                                    <td>{{ $key->openQtyKarung }}</td>
                                    
                                    
                                    <td>
                                        <a wire:click="edit('{{ $key->id }}')" class="btn btn-warning btn-sm">Pilih</a>
                                    </td>

                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>

                        
                    </div>


                    
                </div>

                <div class="mb-3 row">
                    <div class="col">
                        <div class="card  offset-1  mt-3 text-white text-center bg-primary" style="max-width: 30rem;">
                            <h2>Sudah Timbang </h2>
                        </div>
                    </div>
                            
                            

                            <input type="text" class="form-control w-50" wire:model.live="noSppb" disabled hidden>
                       
                            


                    {{-- Laporan list dan total tiket muat --}}
                    {{ $sdhtimbang->links() }}
                    
                    <div class="d-flex justify-content-left">
                    
                        <table class="table table-striped table-sortable w-50 p-3 ">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="col-md-0">SPPB No</th>
                                    <th class="col-md-0">Tiket Muat</th>
                                    <th class="col-md-0">Netto</th>
                                    
                                    
                                    
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach ($sdhtimbang as $key )
                                <tr>
                                    <td></td>
                                    <td>{{ $key->sppbNo }}</td>
                                    <td>{{ $key->pendfNo }}</td>
                                    @if($key->type == 'FG-L')
                                        <td>{{ number_format($key->netto) }}</td>
                                    @else
                                        <td>{{ number_format($key->tmQtyKg) }}</td>
                                    @endif
                                    
                                    
                                    
                                    
                                    
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>

                        
                    </div>
                    @if($fgtype == 'FG-L')
                        <p>Total: {{ $sdhtimbangtotalliq }}</p>
                    @else
                        <p>Total: {{ $sdhtimbangtotal }}</p>
                    @endif
                    
                </div>


                <div class="mb-3 row">
                    <div class="col">
                        <div class="card offset-1   mt-3 text-white text-center bg-primary" style="max-width: 30rem;">
                            <h2>Tiket Muat belum datang </h2>
                        </div>
                    </div>
                            
                            

                            <input type="text" class="form-control w-50" wire:model.live="noSppb" disabled hidden>
                       
                            


                    {{-- Laporan list dan total tiket muat --}}
                    {{ $tidakdatang->links() }}
                    
                    <div class="d-flex justify-content-left">
                    
                        <table class="table table-striped table-sortable w-50 p-3 ">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="col-md-0">SPPB No</th>
                                    <th class="col-md-0">Tiket Muat</th>
                                    <th class="col-md-0">Qty</th>
                                    
                                    
                                    
                                    
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach ($tidakdatang as $key )
                                <tr>
                                    <td></td>
                                    <td>{{ $key->sppbNo }}</td>
                                    <td>{{ $key->pendfNo }}</td>
                                    <td>{{ number_format($key->tmQtyKg)}}</td>
                                    
                                    
                                    
                                    
                                    
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>

                        
                    </div>
                    <p>Total: {{ $tidakdatangtotal }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
