<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Jalan Multi Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @media print {
            #print {
                display: none;
            }
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .product-table th,
        .product-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .product-table th {
            background-color: #f0f0f0;
        }

        table tr {
            font-family: sans-serif;
            border: 0.03ch solid black;
            border-collapse: collapse;
        }

        table td {
            font-family: sans-serif;
            border: 0.03ch solid black;
            border-collapse: collapse;
            padding: 5px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container mt-2">
        <a class="btn-primary" href='/lapsj' id="print">Back</a>

        <div class="row">
            <div class="col-sm-9">
                <h6><b>NO SJ : {{ $firstDetail->spmNo ?? '-' }}
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp; Lamongan, {{ date('d-m-Y', strtotime($firstDetail->tglSpm ?? now())) }}</b></h6>
                <h6><b> NO SO : {{ $firstDetail->sppbNo ?? '-' }}
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp; {{ $header->custName ?? '-' }}</b></h6>
                <h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp; <b>{{ $header->custAdd ?? '-' }}</b></h6>
                <h5><b>SURAT JALAN </b></h5>
            </div>
        </div>

        <div>
            <div class="row">
                <div class="col-sm-5">
                    <h5><b> dengan kendaraan no. {{ $header->carID }}</b></h5>
                </div>
            </div>

            <table border="1" style="width: 100%">
                <tr>
                    <td class="w-25 text-center">
                        <h5>BANYAKNYA</h5>
                    </td>
                    <td class="text-center" colspan="2">
                        <h5>NAMA BARANG</h5>
                    </td>
                </tr>

                <tbody>
                    @foreach ($details as $detail)
                        <tr>
                            <td class="text-center w-25">
                                <h5><b>{{ number_format($detail->qty_karung) }}</b></h5>
                            </td>
                            <td class="w-25 text-center m-auto">
                                <h5><b>
                                        @if ($detail->packingID == 3)
                                            Sak
                                        @else
                                            Pcs
                                        @endif
                                    </b></h5>
                            </td>
                            <td>
                                <h5><b>&nbsp;{{ $detail->itemName }}</b></h5>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-center">
                                <h5><b>{{ number_format($detail->qtyKg) }}</b></h5>
                            </td>
                            <td class="text-center">
                                <h5><b>Kg</b></h5>
                            </td>
                            <td></td>
                        </tr>
                    @endforeach

                    <tr style="font-weight: bold; background-color: #f0f0f0;">
                        <td class="text-center">
                            <h5><b>{{ number_format($details->sum('qty_karung')) }}</b></h5>
                        </td>
                        <td class="text-center">
                            <h5><b>Total Sak</b></h5>
                        </td>
                        <td></td>
                    </tr>

                    <tr style="font-weight: bold; background-color: #f0f0f0;">
                        <td class="text-center">
                            <h5><b>{{ number_format($details->sum('qtyKg')) }}</b></h5>
                        </td>
                        <td class="text-center">
                            <h5><b>Total Kg</b></h5>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td class="w-100" style="height: 60px">
                            <h5>&nbsp;NO SEAL. : {{ $firstDetail->sealNo ?? '-' }}</h5><br>
                            <h5>&nbsp;NO SPPB. : {{ $firstDetail->sppbNo ?? '-' }}</h5><br>
                        </td>
                    </tr>
                </tbody>
            </table>

            <br>
            <table border="1" style="width: 100%" class="text-center">
                <tr>
                    <td>
                        <h4>Penerima</h4>
                        <br><br><br>
                        <h4>{{ $header->driver }}</h4>
                    </td>
                    <td>
                        <h4>Registrasi Barang Jadi</h4>
                        <br><br><br>
                        <h4>&nbsp;</h4>
                    </td>
                    <td>
                        <h4>Admin Gudang</h4>
                        <br><br><br>
                        <h4>&nbsp;</h4>
                    </td>
                </tr>
            </table>

            <h6>Note: Mohon Penerima mencantumkan nama jelas & Tgl terima barang</h6>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
