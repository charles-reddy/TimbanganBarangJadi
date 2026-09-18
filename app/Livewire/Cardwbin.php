<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Cardwbin extends Component
{
    use WithPagination;
    public $katakunci;
    public $katacust;
    public $katasppb;
    public $kataproduct = [];
    #[Url]
    public $tanggal;

    public function render()
    {
        $tanggal = $this->tanggal ?: Carbon::now()->format('Y-m-d');

        // Query untuk single product (trscale)
        $singleQuery = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->leftJoin('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
            ->join('products', 'products.itemCode', 'trscale.itemCode')
            ->join('customers', 'customers.custID', 'trscale.custID')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as spmID',
                'createspms.sealNo1',
                'createspms.driver',
                'createspms.carID',
                'createspms.spmNo',
                'createsppbs.sppbNo',
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
            ->whereDate('jam_in', '=', $tanggal)
            ->whereNotNull('timbangin')
            ->whereNull('trscale.isLoading');

        // Query untuk multi product (trscale_headers & trscale_details)
        // Menampilkan per detail transaksi
        $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
            ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
            ->leftJoin('createspms', 'createspms.id', '=', 'trscale_details.spm_id')
            ->leftJoin('createsppbs', 'createsppbs.id', '=', 'trscale_details.sppb_id')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'createspms.spmJenisTruk')
            ->select(
                'trscale_details.spm_id as spmID',
                'createspms.sealNo1',
                'trscale_headers.driver',
                'trscale_headers.carID',
                'createspms.spmNo',
                'createsppbs.sppbNo',
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
            ->whereDate('trscale_headers.weigh_in_time', '=', $tanggal)
            ->whereNotNull('trscale_headers.tare_weight')
            ->whereNull('trscale_details.isLoading');

        // Apply filters based on conditions
        if ($this->katakunci != null) {
            $singleQuery->where('trscale.carID', 'like', '%' . $this->katakunci . '%');
            $multiQuery->where('trscale_headers.carID', 'like', '%' . $this->katakunci . '%');
        }
        if ($this->katacust) {
            $singleQuery->where('customers.custName', 'like', '%' . $this->katacust . '%');
            $multiQuery->where('trscale_headers.custName', 'like', '%' . $this->katacust . '%');
        }
        if ($this->katasppb) {
            $singleQuery->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
            $multiQuery->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
        }
        if (!empty($this->kataproduct)) {
            $singleQuery->whereIn('products.itemCode', $this->kataproduct);
            $multiQuery->whereIn('trscale_details.itemCode', $this->kataproduct);
        }

        // Combine queries
        $datain = $singleQuery
            ->unionAll($multiQuery)
            ->orderBy('jam_in', 'desc')
            ->paginate(10);

        $products = DB::connection('sqlsrv')->table('products')->select('itemCode', 'itemName')->where('type', '!=', 'NFG')->orderBy('itemName')->get();

        return view('livewire.cardwbin', ['datain' => $datain, 'products' => $products]);
    }
}
