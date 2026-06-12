<?php

namespace App\Livewire;

use App\Exports\ExportTimbangOut;
use App\Exports\ExportTimbangOutmaterial;
use App\Models\Customer;
use App\Models\JembatanTimbang;
use App\Models\Product;
use App\Models\supplier;
use App\Models\Transporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Laporantimbanganmaterial extends Component
{
    use WithPagination;
    public $katakunciout;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'desc';
    public $trscaleSelectedID = [];
    public $tglin;
    public $productFilter;

    // Reset pagination when filters change
    public function updatedKatakunciout()
    {
        $this->resetPage();
    }

    public function updatedTglin()
    {
        $this->resetPage();
    }

    public function updatedProductFilter()
    {
        $this->resetPage();
    }

    public function export_out()
    {

        return Excel::download(new ExportTimbangOutmaterial($this->tglin, $this->katakunciout, $this->sortColumn, $this->sortDirection, $this->productFilter), "timbanganmaterialexport.xlsx");
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
    }

    public function clear()
    {
        $this->katakunciout = '';
        $this->tglin = '';
        $this->productFilter = '';
        $this->resetPage();
    }


    public function render()
    {
        $tglawal = date('m-d-Y', strtotime(Carbon::now()->subDay(4)));

        // Set default date if not set
        if (empty($this->tglin)) {
            $this->tglin = $tglawal;
        }

        // Build query with conditional filters
        $sdhout = DB::connection('sqlsrv')->table('trscaleb19s')
            ->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')
            ->join('products', 'products.itemCode', 'trscaleb19s.itemCode')
            ->whereNotNull('netto')
            ->wheredate('jam_in', '>=', $this->tglin)
            ->when($this->katakunciout, function ($query) {
                $query->where(function ($q) {
                    $q->where('driver', 'like', '%' . $this->katakunciout . '%')
                        ->orWhere('carID', 'like', '%' . $this->katakunciout . '%');
                });
            })
            ->when($this->productFilter, function ($query) {
                $query->where('products.itemName', 'like', '%' . $this->productFilter . '%');
            })
            ->orderby($this->sortColumn, $this->sortDirection)
            ->paginate(5);


        $timbangan = JembatanTimbang::all();
        $pelanggan = supplier::all();
        $angkutan = Transporter::all();
        $barang = Product::all();



        return view('livewire.laporantimbanganmaterial', ['datascaleout' => $sdhout, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]);
    }
}
