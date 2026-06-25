<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAllWeighing implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $dateFrom;
    protected $dateTo;
    protected $transType; // 'ALL', 'SINGLE', 'MULTI'
    protected $search;

    public function __construct($dateFrom = null, $dateTo = null, $transType = 'ALL', $search = null)
    {
        $this->dateFrom = $dateFrom ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = $dateTo ?? Carbon::now()->format('Y-m-d');
        $this->transType = $transType;
        $this->search = $search;
    }

    public function collection()
    {
        $query = DB::connection('sqlsrv')
            ->table('v_all_weighing_transactions')
            ->whereBetween(DB::raw('CAST(weigh_out_time AS DATE)'), [$this->dateFrom, $this->dateTo]);

        // Filter by transaction type
        if ($this->transType !== 'ALL') {
            $query->where('trans_type', $this->transType);
        }

        // Search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('trans_no', 'like', '%' . $this->search . '%')
                    ->orWhere('carID', 'like', '%' . $this->search . '%')
                    ->orWhere('driver', 'like', '%' . $this->search . '%')
                    ->orWhere('custName', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('weigh_out_time', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Tipe',
            'Tanggal Timbang Masuk',
            'Tanggal Timbang Keluar',
            'No. Polisi',
            'Driver',
            'Customer',
            'Transporter',
            'Produk',
            'Berat Masuk (kg)',
            'Berat Keluar (kg)',
            'Netto (kg)',
            'Berat Teoritis (kg)',
            'Faktor Koreksi (K)',
            'Status',
            'Need Approval',
            'Total Produk',
            'Total Karung',
        ];
    }

    public function map($row): array
    {
        return [
            $row->trans_no,
            $row->trans_type,
            $row->weigh_in_time ? Carbon::parse($row->weigh_in_time)->format('d-m-Y H:i') : '-',
            $row->weigh_out_time ? Carbon::parse($row->weigh_out_time)->format('d-m-Y H:i') : '-',
            $row->carID,
            $row->driver,
            $row->custName,
            $row->transpName ?? '-',
            $row->itemName ?? '-',
            number_format($row->tare_weight, 2, ',', '.'),
            number_format($row->gross_weight, 2, ',', '.'),
            number_format($row->net_weight, 2, ',', '.'),
            $row->theoretical_weight ? number_format($row->theoretical_weight, 2, ',', '.') : '-',
            $row->correction_factor ? number_format($row->correction_factor, 6, ',', '.') : '-',
            $row->status,
            $row->need_approval ? 'Ya' : 'Tidak',
            $row->total_products ?? 1,
            $row->total_karung ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Timbangan';
    }
}
