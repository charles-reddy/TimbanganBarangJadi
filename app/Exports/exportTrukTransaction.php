<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class exportTrukTransaction implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $katakunci;
    protected $tglout1;
    protected $tglout2;
    public $spmNo;
    public $buktiPGI;
    public $listkarung;
    protected $katacust;

    function __construct($tglout1, $tglout2, $katakunci, $katacust)
    {
        $this->tglout1 = $tglout1;
        $this->tglout2 = $tglout2;
        $this->katakunci = $katakunci;
        $this->katacust = $katacust;
    }


    public function collection()
    {
        $baseQuery = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
            ->join('products', 'products.itemCode', 'trscale.itemCode')
            ->join('customers', 'customers.custID', 'trscale.custID')
            ->whereNotNull('trscale.netto');

        if ($this->katakunci != null) {
            $hasil = $baseQuery
                ->where(function ($query) {
                    $query->where('createspms.carID', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createspms.dnNo', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createsppbs.sppbNo', 'like', '%' . $this->katakunci . '%');
                })
                ->orderBy('trscale.id', 'desc')
                ->select(
                    'create_t_m_s.isSecCekDate',
                    'trscale.jam_in as tgl_tim_in',
                    'trscale.jam_out as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'customers.custName',
                    'products.itemName',
                    'products.type',
                    'createspms.carID',
                    'createspms.driver',
                    'trscale.timbangin',
                    'trscale.timbangout',
                    'trscale.netto',
                    'trscale.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale.avgkarung as avgKarung',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm")
                )
                ->get();
        } elseif ($this->katacust != null) {
            $hasil = $baseQuery
                ->where(function ($query) {
                    $query->where('customers.custName', 'like', '%' . $this->katacust . '%')
                        ->orWhere('createspms.dnNo', 'like', '%' . $this->katacust . '%');
                })
                ->orderBy('trscale.id', 'desc')
                ->select(
                    'create_t_m_s.isSecCekDate',
                    'trscale.jam_in as tgl_tim_in',
                    'trscale.jam_out as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'customers.custName',
                    'products.itemName',
                    'products.type',
                    'createspms.carID',
                    'createspms.driver',
                    'trscale.timbangin',
                    'trscale.timbangout',
                    'trscale.netto',
                    'trscale.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale.avgkarung as avgKarung',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm")
                )
                ->get();
        } elseif ($this->tglout1 != null) {
            $hasil = $baseQuery
                ->whereBetween('trscale.jam_out', [$this->tglout1, $this->tglout2])
                ->orderBy('trscale.id', 'desc')
                ->select(
                    'create_t_m_s.isSecCekDate',
                    'trscale.jam_in as tgl_tim_in',
                    'trscale.jam_out as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'customers.custName',
                    'products.itemName',
                    'products.type',
                    'createspms.carID',
                    'createspms.driver',
                    'trscale.timbangin',
                    'trscale.timbangout',
                    'trscale.netto',
                    'trscale.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale.avgkarung as avgKarung',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm")
                )
                ->get();
        } else {
            $hasil = $baseQuery
                ->whereBetween('trscale.jam_out', [Carbon::now()->addDays(-14), Carbon::now()])
                ->orderBy('trscale.id', 'desc')
                ->select(
                    'create_t_m_s.isSecCekDate',
                    'trscale.jam_in as tgl_tim_in',
                    'trscale.jam_out as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'customers.custName',
                    'products.itemName',
                    'products.type',
                    'createspms.carID',
                    'createspms.driver',
                    'trscale.timbangin',
                    'trscale.timbangout',
                    'trscale.netto',
                    'trscale.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale.avgkarung as avgKarung',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm")
                )
                ->get();
        }
        
        return $hasil;
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Tgl Registrasi',
            'Tgl Timbang Masuk',
            'Tgl Timbang Keluar',
            'SPPB',
            'SPM',
            'Tiket Muat',
            'Customer',
            'Item',
            'Tipe',
            'Plat No ',
            'Sopir',
            'Berat Masuk',
            'Gross',
            'Netto',
            'qty Karung',
            'No DN',
            'Rata-Rata Karung',
            'Shift Tiket Muat',
        ];
    }
}
