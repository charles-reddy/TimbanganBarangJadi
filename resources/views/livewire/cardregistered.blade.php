<div>
    <!-- START registered-->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h1> Truk Sudah Registrasi</h1>
        @include('livewire.partials.dashboard-card-filters', [
            'dateId' => 'tanggal',
            'dateLabel' => 'Tgl Muat',
            'dateModel' => 'tanggal',
        ])
        <div class="card-body table-responsive p-0">
            {{ $registered->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md">Tiket Muat</th>
                        <th class="col-md">SPM No</th>
                        <th class="col-md">SPPB</th>
                        <th class="col-md">Driver</th>
                        <th class="col-md">Car ID</th>
                        <th class="col-md">Customer</th>
                        <th class="col-md">Item </th>
                        <th class="col-md">Truck Type </th>
                        <th class="col-md">Weight </th>






                    </tr>
                </thead>
                <tbody>
                    @foreach ($registered as $key => $value)
                        <tr>
                            <td></td>
                            <td>{{ $registered->firstItem() + $key }}</td>
                            <td>{{ $value->pendfNo }}</td>
                            <td>{{ $value->spmNo }}</td>
                            <td>{{ $value->sppbNo }}</td>
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
