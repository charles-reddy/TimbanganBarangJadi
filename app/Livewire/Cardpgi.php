<?php

namespace App\Livewire;

use App\Exports\exportCardPgi;
use App\Exports\ExportTimbangOut;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Cardpgi extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglout;
    public $spmNo;
    public $buktiPGI;
    public $listkarung;
    public $shift;

    public function edit($spmID, $transType = null)
    {
        // Cari data langsung dari createspms berdasarkan spmID
        // spmID untuk single = createspms.id
        // spmID untuk multi = trscale_details.spm_id (yang sama dengan createspms.id)
        $data = DB::connection('sqlsrv')->table('createspms')
            ->where('id', $spmID)
            ->select('id', 'spmNo', 'buktiPGI')
            ->first();

        if ($data) {
            $this->spmNo = $data->spmNo;
            $this->buktiPGI = '/storage/' . $data->buktiPGI;
        }
    }

    public function export_out()
    {

        return Excel::download(new exportCardPgi($this->tglout, $this->katakunci), "lappgiexport.xlsx");
    }


    public function clear()
    {

        redirect('/cardpgi');
    }

    public function edit1($id, $transType = null)
    {
        // Jika trans_type adalah 'multi', cari di logAppAvgKarung berdasarkan trscale_detail_id
        if ($transType === 'multi') {
            $data = DB::connection('sqlsrv')->table('logAppAvgKarung')
                ->where('trscale_detail_id', $id)
                ->get();
        } else {
            // Single product - cari berdasarkan trscaleID
            $data = DB::connection('sqlsrv')->table('logAppAvgKarung')
                ->where('trscaleID', $id)
                ->get();
        }

        $listavg = [];
        foreach ($data as $value) {
            $listavg[] = number_format($value->avgKarung, 2);
        }

        $this->listkarung = implode(", ", $listavg);
    }

    public function render()
    {
        $tglout = DB::connection('sqlsrv')->table('trscale')->whereNotNull('netto')->orderBy('id', 'desc')->first();

        // Set default tglout jika belum ada
        if (!$this->tglout) {
            $this->tglout = $tglout->jam_out;
        }

        // Query untuk single product (trscale)
        $singleQuery = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
            ->join('products', 'products.itemCode', 'trscale.itemCode')
            ->join('customers', 'customers.custID', 'trscale.custID')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as spmID',
                'createspms.sealNo1',
                'createspms.driver',
                'createspms.carID',
                'createspms.spmNo',
                'products.itemName',
                'customers.custName',
                'jenistruks.jenisTruk',
                'trscale.jam_in',
                'products.type',
                'trscale.id as trsID',
                'trscale.jam_out',
                'trscale.timbangin',
                'trscale.timbangout',
                'trscale.netto',
                'trscale.avgkarung',
                'createspms.sealNo',
                'createsppbs.sppbNo',
                'create_t_m_s.pendfNo',
                'createspms.buktiPGI',
                'createspms.dnNo',
                'trscale.b10QtyKarung',
                'create_t_m_s.tglDaftar',
                DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                DB::raw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_wbin"),
                DB::raw("'single' as trans_type"),
                DB::raw("NULL as header_id")
            )
            ->whereNotNull('netto')
            ->whereNotNull('createspms.sealNo1');

        // Query untuk multi product (trscale_headers & trscale_details)
        $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
            ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
            ->leftJoin('createspms', 'createspms.id', '=', 'trscale_details.spm_id')
            ->leftJoin('createsppbs', 'createsppbs.id', '=', 'trscale_details.sppb_id')
            ->leftJoin('create_t_m_s', 'create_t_m_s.id', '=', 'createspms.tiketID')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'createspms.spmJenisTruk')
            ->select(
                'trscale_details.spm_id as spmID',
                'createspms.sealNo1',
                'trscale_headers.driver',
                'trscale_headers.carID',
                'createspms.spmNo',
                DB::raw("CONCAT('[MULTI] ', trscale_details.itemName) as itemName"),
                'trscale_headers.custName',
                'jenistruks.jenisTruk',
                'trscale_headers.weigh_in_time as jam_in',
                'trscale_details.itemType as type',
                'trscale_details.id as trsID',
                'trscale_headers.weigh_out_time as jam_out',
                'trscale_headers.tare_weight as timbangin',
                'trscale_headers.gross_weight as timbangout',
                'trscale_details.actual_weight as netto',
                'trscale_details.avg_per_karung as avgkarung',
                'createspms.sealNo',
                'createsppbs.sppbNo',
                'create_t_m_s.pendfNo',
                'createspms.buktiPGI',
                'createspms.dnNo',
                'trscale_details.qty_karung as b10QtyKarung',
                'create_t_m_s.tglDaftar',
                DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                DB::raw("CASE WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '08:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '12:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '16:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_wbin"),
                DB::raw("'multi' as trans_type"),
                'trscale_headers.id as header_id'
            )
            ->whereNotNull('trscale_headers.net_weight')
            ->whereNotNull('createspms.sealNo1');
            

        // Apply filter katakunci (car ID search)
        if ($this->katakunci != null) {
            $singleQuery = $singleQuery->where('createspms.carID', 'like', '%' . $this->katakunci . '%');
            $multiQuery = $multiQuery->where('trscale_headers.carID', 'like', '%' . $this->katakunci . '%');
        }

        // Apply filter tglout (date filter)
        if ($this->tglout != null) {
            $singleQuery = $singleQuery->whereDate('jam_out', '=', $this->tglout);
            // $multiQuery = $multiQuery->whereDate('trscale_headers.weigh_out_time', '=', $this->tglout);
        }

        // Apply filter shift
        if ($this->shift) {
            $singleQuery = $singleQuery->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
            // $multiQuery = $multiQuery->whereRaw("CASE WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '08:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '12:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale_headers.weigh_in_time as TIME) >= '16:00' AND CAST(trscale_headers.weigh_in_time as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
        }

        // Gabungkan single dan multi product
        $datapgi = $singleQuery->unionAll($multiQuery)
            ->orderBy('spmID', 'desc')
            ->paginate(10);

        return view('livewire.cardpgi', ['datapgi' => $datapgi]);
    }
}
