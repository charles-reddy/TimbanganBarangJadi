<div>
    <!-- START data -->
    <div class="my-3 p-3 bg-body rounded shadow-sm"  >
        <h1> Transaksi Truk</h1>
        
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
                <div class="col-sm-2">
                <label for=""></label>
                    <input type="text" class="form-control mb-3 w-100" placeholder="Search CARID / SO " wire:model.live="katakunci">
                </div>
                <div class="col-sm-2">
                <label for=""></label>
                    <input type="text" class="form-control mb-3 w-75" placeholder="Search Customer" wire:model.live="katacust">
                </div>
                <div class="col-sm-2 ms-2">
                        <label for="">Filter by Date Out From</label>
                        <input type="date" id="tglout1" class="form-control  mb-3 w-75"  wire:model.blur="tglout1" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-sm-2 ms-2">
                        <label for="">Filter by Date Out To</label>
                        <input type="date" id="tglout2" class="form-control  mb-3 w-75"  wire:model.blur="tglout2" max="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <button type="button" class="btn btn-primary" wire:click="clear()">Clear </button>
                    <button type="button" class="btn btn-primary" wire:click="export_out()">Export</button>  
                </div>
        
        </div>
        <div class="card-body table-responsive p-0">
            {{ $data->links() }}
            <table class="table table-striped table-sortable text-center">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md">Tgl Registrasi</th>
                        <th class="col-md">Tgl Timbang Masuk</th>
                        <th class="col-md" >Tgl Timbang Keluar</th>
                        <th class="col-md" >SPPB</th>
                        <th class="col-md" >SPM</th>
                        <th class="col-md" >Tiket Muat</th>
                        <th class="col-md" >Customer</th>
                        <th class="col-md" >Item</th>
                        <th class="col-md" >Tipe</th>
                        <th class="col-md" >Plat No</th>
                        <th class="col-md" >Sopir</th>
                        <th class="col-md" >Berat Masuk</th>
                        <th class="col-md" >Gross </th>
                        <th class="col-md" >Netto</th>
                        <th class="col-md" >qty Karung</th>
                        <th class="col-md" >No DN</th>
                        <th class="col-md" >Rata-Rata Karung</th>
                        <th class="col-md" >Rata-Rata OK?</th>
                        <th class="col-md" >PGI</th>
                        <th class="col-md" >SPM</th>
                        <th class="col-md" >Segel</th>
                        <th class="col-md" >DN</th>
                        <th class="col-md" >WB</th>
                        
                        
                        
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $data->firstItem() + $key }}</td>
                        <td>{{ $value->isSecCekDate }}</td>
                        <td>{{ $value->tgl_tim_in }}</td>
                        <td>{{ $value->tgl }}</td>
                        <td>{{ $value->sppbNo }}</td>
                        <td>{{ $value->spmNo }}</td>
                        <td>{{ $value->pendfNo }}</td>
                        <td>{{ $value->custName }}</td>
                        <td>{{ $value->itemName }}</td>
                        <td>{{ $value->type }}</td>
                        <td>{{ $value->carID }}</td>
                        <td>{{ $value->driver }}</td>
                        <td>{{ number_format($value->timbangin) }}</td>
                        <td>{{ number_format($value->timbangout) }}</td>
                        <td>{{ number_format($value->netto) }}</td>
                        
                        <td>{{ number_format($value->b10QtyKarung) }}</td>
                        <td>{{ $value->dnNo }}</td>
                        <td>{{ number_format($value->avgKarung,2) }}</td>
                        <td>@if ($value->isApp)
                                <h6 class="text-danger">Abnormal</h6>
                            @else
                                <h6 class="text-primary">Normal</h6>
                            @endif
                            </td>
                        
                        <td>@if ($value->buktiPGI)
                                <h6 class="text-primary">Done</h6>
                            @else
                                <h6 class="text-warning">-</h6>
                            @endif
                            </td>
                            
                        <td>
                            <a href="/cetakspm/{{ $value->spmID }} " class="btn btn-success" target="_blank" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                            </svg>
                            </a> 
                        </td>
                        <td>
                            <a href="/cetaksegel/{{ $value->spmID }} " class="btn btn-primary" target="_blank" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-lock2" viewBox="0 0 16 16">
                                    <path d="M8 5a1 1 0 0 1 1 1v1H7V6a1 1 0 0 1 1-1m2 2.076V6a2 2 0 1 0-4 0v1.076c-.54.166-1 .597-1 1.224v2.4c0 .816.781 1.3 1.5 1.3h3c.719 0 1.5-.484 1.5-1.3V8.3c0-.627-.46-1.058-1-1.224"/>
                                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1"/>
                                </svg>
                            </a> 
                        </td>
                        <td>
                            <a href="/cetaksj/{{ $value->id }} " class="btn btn-warning" target="_blank" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                                    <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z"/>
                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                </svg>
                            </a> 
                        </td>
                        <td>
                            <a href="/cetakout/{{ $value->id }} " class="btn btn-info" target="_blank" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-square" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                                </svg>
                            </a> 
                        </td>

                        
                        
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
        <div class="card-body table-responsive p-0">
    </div>
    <!-- AKHIR data  -->

</div>

<script>
    function validateTglout1() {
        const input = document.getElementById('tglout1');
        const tglout2 = document.getElementById('tglout2');
        const today = new Date().toISOString().split('T')[0];
        
        if (input.value) {
            // Validasi tanggal tidak boleh lebih dari hari ini
            if (input.value > today) {
                alert('Tanggal tidak boleh lebih dari hari ini!');
                input.value = '';
                @this.set('tglout1', null);
                return false;
            }
            
            // Validasi tanggal From tidak boleh lebih besar dari tanggal To
            if (tglout2.value && input.value > tglout2.value) {
                alert('Tanggal From tidak boleh lebih besar dari tanggal To!');
                input.value = '';
                @this.set('tglout1', null);
                return false;
            }
            
            // Validasi format tanggal (tambahan untuk browser lama)
            const datePattern = /^\d{4}-\d{2}-\d{2}$/;
            if (!datePattern.test(input.value)) {
                alert('Format tanggal tidak valid! Gunakan format YYYY-MM-DD.');
                input.value = '';
                @this.set('tglout1', null);
                return false;
            }
        }
        return true;
    }
    
    function validateTglout2() {
        const input = document.getElementById('tglout2');
        const tglout1 = document.getElementById('tglout1');
        const today = new Date().toISOString().split('T')[0];
        
        if (input.value) {
            // Validasi tanggal tidak boleh lebih dari hari ini
            if (input.value > today) {
                alert('Tanggal tidak boleh lebih dari hari ini!');
                input.value = '';
                @this.set('tglout2', null);
                return false;
            }
            
            // Validasi tanggal To tidak boleh lebih kecil dari tanggal From
            if (tglout1.value && input.value < tglout1.value) {
                alert('Tanggal To tidak boleh lebih kecil dari tanggal From!');
                input.value = '';
                @this.set('tglout2', null);
                return false;
            }
            
            // Validasi format tanggal (tambahan untuk browser lama)
            const datePattern = /^\d{4}-\d{2}-\d{2}$/;
            if (!datePattern.test(input.value)) {
                alert('Format tanggal tidak valid! Gunakan format YYYY-MM-DD.');
                input.value = '';
                @this.set('tglout2', null);
                return false;
            }
        }
        return true;
    }
    
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-alert', (event) => {
            alert(event.message);
        });
        
        // Validasi saat halaman dimuat untuk tglout1
        const tglout1Input = document.getElementById('tglout1');
        if (tglout1Input) {
            tglout1Input.addEventListener('blur', validateTglout1);
        }
        
        // Validasi saat halaman dimuat untuk tglout2
        const tglout2Input = document.getElementById('tglout2');
        if (tglout2Input) {
            tglout2Input.addEventListener('blur', validateTglout2);
        }
    });
</script>
