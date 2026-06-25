<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\Transporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Laporansuratjalan extends Component
{
    use WithPagination;
    public $katakunciout;
    public $sortColumn = 'tglSpm';
    public $sortDirection = 'desc';
    public $trscaleSelectedID = [];
    public $tglin;

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
    }

    public function clear()
    {
        $this->katakunciout = '';
        $this->tglin = '';
    }



    public function render()
    {
        $tglawal = date('m-d-Y', strtotime(Carbon::now()->subDay(4)));

        // Set default tglin jika belum ada
        if (!$this->tglin) {
            $this->tglin = $tglawal;
        }

        // Query untuk single product (trscale)
        $singleQuery = DB::connection('sqlsrv')->table('createspms')
            ->join('customers', 'customers.custID', 'createspms.custID')
            ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
            ->join('trscale', 'trscale.spmID', 'createspms.id')
            ->join('products', 'products.itemCode', 'createspms.itemCode')
            ->select(
                'createspms.id',
                'createspms.id as spmID',
                'createspms.spmNo',
                'createspms.driver',
                'createspms.carID',
                'createspms.tglSpm',
                'createspms.qtyKg',
                'createspms.qtyKarung',
                'createspms.sealNo1',
                'products.itemName',
                'customers.custName',
                'createsppbs.sppbNo',
                'trscale.jam_in',
                'trscale.jam_out',
                'trscale.timbangin',
                'trscale.timbangout',
                'trscale.netto',
                'trscale.avgkarung',
                'trscale.b10QtyKarung',
                'trscale.id as trsID',
                DB::raw("'single' as trans_type"),
                DB::raw("NULL as header_id")
            )
            ->where(function ($query) {
                $query->where('itemName', 'like', '%GKR%')
                    ->orWhere('itemName', 'like', '%Molasses%');
            });

        // Query untuk multi product (trscale_headers & trscale_details)
        $multiQuery = DB::connection('sqlsrv')->table('trscale_headers')
            ->join('trscale_details', 'trscale_details.header_id', 'trscale_headers.id')
            ->leftJoin('createspms', 'createspms.id', '=', 'trscale_details.spm_id')
            ->leftJoin('createsppbs', 'createsppbs.id', '=', 'trscale_details.sppb_id')
            ->leftJoin('customers', 'customers.custID', '=', 'trscale_headers.custID')
            ->select(
                'createspms.id',
                'trscale_details.spm_id as spmID',
                'createspms.spmNo',
                'trscale_headers.driver',
                'trscale_headers.carID',
                'createspms.tglSpm',
                'createspms.qtyKg',
                'createspms.qtyKarung',
                'createspms.sealNo1',
                DB::raw("CONCAT('[MULTI] ', trscale_details.itemName) as itemName"),
                'customers.custName',
                'createsppbs.sppbNo',
                'trscale_headers.weigh_in_time as jam_in',
                'trscale_headers.weigh_out_time as jam_out',
                'trscale_headers.tare_weight as timbangin',
                'trscale_headers.gross_weight as timbangout',
                'trscale_details.actual_weight as netto',
                'trscale_details.avg_per_karung as avgkarung',
                'trscale_details.qty_karung as b10QtyKarung',
                'trscale_details.id as trsID',
                DB::raw("'multi' as trans_type"),
                'trscale_headers.id as header_id'
            );

        // Apply filter katakunciout (search by driver atau carID)
        if ($this->katakunciout != null) {
            $singleQuery = $singleQuery->where(function ($query) {
                $query->where('createspms.driver', 'like', '%' . $this->katakunciout . '%')
                    ->orWhere('createspms.carID', 'like', '%' . $this->katakunciout . '%');
            });

            $multiQuery = $multiQuery->where(function ($query) {
                $query->where('trscale_headers.driver', 'like', '%' . $this->katakunciout . '%')
                    ->orWhere('trscale_headers.carID', 'like', '%' . $this->katakunciout . '%');
            });
        }

        // Apply filter tglin (tanggal)
        if ($this->tglin != null) {
            $singleQuery = $singleQuery->whereDate('trscale.jam_in', '>=', $this->tglin);
            $multiQuery = $multiQuery->whereDate('trscale_headers.weigh_in_time', '>=', $this->tglin);
        }

        // Gabungkan single dan multi product dengan wrapping untuk sorting
        $unionQuery = $singleQuery->unionAll($multiQuery);

        $sdhout = DB::connection('sqlsrv')->table(DB::raw("({$unionQuery->toSql()}) as combined"))
            ->mergeBindings($unionQuery)
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate(5);

        $timbangan = JembatanTimbang::all();
        $pelanggan = Customer::all();
        $angkutan = Transporter::all();
        $barang = Product::all();

        return view('livewire.laporansuratjalan', ['datascaleout' => $sdhout, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]);
    }
}
