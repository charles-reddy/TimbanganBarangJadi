<div>
    {{-- Error Messages --}}
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

    {{-- Success Messages --}}
    @if (session()->has('message'))
        <div class="pt-3">
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        </div>
    @endif


    {{-- Main Form --}}
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div wire:poll.30s>
            {{ now() }}
        </div>

        <div class="row mb-4">
            <div class="col">
                <div class="card m-auto mt-3 text-white text-center bg-primary" style="max-width: 18rem;">
                    <h2>Buat Quota Harian</h2>
                </div>
            </div>
        </div>

        <form>
            <div class="row">
                <div class="col">
                    @if ($updateMode == false)
                        <div class="mb-3 row" style="display: none;">
                            <label for="custID" class="col-sm-2 col-form-label">SPPB No</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control w-50" wire:model="sppbNo" autocomplete="off">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="custID" class="col-sm-2 col-form-label">SPPB No</label>
                            <div class="col-sm-10" wire:ignore>
                                <select class="js-example-basic-single w-50" id="my-sppbNo" wire:model="sppbNo">
                                    <option></option>
                                    @foreach ($datasppb as $item)
                                        <option value="{{ $item->id }}">{{ $item->sppbNo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @else
                        

                        <div class="mb-3 mt-3 row" >
                            <label for="nama" class="col-sm-2 col-form-label">SPPB No</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control w-50" wire:model="sppbNo" disabled>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">transID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="transID" disabled>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Tgl Muat</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control w-50" wire:model="tglMuat">
                        </div>
                    </div>
                    <div class="mb-3 mt-3 row">
                        <label for="nama" class="col-sm-2 col-form-label">Quota Harian (Kg)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w-50" wire:model="quotaKg" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    @if ($updateMode == false)
                        <button type="button" class="btn btn-primary" name="submit"
                            wire:click="store()">SIMPAN</button>
                    @else
                        <button type="button" class="btn btn-primary" name="submit"
                            wire:click="update()">UPDATE</button>
                    @endif
                    <button type="button" class="btn btn-secondary" name="submit" wire:click="clear()">CLEAR</button>
                </div>
            </div>
        </form>
    </div>


    <!-- START DATA -->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h1>Data Quota Harian</h1>
        <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Searching ..."
                wire:model.live="katakunci">
        </div>



        {{ $dataquota->links() }}
        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-md-0">No</th>
                    <th class="col-md-0 ">SPPB ID</th>
                    <th class="col-md-0 ">SPPB No</th>
                    <th class="col-md-0 ">Tgl Muat / quota</th>
                    <th class="col-md-0 ">Quota (KG)</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($dataquota as $key => $value)
                    <tr>
                        <td></td>
                        <td>{{ $dataquota->firstItem() + $key }}</td>
                        <td>{{ $value->id }}</td>
                        <td>{{ $value->sppbNo }}</td>
                        <td>{{ date('Y-m-d', strtotime($value->quotaTglDaftar)) }}</td>
                        <td>{{ $value->sisaQuotaKg }}</td>
                        <td>
                            <a wire:click="edit({{ $value->id }})" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                        <td>
                            <a wire:click="delete({{ $value->id }})" class="btn btn-danger btn-sm"
                                wire:confirm="Are you sure you want to delete this item?">Delete</a>
                        </td>



                    </tr>
                @endforeach

            </tbody>
        </table>




    </div>
    <!-- AKHIR DATA -->


    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

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

            $('#my-sppbNo').on('change', function(e) {
                var data = $('#my-sppbNo').select2("val");
                @this.set('sppbNo', data);
            })

            // $('#my-itemCode').on('change',function(e) {
            //     var data = $('#my-itemCode').select2("val");
            //     @this.set('itemCode',data);
            // })




        });
    </script>
