<div>
    <div class="container-fluid px-3 px-md-4">

        <!-- Cards Section -->
        <div class="row g-3 mb-4">
            <!-- Antrian Besok -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-warning h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">
                            {{ $antrianbsk->antrian ?? '0' }}
                        </h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardantrianbesok" class="text-white text-decoration-none">
                                Antrian Besok
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Antrian Hari Ini -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-info h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">
                            {{ $antrianskr->antrian ?? '0' }}
                        </h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardantrianhariini" class="text-white text-decoration-none">
                                Antrian Hari Ini
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Sudah Masuk -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-success h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">{{ $tmsdhmasuk }}</h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardtmsdhmasuk" class="text-white text-decoration-none">
                                Sdh Masuk
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Registrasi -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-secondary h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">{{ $registered }}</h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardregistered" class="text-white text-decoration-none">
                                Registrasi
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Timbang Masuk -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-primary h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">{{ $datafgtruk->timIn }}</h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardwbin" class="text-white text-decoration-none">
                                Timb. Masuk
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Sedang Muat -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-info h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">{{ $datafgtruk->loading }}</h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardloading" class="text-white text-decoration-none">
                                Sedang Muat
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Avg Abnormal -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-danger h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">{{ $datafgtruk->appavg }}</h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardabnormal" class="text-white text-decoration-none">
                                Avg Abnormal
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Timbang Keluar -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-success h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">{{ $datafgtruk->timout }}</h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardwbout" class="text-white text-decoration-none">
                                Timb. Keluar
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Sudah PGI -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-primary h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">{{ $datafgtruk->pgi }}</h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardpgi" class="text-white text-decoration-none">
                                Sudah PGI
                            </a>
                        </h6>
                    </div>
                </div>
            </div>

            <!-- Tunda Kemarin -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card bg-danger h-100">
                    <div class="card-body text-center p-3">
                        <h2 class="card-title mb-1">{{ $pendingkmr }}</h2>
                        <h6 class="card-text mb-0">
                            <a href="/cardpending" class="text-white text-decoration-none">
                                Tunda Kemarin
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Summary & Chart Section -->
        <div class="row g-3 mb-4">
            <!-- Delivery Summary -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">Delivery Summary -
                            <span wire:poll.60s>{{ $datafgtruk->tgl }}</span>
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-3"><strong>Item Name</strong></th>
                                        <th class="py-3 px-2 text-center"><strong>Truck(s)</strong></th>
                                        <th class="py-3 px-2 text-end"><strong>MT</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-3">GKP - Kemasan 65 Gram</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKP65 }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKP65 ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">GKP - Kemasan 500 Gram</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKP500g }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKP500g ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">GKP - Kemasan 1 Kg</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKP1kg }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKP1kg ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">GKP - Kemasan 50 Kg</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKP50Kg }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKP50Kg ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">GKP - BULK Unpacking</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKPbulk }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKPbulk ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">GKP - Molases</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKPMol }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKPMol ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">GKR - Premium 50Kg</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKR50Kg }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKR50Kg ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">GKR - BULK Unpacking</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKRbulk }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKRbulk ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">GKR - Molases</td>
                                        <td class="px-2 text-center">{{ $datafgtruk->trukGKRMol }}</td>
                                        <td class="px-2 text-end">
                                            {{ number_format(($datafgtruk->netGKRMol ?? 0) / 1000, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7 Days Delivery Chart -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body p-2">
                        <canvas id="myChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Details & Truck Chart Section -->
        <div class="row g-3 mb-4">
            <!-- Delivery Details -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">Delivery Details (Today)</h4>
                    </div>
                    <div class="card-body p-2">
                        {{ $datatrukout->links() }}
                        <div class="table-responsive">
                            <table class="table table-striped table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-2">No</th>
                                        <th class="py-2 px-2">Tiket Muat</th>
                                        <th class="py-2 px-2">SPM NO</th>
                                        <th class="py-2 px-2">Driver</th>
                                        <th class="py-2 px-2">Car ID</th>
                                        <th class="py-2 px-2 d-none d-md-table-cell">Customer</th>
                                        <th class="py-2 px-2 d-none d-lg-table-cell">Item</th>
                                        <th class="py-2 px-2">WB IN</th>
                                        <th class="py-2 px-2">WB Out</th>
                                        <th class="py-2 px-2">Netto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datatrukout as $key => $value)
                                        <tr>
                                            <td class="py-2 px-2">{{ $datatrukout->firstItem() + $key }}</td>
                                            <td class="py-2 px-2">{{ $value->pendfNo }}</td>
                                            <td class="py-2 px-2">{{ $value->spmNo }}</td>
                                            <td class="py-2 px-2">{{ $value->driver }}</td>
                                            <td class="py-2 px-2">{{ $value->carID }}</td>
                                            <td class="py-2 px-2 d-none d-md-table-cell">{{ $value->custName }}</td>
                                            <td class="py-2 px-2 d-none d-lg-table-cell">{{ $value->itemName }}</td>
                                            <td class="py-2 px-2">{{ $value->timbangin }}</td>
                                            <td class="py-2 px-2">{{ $value->timbangout }}</td>
                                            <td class="py-2 px-2">{{ $value->netto }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7 Days Truck Chart -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body p-2">
                        <canvas id="myChart1" style="max-height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Antrian 7 Hari Section -->
        <div class="card mb-4">
            <div class="card-header bg-light py-3">
                <h4 class="mb-0">Antrian 7 Hari ke Depan</h4>
            </div>
            <div class="card-body p-2">
                {{ $data7hari->links() }}
                <div class="table-responsive">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th class="py-2 px-2">No</th>
                                <th class="py-2 px-2">Tiket Muat</th>
                                <th class="py-2 px-2">SPPB No</th>
                                <th class="py-2 px-2">Driver</th>
                                <th class="py-2 px-2">Car ID</th>
                                <th class="py-2 px-2 d-none d-md-table-cell">Customer</th>
                                <th class="py-2 px-2 d-none d-lg-table-cell">Item</th>
                                <th class="py-2 px-2 d-none d-xl-table-cell">Truck Type</th>
                                <th class="py-2 px-2">Weight (KG)</th>
                                <th class="py-2 px-2">Tgl Muat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data7hari as $key => $value)
                                <tr>
                                    <td class="py-2 px-2">{{ $data7hari->firstItem() + $key }}</td>
                                    <td class="py-2 px-2">{{ $value->pendfNo }}</td>
                                    <td class="py-2 px-2">{{ $value->sppbNo }}</td>
                                    <td class="py-2 px-2">{{ $value->tmDriver }}</td>
                                    <td class="py-2 px-2">{{ $value->tmCarID }}</td>
                                    <td class="py-2 px-2 d-none d-md-table-cell">{{ $value->custName }}</td>
                                    <td class="py-2 px-2 d-none d-lg-table-cell">{{ $value->itemName }}</td>
                                    <td class="py-2 px-2 d-none d-xl-table-cell">{{ $value->jenisTruk }}</td>
                                    <td class="py-2 px-2">{{ $value->tmQtyKg }}</td>
                                    <td class="py-2 px-2">{{ $value->tglMuat }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.2/chart.min.js"
        integrity="sha512-zjlf0U0eJmSo1Le4/zcZI51ks5SjuQXkU0yOdsOBubjSmio9iCUp8XPLkEAADZNBdR9crRy3cniZ65LF2w8sRA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // Delivery Chart
        var chartData = JSON.parse(`<?php echo $transac; ?>`);
        const ctx = document.getElementById('myChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.label,
                datasets: [{
                    label: '7 DAYS DELIVERY GRAPHIC ( KG )',
                    data: chartData.data,
                    borderWidth: 1,
                    backgroundColor: ['lightgreen'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                family: 'Arial',
                                size: 14,
                                style: 'normal',
                                weight: 'bold',
                            },
                        },
                    },
                },
            }
        });

        // Truck Chart
        var chartDatajmltruk = JSON.parse(`<?php echo $jmltruk; ?>`);
        const ctxjmltruk = document.getElementById('myChart1');
        new Chart(ctxjmltruk, {
            type: 'line',
            data: {
                labels: chartDatajmltruk.label,
                datasets: [{
                    label: '7 DAYS DELIVERY GRAPHIC ( TRUCKS )',
                    data: chartDatajmltruk.data,
                    borderWidth: 3,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: ['blue', 'yellow', 'green', 'pink', 'orange', 'black', 'magenta'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                family: 'Arial',
                                size: 14,
                                style: 'normal',
                                weight: 'bold',
                            },
                        },
                    },
                },
            }
        });
    </script>
</div>
