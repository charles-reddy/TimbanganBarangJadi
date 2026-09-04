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
    public $tglinFrom;
    public $tglinTo;
    public $tgloutFrom;
    public $tgloutTo;
    public $productFilter;

    // Reset pagination when filters change
    public function updatedKatakunciout()
    {
        $this->resetPage();
    }

    public function updatedTglinFrom()
    {
        $this->resetPage();
    }

    public function updatedTglinTo()
    {
        $this->resetPage();
    }

    public function updatedTgloutFrom()
    {
        $this->resetPage();
    }

    public function updatedTgloutTo()
    {
        $this->resetPage();
    }

    public function updatedProductFilter()
    {
        $this->resetPage();
    }

    public function export_out()
    {
        return Excel::download(new ExportTimbangOutmaterial(
            $this->tglinFrom,
            $this->tglinTo,
            $this->tgloutFrom,
            $this->tgloutTo,
            $this->katakunciout,
            $this->sortColumn,
            $this->sortDirection,
            $this->productFilter
        ), "timbanganmaterialexport.xlsx");
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
    }

    public function clear()
    {
        $this->katakunciout = '';
        $this->tglinFrom = '';
        $this->tglinTo = '';
        $this->tgloutFrom = '';
        $this->tgloutTo = '';
        $this->productFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        if (empty($this->tglinFrom) && empty($this->tglinTo) && empty($this->tgloutFrom) && empty($this->tgloutTo)) {
            $latestIn = DB::connection('sqlsrv')->table('trscaleb19s')
                ->whereNotNull('netto')
                ->max('jam_in');

            $latestDate = $latestIn ? Carbon::parse($latestIn) : Carbon::now();
            $this->tglinFrom = $latestDate->copy()->subDays(4)->format('Y-m-d');
            $this->tglinTo = $latestDate->format('Y-m-d');
        }

        $sdhout = DB::connection('sqlsrv')->table('trscaleb19s')
            ->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')
            ->join('products', 'products.itemCode', 'trscaleb19s.itemCode')
            ->whereNotNull('netto')
            ->when($this->tglinFrom, function ($query) {
                $query->whereDate('jam_in', '>=', $this->tglinFrom);
            })
            ->when($this->tglinTo, function ($query) {
                $query->whereDate('jam_in', '<=', $this->tglinTo);
            })
            ->when($this->tgloutFrom, function ($query) {
                $query->whereDate('jam_out', '>=', $this->tgloutFrom);
            })
            ->when($this->tgloutTo, function ($query) {
                $query->whereDate('jam_out', '<=', $this->tgloutTo);
            })
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
            ->paginate(50);


        $timbangan = JembatanTimbang::all();
        $pelanggan = supplier::all();
        $angkutan = Transporter::all();
        $barang = Product::all();



        return view('livewire.laporantimbanganmaterial', ['datascaleout' => $sdhout, 'customer' => $pelanggan, 'transporter' => $angkutan, 'product' => $barang, 'timbangan' => $timbangan]);
    }
}
