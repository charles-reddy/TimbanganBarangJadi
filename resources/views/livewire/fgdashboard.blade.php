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

        <!-- Shift Performance Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <h5 class="mb-0">Shift Performance - {{ now()->format('d M Y') }}</h5>
            </div>

            <!-- Shift 1 -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-primary h-100">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0"><i class="bi bi-sunrise"></i> Shift 1 (08:00-12:00)</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row text-center mb-2">
                            <div class="col-6">
                                <h3 class="mb-0 text-primary">{{ $shift1->totalTruk ?? 0 }}</h3>
                                <small class="text-muted">Truk</small>
                            </div>
                            <div class="col-6">
                                <h3 class="mb-0 text-primary">{{ number_format(($shift1->totalNetto ?? 0) / 1000, 2) }}
                                </h3>
                                <small class="text-muted">MT</small>
                            </div>
                        </div>
                        @if ($quotaToday && $quotaToday->quota1)
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Target: {{ $quotaToday->quota1 }} truk</small>
                                <small
                                    class="fw-bold {{ ($shift1->totalTruk ?? 0) >= $quotaToday->quota1 ? 'text-success' : 'text-warning' }}">
                                    {{ number_format((($shift1->totalTruk ?? 0) / $quotaToday->quota1) * 100, 1) }}%
                                </small>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ ($shift1->totalTruk ?? 0) >= $quotaToday->quota1 ? 'bg-success' : 'bg-warning' }}"
                                    style="width: {{ min((($shift1->totalTruk ?? 0) / $quotaToday->quota1) * 100, 100) }}%">
                                    {{ $shift1->totalTruk ?? 0 }}/{{ $quotaToday->quota1 }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shift 2 -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-success h-100">
                    <div class="card-header bg-success text-white py-2">
                        <h6 class="mb-0"><i class="bi bi-sun"></i> Shift 2 (12:00-16:00)</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row text-center mb-2">
                            <div class="col-6">
                                <h3 class="mb-0 text-success">{{ $shift2->totalTruk ?? 0 }}</h3>
                                <small class="text-muted">Truk</small>
                            </div>
                            <div class="col-6">
                                <h3 class="mb-0 text-success">
                                    {{ number_format(($shift2->totalNetto ?? 0) / 1000, 2) }}</h3>
                                <small class="text-muted">MT</small>
                            </div>
                        </div>
                        @if ($quotaToday && $quotaToday->quota2)
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Target: {{ $quotaToday->quota2 }} truk</small>
                                <small
                                    class="fw-bold {{ ($shift2->totalTruk ?? 0) >= $quotaToday->quota2 ? 'text-success' : 'text-warning' }}">
                                    {{ number_format((($shift2->totalTruk ?? 0) / $quotaToday->quota2) * 100, 1) }}%
                                </small>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ ($shift2->totalTruk ?? 0) >= $quotaToday->quota2 ? 'bg-success' : 'bg-warning' }}"
                                    style="width: {{ min((($shift2->totalTruk ?? 0) / $quotaToday->quota2) * 100, 100) }}%">
                                    {{ $shift2->totalTruk ?? 0 }}/{{ $quotaToday->quota2 }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shift 3 -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-warning h-100">
                    <div class="card-header bg-warning text-dark py-2">
                        <h6 class="mb-0"><i class="bi bi-sunset"></i> Shift 3 (16:00-20:00)</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row text-center mb-2">
                            <div class="col-6">
                                <h3 class="mb-0 text-warning">{{ $shift3->totalTruk ?? 0 }}</h3>
                                <small class="text-muted">Truk</small>
                            </div>
                            <div class="col-6">
                                <h3 class="mb-0 text-warning">
                                    {{ number_format(($shift3->totalNetto ?? 0) / 1000, 2) }}</h3>
                                <small class="text-muted">MT</small>
                            </div>
                        </div>
                        @if ($quotaToday && $quotaToday->quota3)
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Target: {{ $quotaToday->quota3 }} truk</small>
                                <small
                                    class="fw-bold {{ ($shift3->totalTruk ?? 0) >= $quotaToday->quota3 ? 'text-success' : 'text-warning' }}">
                                    {{ number_format((($shift3->totalTruk ?? 0) / $quotaToday->quota3) * 100, 1) }}%
                                </small>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ ($shift3->totalTruk ?? 0) >= $quotaToday->quota3 ? 'bg-success' : 'bg-warning' }}"
                                    style="width: {{ min((($shift3->totalTruk ?? 0) / $quotaToday->quota3) * 100, 100) }}%">
                                    {{ $shift3->totalTruk ?? 0 }}/{{ $quotaToday->quota3 }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Outside Shift -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-danger h-100">
                    <div class="card-header bg-danger text-white py-2">
                        <h6 class="mb-0"><i class="bi bi-moon-stars"></i> Luar Jam Shift</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row text-center mb-2">
                            <div class="col-6">
                                <h3 class="mb-0 text-danger">{{ $shiftOutside->totalTruk ?? 0 }}</h3>
                                <small class="text-muted">Truk</small>
                            </div>
                            <div class="col-6">
                                <h3 class="mb-0 text-danger">
                                    {{ number_format(($shiftOutside->totalNetto ?? 0) / 1000, 2) }}</h3>
                                <small class="text-muted">MT</small>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="text-center">
                            <small class="text-muted d-block"><i class="bi bi-exclamation-triangle"></i> Transaksi di
                                luar jam operasional</small>
                            <small class="text-muted">(&lt; 08:00 atau &gt;= 20:00)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shift Performance Chart Section -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white py-2">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart-fill"></i> 7 Hari Kerja - Performance Per Shift (Senin-Sabtu)
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <canvas id="shiftPerformanceChart" style="max-height: 350px;"></canvas>
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
                                        <th class="py-2 px-2">Shift</th>
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
                                            <td class="py-2 px-2">
                                                @if ($value->shift == 'Shift 1')
                                                    <span class="badge bg-primary">S1</span>
                                                @elseif($value->shift == 'Shift 2')
                                                    <span class="badge bg-success">S2</span>
                                                @elseif($value->shift == 'Shift 3')
                                                    <span class="badge bg-warning text-dark">S3</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
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

        // Shift Performance Chart (Stacked Bar)
        var shiftData = JSON.parse(`<?php echo $shiftChartData; ?>`);
        const ctxShift = document.getElementById('shiftPerformanceChart');
        new Chart(ctxShift, {
            type: 'bar',
            data: {
                labels: shiftData.labels,
                datasets: [{
                        label: 'Shift 1 (08:00-12:00)',
                        data: shiftData.shift1,
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Shift 2 (12:00-16:00)',
                        data: shiftData.shift2,
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Shift 3 (16:00-20:00)',
                        data: shiftData.shift3,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Luar Jam Shift',
                        data: shiftData.outsideShift,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Hari (Senin - Sabtu)',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Truk',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            stepSize: 5
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Arial',
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    title: {
                        display: true,
                        text: '7 Hari Kerja - Performance Per Shift',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        padding: {
                            top: 5,
                            bottom: 10
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            footer: function(tooltipItems) {
                                let total = 0;
                                tooltipItems.forEach(item => {
                                    total += item.parsed.y;
                                });
                                return 'Total: ' + total + ' truk';
                            }
                        }
                    }
                }
            }
        });
    </script>
</div>
