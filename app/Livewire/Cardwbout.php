<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cardwbout extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglout;

    public function showlistavgkarung($id)
    {

        $data = DB::connection('sqlsrv')->table('logAppAvgKarung')->where('trscaleID', $id)->paginate(5);
        dd($data);
        // return view('livewire.showlistavgkarung',['data'=>$data]);
    }

    public function render()
    {
        $tglout = DB::connection('sqlsrv')->table('trscale')->whereNotNull('netto')->orderBy('id', 'desc')->first();

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
                DB::raw("'single' as trans_type"),
                DB::raw("NULL as header_id")
            )
            ->whereNotNull('netto')
            ->whereNotNull('createspms.sealNo1')
            ->whereNull('buktiPGI');

        // Query untuk multi product (trscale_headers & trscale_details)
        // Menampilkan per detail transaksi, bukan gabungan
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
                DB::raw("'multi' as trans_type"),
                'trscale_headers.id as header_id'
            )
            ->whereNotNull('trscale_headers.net_weight')
            ->whereNotNull('createspms.sealNo1')
            ->whereNull('createspms.buktiPGI');
           

        // Apply filters based on conditions
        if ($this->katakunci != null) {
            $singleQuery->where('trscale.carID', 'like', '%' . $this->katakunci . '%');
            $multiQuery->where('trscale_headers.carID', 'like', '%' . $this->katakunci . '%');
        } elseif ($this->tglout != null) {
            $singleQuery->whereDate('jam_out', '=', $this->tglout);
            // $multiQuery->whereDate('trscale_headers.weigh_out_time', '=', $this->tglout);
        } else {
            $this->tglout = $tglout->jam_out;
            $singleQuery->whereDate('jam_out', '=', $this->tglout);
            // $multiQuery->whereDate('trscale_headers.weigh_out_time', '=', $this->tglout);
        }

        // Combine queries
        $dataout = $singleQuery
            ->unionAll($multiQuery)
            ->orderBy('jam_out', 'desc')
            ->paginate(10);

        // dd($dataout);

        return view('livewire.cardwbout', ['dataout' => $dataout]);
    }
}
