<div>
    <!-- START data out-->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h1> Truk Sedang Loading</h1>
        @include('livewire.partials.dashboard-card-filters', [
            'dateId' => 'tanggal',
            'dateLabel' => 'Tgl Timbang Masuk',
            'dateModel' => 'tanggal',
        ])
        <div class="card-body table-responsive p-0">
            {{ $dataloading->links() }}
            <table class="table table-striped table-sortable">
                <thead>
                    <tr>
                        <th></th>
                        <th class="col-md">No</th>
                        <th class="col-md">SPM</th>
                        <th class="col-md">SO/SPPB</th>
                        <th class="col-md">PO</th>
                        <th class="col-md">Seal No</th>
                        <th class="col-md">Driver</th>
                        <th class="col-md">Car ID</th>
                        <th class="col-md">Customer</th>
                        <th class="col-md">Item </th>
                        <th class="col-md">Truck Type </th>
                        <th class="col-md">Date In </th>
                        <th class="col-md">Car</th>





                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataloading as $key => $value)
                        <tr>
                            <td></td>
                            <td>{{ $dataloading->firstItem() + $key }}</td>
                            <td>{{ $value->spmNo }}</td>
                            <td>{{ $value->sppbNo }}</td>
                            <td>{{ $value->poNo }}</td>
                            <td>{{ $value->sealNo }}</td>
                            <td>{{ $value->driver }}</td>
                            <td>{{ $value->carID }}</td>
                            <td>{{ $value->custName }}</td>
                            <td>{{ $value->itemName }}</td>
                            <td>{{ $value->jenisTruk }}</td>
                            <td>{{ date('d-m-Y H:i', strtotime($value->jam_in)) }}</td>
                            <td>{{ number_format($value->timbangin) }}</td>



                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <!-- AKHIR data loading -->
</div>
