<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Cardloading extends Component
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
            ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
            ->join('products', 'products.itemCode', 'trscale.itemCode')
            ->join('customers', 'customers.custID', 'trscale.custID')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id as spmID',
                'createspms.sealNo1',
                'createspms.sealNo',
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
                'createsppbs.sppbNo',
                'createsppbs.poNo',
                DB::raw("'single' as trans_type")
            )
            ->whereDate('jam_in', '=', $tanggal)
            ->whereNotNull('timbangin')
            ->whereNotNull('trscale.isLoading')
            ->whereNull('timbangout')
            ->whereNull('isLoadingDone');

        // Query untuk multi product (trscale_headers)
        $multiQuery = DB::connection('sqlsrv')
            ->table(DB::raw('(
                SELECT 
                    h.id,
                    h.driver,
                    h.carID,
                    h.custName,
                    h.weigh_in_time,
                    h.weigh_out_time,
                    h.tare_weight,
                    h.gross_weight,
                    h.net_weight,
                    COUNT(DISTINCT d.itemCode) as product_count,
                    (SELECT TOP 1 spm_id FROM trscale_details WHERE header_id = h.id) as first_spmID
                FROM trscale_headers h
                LEFT JOIN trscale_details d ON d.header_id = h.id
                WHERE 
                    h.tare_weight IS NOT NULL
                    AND h.isLoading IS NOT NULL
                    AND h.gross_weight IS NULL
                    AND d.isLoadingDone IS NULL
                GROUP BY h.id, h.driver, h.carID, h.custName, h.weigh_in_time, 
                         h.weigh_out_time, h.tare_weight, h.gross_weight, h.net_weight
            ) as multi_data'))
            ->leftJoin('createspms', 'createspms.id', '=', 'multi_data.first_spmID')
            ->leftJoin('createsppbs', 'createsppbs.id', '=', 'createspms.sppbNo')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'createspms.spmJenisTruk')
            ->select(
                'multi_data.first_spmID as spmID',
                'createspms.sealNo1',
                'createspms.sealNo',
                'multi_data.driver',
                'multi_data.carID',
                'createspms.spmNo',
                DB::raw("CONCAT(multi_data.product_count, ' Products') as itemName"),
                'multi_data.custName',
                'jenistruks.jenisTruk',
                'multi_data.weigh_in_time as jam_in',
                DB::raw("'Multi' as type"),
                'multi_data.id as trsID',
                'multi_data.weigh_out_time as jam_out',
                'multi_data.tare_weight as timbangin',
                'multi_data.gross_weight as timbangout',
                'multi_data.net_weight as netto',
                'createsppbs.sppbNo',
                'createsppbs.poNo',
                DB::raw("'multi' as trans_type")
            );

        // Combine queries
        if ($this->katakunci != null) {
            $singleQuery->where('trscale.carID', 'like', '%' . $this->katakunci . '%');
            $multiQuery->where('multi_data.carID', 'like', '%' . $this->katakunci . '%');
        }
        if ($this->katacust) {
            $singleQuery->where('customers.custName', 'like', '%' . $this->katacust . '%');
            $multiQuery->where('multi_data.custName', 'like', '%' . $this->katacust . '%');
        }
        if ($this->katasppb) {
            $singleQuery->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
            $multiQuery->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
        }
        if (!empty($this->kataproduct)) {
            $singleQuery->whereIn('products.itemCode', $this->kataproduct);
            $multiQuery->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('trscale_details as filter_details')
                    ->whereColumn('filter_details.header_id', 'multi_data.id')
                    ->whereIn('filter_details.itemCode', $this->kataproduct);
            });
        }

        // Union and paginate
        $dataloading = $singleQuery
            ->unionAll($multiQuery)
            ->orderBy('jam_in', 'desc')
            ->paginate(10);

        $products = DB::connection('sqlsrv')->table('products')->select('itemCode', 'itemName')->where('type', '!=', 'NFG')->orderBy('itemName')->get();

        return view('livewire.cardloading', ['dataloading' => $dataloading, 'products' => $products]);
    }
}
