<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Timbang Multi Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }

        @media print {
            #print {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .container {
                width: 100%;
                max-width: 100%;
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
    </style>
</head>

<body onload="window.print()">
    <div class="container mt-2">
        <a class="btn-primary" href='/multi-product-weighing-out' id="print">Back</a>

        <div class="row text-center">
            <h3>BUKTI TIMBANG </h3>
            <h4>PT Kebun Tebu Mas</h4>
            <h4>Jl Raya Babat Jombang Km 25.5</h4>
            <h4>Ds. Lamongrejo Kec. Ngimbang - Lamongan</h4>
        </div>
        <hr>
        <br><br>

        <table>
            <tr>
                <td>
                    <h4>No Transaksi: {{ $header->trans_no }}</h4><br>
                </td>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td>
                    <h4>Jumlah Produk: {{ $details->count() }}</h4>
                </td>
            </tr>
            <tr>
                <td>
                    <h4>Tgl Masuk: {{ date('d-m-Y H:i:s', strtotime($header->weigh_in_time)) }}</h4><br>
                </td>
                <td>
                    &nbsp;
                </td>
                <td>
                    <h4>Gross: {{ number_format($header->gross_weight, 2) }} kg</h4>
                </td>
            </tr>
            <tr>
                <td>
                    <h4>Tgl Keluar:
                        {{ $header->weigh_out_time ? date('d-m-Y H:i:s', strtotime($header->weigh_out_time)) : '-' }}
                    </h4>
                </td>
                <td>
                    &nbsp;
                </td>
                <td>
                    <h4>Tare: {{ number_format($header->tare_weight, 2) }} kg</h4>
                </td>
            </tr>
            <tr>
                <td>
                    <h4>No Kendaraan: {{ $header->carID }}</h4>
                </td>
                <td>
                    &nbsp;
                </td>
                <td>
                    <h4>Netto: {{ number_format($header->net_weight, 2) }} kg</h4>
                </td>
            </tr>
            <tr>
                <td>
                    <h4>Driver: {{ $header->driver }}</h4>
                </td>
                <td>
                    &nbsp;
                </td>
                {{-- <td>
                    <h4>K Factor: {{ number_format($header->correction_factor, 4) }}</h4>
                </td> --}}
            </tr>
            <tr>
                <td>
                    <h4>Customer : {{ $header->custName }}</h4>
                </td>
                <td>
                    &nbsp;
                </td>

            </tr>
            @if ($header->remarks)
                <tr>
                    <td colspan="3">
                        <h4>Remarks: {{ $header->remarks }}</h4>
                    </td>
                </tr>
            @endif
        </table>

        <br>
        <h4>Detail Produk:</h4>
        <table class="product-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item Code</th>
                    <th>Nama Produk</th>
                    <th style="text-align: center;">Qty Karung</th>
                    {{-- <th style="text-align: right;">Theoretical (kg)</th> --}}
                    <th style="text-align: right;">Actual (kg)</th>
                    <th style="text-align: right;">Avg/Karung (kg)</th>
                    {{-- <th style="text-align: right;">Range Min</th>
                    <th style="text-align: right;">Range Max</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                    $totalRangeMin = 0;
                    $totalRangeMax = 0;
                @endphp
                @foreach ($details as $index => $detail)
                    @php
                        $rangeMinTotal = $detail->qty_karung * $detail->gross_min;
                        $rangeMaxTotal = $detail->qty_karung * $detail->gross_max;
                        $totalRangeMin += $rangeMinTotal;
                        $totalRangeMax += $rangeMaxTotal;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->itemCode }}</td>
                        <td>{{ $detail->itemName }}</td>
                        <td style="text-align: center;">{{ number_format($detail->qty_karung) }}</td>
                        {{-- <td style="text-align: right;">{{ number_format($detail->theoretical_weight, 2) }}</td> --}}
                        <td style="text-align: right;">{{ number_format($detail->actual_weight, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($detail->avg_per_karung, 2) }}</td>
                        {{-- <td style="text-align: right;">{{ number_format($detail->gross_min, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($detail->gross_max, 2) }}</td> --}}
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold;">
                    <td colspan="3" style="text-align: right;">TOTAL:</td>
                    <td style="text-align: center;">{{ number_format($details->sum('qty_karung')) }}</td>
                    {{-- <td style="text-align: right;">{{ number_format($details->sum('theoretical_weight'), 2) }}</td> --}}
                    <td style="text-align: right;">{{ number_format($details->sum('actual_weight'), 2) }}</td>
                    <td colspan="3"></td>
                </tr>
                {{-- <tr style="font-weight: bold; background-color: #e0e0e0;">
                    <td colspan="7" style="text-align: right;">TOTAL RANGE NETTO:</td>
                    <td style="text-align: right;">{{ number_format($totalRangeMin, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($totalRangeMax, 2) }}</td>
                </tr>
                <tr style="font-weight: bold; background-color: {{ $header->need_approval ? '#ffcccc' : '#ccffcc' }};">
                    <td colspan="7" style="text-align: right;">STATUS TRANSAKSI:</td>
                    <td colspan="2" style="text-align: center;">
                        @if ($header->need_approval)
                            ✗ OUT OF RANGE (Perlu Approval)
                        @else
                            ✓ IN RANGE
                        @endif
                    </td>
                </tr> --}}
            </tfoot>
        </table>

        <hr>
        <br><br>
        <table>
            <tr>
                <td>Dibuat</td>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td>Driver</td>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td>Diperiksa</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>{{ Auth::user()->name }}</td>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td>{{ $header->driver }}</td>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td>____________</td>
            </tr>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
