<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cardwbin extends Component
{
    use WithPagination;
    public $katakunci;
    
    public function render()
    {
        // Query untuk single product (trscale)
        $singleQuery = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
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
                DB::raw("'single' as trans_type"),
                DB::raw("NULL as header_id")
            )
            ->whereDate('jam_in', '=', Carbon::now())
            ->whereNotNull('timbangin')
            ->whereNull('trscale.isLoading');

        // Query untuk multi product (trscale_headers & trscale_details)
        // Menampilkan per detail transaksi
        $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
            ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
            ->leftJoin('createspms', 'createspms.id', '=', 'trscale_details.spm_id')
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
                DB::raw("'multi' as trans_type"),
                'trscale_headers.id as header_id'
            )
            ->whereDate('trscale_headers.weigh_in_time', '=', Carbon::now())
            ->whereNotNull('trscale_headers.tare_weight')
            ->whereNull('trscale_details.isLoading');

        // Apply filters based on conditions
        if ($this->katakunci != null) {
            $singleQuery->where('trscale.carID', 'like', '%' . $this->katakunci . '%');
            $multiQuery->where('trscale_headers.carID', 'like', '%' . $this->katakunci . '%');
        }

        // Combine queries
        $datain = $singleQuery
            ->unionAll($multiQuery)
            ->orderBy('jam_in', 'desc')
            ->paginate(10);

        return view('livewire.cardwbin', ['datain' => $datain]);
    }
}
