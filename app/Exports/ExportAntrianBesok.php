<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportAntrianBesok implements FromCollection, WithHeadings
{
    protected $katakunci;
    protected $katacust;
    protected $katasppb;
    protected $shift;
    protected $kataproduct;
    protected $tglmuat;

    public function __construct($katakunci, $katacust, $katasppb, $shift, $kataproduct, $tglmuat)
    {
        $this->katakunci = $katakunci;
        $this->katacust = $katacust;
        $this->katasppb = $katasppb;
        $this->shift = $shift;
        $this->kataproduct = $kataproduct;
        $this->tglmuat = $tglmuat;
    }

    public function collection()
    {
        // Build the base query
        $query = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->where('create_t_m_s.tmQtyKg', '>', 0);

        // Apply date filter
        if ($this->tglmuat) {
            $query->whereDate('tglMuat', '=', $this->tglmuat);
        } else {
            $query->whereDate('tglMuat', '=', Carbon::now()->addDays(+1));
        }

        // Apply other filters conditionally
        if ($this->katakunci) {
            $query->where('create_t_m_s.tmCarID', 'like', '%' . $this->katakunci . '%');
        }

        if ($this->katacust) {
            $query->where('customers.custName', 'like', '%' . $this->katacust . '%');
        }

        if ($this->katasppb) {
            $query->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
        }

        if (!empty($this->kataproduct)) {
            $query->whereIn('products.itemCode', $this->kataproduct);
        }

        if ($this->shift) {
            $query->whereRaw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
        }

        // Select fields and return collection
        return $query->select(
            'create_t_m_s.pendfNo',
            'create_t_m_s.tglMuat',
            DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift"),
            'createsppbs.sppbNo',
            'create_t_m_s.tmDriver',
            'create_t_m_s.tmCarID',
            'customers.custName',
            'products.itemName',
            'jenistruks.jenisTruk',
            'create_t_m_s.tmQtyKg'
        )
            ->orderBy('create_t_m_s.id', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tiket Muat',
            'Tgl Muat',
            'Shift',
            'SPPB',
            'Driver',
            'Car ID',
            'Customer',
            'Product',
            'Truck Type',
            'Weight (Kg)',
        ];
    }
}
