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
    protected $shift;

    function __construct($tglout1, $tglout2, $katakunci, $katacust, $shift = null)
    {
        $this->tglout1 = $tglout1;
        $this->tglout2 = $tglout2;
        $this->katakunci = $katakunci;
        $this->katacust = $katacust;
        $this->shift = $shift;
    }


    public function collection()
    {
        if (!empty($this->katakunci)) {
            // Single product query
            $singleQuery = DB::connection('sqlsrv')->table('trscale')
                ->join('createspms', 'createspms.id', 'trscale.spmID')
                ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale.netto')
                ->where(function ($query) {
                    $query->where('createspms.carID', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createspms.dnNo', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createsppbs.sppbNo', 'like', '%' . $this->katakunci . '%');
                })
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
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
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("'single' as trans_type")
                );

            // Multi product query
            $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
                ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
                ->leftJoin('createspms', 'createspms.id', 'trscale_details.spm_id')
                ->leftJoin('createsppbs', 'createsppbs.id', 'trscale_details.sppb_id')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->leftJoin('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale_headers.net_weight')
                ->where(function ($query) {
                    $query->where('trscale_headers.carID', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createspms.dnNo', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createsppbs.sppbNo', 'like', '%' . $this->katakunci . '%');
                })
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '08:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '12:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '16:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
                ->select(
                    'create_t_m_s.isSecCekDate',
                    'trscale_headers.weigh_in_time as tgl_tim_in',
                    'trscale_headers.weigh_out_time as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'trscale_headers.custName',
                    DB::raw("CONCAT('[MULTI] ', trscale_details.itemName) as itemName"),
                    'trscale_details.itemType as type',
                    'trscale_headers.carID',
                    'trscale_headers.driver',
                    'trscale_headers.tare_weight as timbangin',
                    'trscale_headers.gross_weight as timbangout',
                    'trscale_details.actual_weight as netto',
                    'trscale_details.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale_details.avg_per_karung as avgKarung',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("'multi' as trans_type")
                );

            // Combine queries
            $unionQuery = $singleQuery->unionAll($multiQuery);

            $hasil = DB::connection('sqlsrv')
                ->query()
                ->fromSub($unionQuery, 'combined')
                ->orderBy('tgl', 'desc')
                ->get();
        } elseif (!empty($this->katacust)) {
            // Single product query
            $singleQuery = DB::connection('sqlsrv')->table('trscale')
                ->join('createspms', 'createspms.id', 'trscale.spmID')
                ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale.netto')
                ->where(function ($query) {
                    $query->where('customers.custName', 'like', '%' . $this->katacust . '%')
                        ->orWhere('createspms.dnNo', 'like', '%' . $this->katacust . '%');
                })
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
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
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("'single' as trans_type")
                );

            // Multi product query
            $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
                ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
                ->leftJoin('createspms', 'createspms.id', 'trscale_details.spm_id')
                ->leftJoin('createsppbs', 'createsppbs.id', 'trscale_details.sppb_id')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->leftJoin('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->leftJoin('customers', 'customers.custName', 'trscale_headers.custName')
                ->whereNotNull('trscale_headers.net_weight')
                ->where(function ($query) {
                    $query->where('trscale_headers.custName', 'like', '%' . $this->katacust . '%')
                        ->orWhere('createspms.dnNo', 'like', '%' . $this->katacust . '%');
                })
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '08:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '12:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '16:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
                ->select(
                    'create_t_m_s.isSecCekDate',
                    'trscale_headers.weigh_in_time as tgl_tim_in',
                    'trscale_headers.weigh_out_time as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'trscale_headers.custName',
                    DB::raw("CONCAT('[MULTI] ', trscale_details.itemName) as itemName"),
                    'trscale_details.itemType as type',
                    'trscale_headers.carID',
                    'trscale_headers.driver',
                    'trscale_headers.tare_weight as timbangin',
                    'trscale_headers.gross_weight as timbangout',
                    'trscale_details.actual_weight as netto',
                    'trscale_details.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale_details.avg_per_karung as avgKarung',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("'multi' as trans_type")
                );

            // Combine queries
            $unionQuery = $singleQuery->unionAll($multiQuery);

            $hasil = DB::connection('sqlsrv')
                ->query()
                ->fromSub($unionQuery, 'combined')
                ->orderBy('tgl', 'desc')
                ->get();
        } elseif (!empty($this->tglout1)) {
            // Set tglout2 ke hari ini jika null
            $tglout2 = $this->tglout2 ?? Carbon::now()->format('Y-m-d');

            // Single product query
            $singleQuery = DB::connection('sqlsrv')->table('trscale')
                ->join('createspms', 'createspms.id', 'trscale.spmID')
                ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale.netto')
                ->whereDate('trscale.jam_out', '>=', $this->tglout1)
                ->whereDate('trscale.jam_out', '<=', $tglout2)
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
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
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("'single' as trans_type")
                );

            // Multi product query
            $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
                ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
                ->leftJoin('createspms', 'createspms.id', 'trscale_details.spm_id')
                ->leftJoin('createsppbs', 'createsppbs.id', 'trscale_details.sppb_id')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->leftJoin('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale_headers.net_weight')
                ->whereDate('trscale_headers.weigh_out_time', '>=', $this->tglout1)
                ->whereDate('trscale_headers.weigh_out_time', '<=', $tglout2)
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '08:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '12:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '16:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
                ->select(
                    'create_t_m_s.isSecCekDate',
                    'trscale_headers.weigh_in_time as tgl_tim_in',
                    'trscale_headers.weigh_out_time as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'trscale_headers.custName',
                    DB::raw("CONCAT('[MULTI] ', trscale_details.itemName) as itemName"),
                    'trscale_details.itemType as type',
                    'trscale_headers.carID',
                    'trscale_headers.driver',
                    'trscale_headers.tare_weight as timbangin',
                    'trscale_headers.gross_weight as timbangout',
                    'trscale_details.actual_weight as netto',
                    'trscale_details.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale_details.avg_per_karung as avgKarung',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("'multi' as trans_type")
                );

            // Combine queries
            $unionQuery = $singleQuery->unionAll($multiQuery);

            $hasil = DB::connection('sqlsrv')
                ->query()
                ->fromSub($unionQuery, 'combined')
                ->orderBy('tgl', 'desc')
                ->get();
        } else {
            // Single product query
            $singleQuery = DB::connection('sqlsrv')->table('trscale')
                ->join('createspms', 'createspms.id', 'trscale.spmID')
                ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale.netto')
                ->whereDate('trscale.jam_out', '>=', Carbon::now()->addDays(-14))
                ->whereDate('trscale.jam_out', '<=', Carbon::now())
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
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
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("'single' as trans_type")
                );

            // Multi product query
            $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
                ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
                ->leftJoin('createspms', 'createspms.id', 'trscale_details.spm_id')
                ->leftJoin('createsppbs', 'createsppbs.id', 'trscale_details.sppb_id')
                ->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->leftJoin('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale_headers.net_weight')
                ->whereDate('trscale_headers.weigh_out_time', '>=', Carbon::now()->addDays(-14))
                ->whereDate('trscale_headers.weigh_out_time', '<=', Carbon::now())
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '08:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '12:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '16:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
                ->select(
                    'create_t_m_s.isSecCekDate',
                    'trscale_headers.weigh_in_time as tgl_tim_in',
                    'trscale_headers.weigh_out_time as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'trscale_headers.custName',
                    DB::raw("CONCAT('[MULTI] ', trscale_details.itemName) as itemName"),
                    'trscale_details.itemType as type',
                    'trscale_headers.carID',
                    'trscale_headers.driver',
                    'trscale_headers.tare_weight as timbangin',
                    'trscale_headers.gross_weight as timbangout',
                    'trscale_details.actual_weight as netto',
                    'trscale_details.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale_details.avg_per_karung as avgKarung',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("'multi' as trans_type")
                );

            // Combine queries
            $unionQuery = $singleQuery->unionAll($multiQuery);

            $hasil = DB::connection('sqlsrv')
                ->query()
                ->fromSub($unionQuery, 'combined')
                ->orderBy('tgl', 'desc')
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
            'Trans Type',
        ];
    }
}
