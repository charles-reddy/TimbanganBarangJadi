<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportTimbangOutmaterial implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $tglin;
    protected $katakunciout;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'asc';
    protected $productFilter;


    function __construct($tglin, $katakunciout, $sortColumn = 'jam_in', $sortDirection = 'asc', $productFilter = null)
    {
        $this->tglin = $tglin;
        $this->katakunciout = $katakunciout;
        $this->sortColumn = $sortColumn;
        $this->sortDirection = $sortDirection;
        $this->productFilter = $productFilter;
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
    }

    public function collection()
    {
        $tglawal = date('d-m-Y', strtotime(Carbon::now()->subDay(4)));


        if (($this->katakunciout)  != null) {

            //  dd('satu');   
            $hasil = DB::connection('sqlsrv')->table('trscaleb19s')
                ->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')
                ->join('products', 'products.itemCode', 'trscaleb19s.itemCode')
                ->where(function ($query) {
                    $query->where('driver', 'like', '%' . $this->katakunciout . '%')
                        ->orWhere('carID', 'like', '%' . $this->katakunciout . '%');
                })
                ->whereNotNull('netto')
                ->wheredate('jam_in', '>=', $this->tglin)
                ->when($this->productFilter, function ($query) {
                    $query->where('products.itemName', 'like', '%' . $this->productFilter . '%');
                })
                ->orderby($this->sortColumn, $this->sortDirection)
                ->get();
        } elseif (($this->tglin)  != null) {
            // dd('dua'); 
            $hasil = DB::connection('sqlsrv')->table('trscaleb19s')
                ->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')
                ->join('products', 'products.itemCode', 'trscaleb19s.itemCode')
                ->wheredate('jam_in', '>=', $this->tglin)
                ->whereNotNull('netto')
                ->when($this->productFilter, function ($query) {
                    $query->where('products.itemName', 'like', '%' . $this->productFilter . '%');
                })
                ->orderby($this->sortColumn, $this->sortDirection)
                ->get();
        } else {

            $this->tglin = $tglawal;
            $hasil = DB::connection('sqlsrv')->table('trscaleb19s')
                ->join('suppliers', 'suppliers.suppID', 'trscaleb19s.suppID')
                ->join('products', 'products.itemCode', 'trscaleb19s.itemCode')
                ->wheredate('jam_in', '>=', $this->tglin)
                ->whereNotNull('netto')
                ->when($this->productFilter, function ($query) {
                    $query->where('products.itemName', 'like', '%' . $this->productFilter . '%');
                })
                ->orderby($this->sortColumn, $this->sortDirection)
                ->get();
        }
        return $hasil;
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'ID Transaksi',
            'Driver',
            'Car ID',
            'Supplier',
            'Item Name',
            'Bobot IN',
            'Bobot OUT',
            'Netto',
            'Date IN',
            'Date OUT',

        ];
    }

    public function map($hasil): array
    {

        return [
            $hasil->id,
            $hasil->driver,
            $hasil->carID,
            $hasil->suppName,
            $hasil->itemName,
            $hasil->timbangin,
            $hasil->timbangout,
            $hasil->netto,
            date('d-m-Y H:i:s', strtotime($hasil->jam_in)),
            date('d-m-Y H:i:s', strtotime($hasil->jam_out)),









        ];
    }
}
