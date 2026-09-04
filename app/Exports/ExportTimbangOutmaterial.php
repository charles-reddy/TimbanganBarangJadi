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

    protected $tglinFrom;
    protected $tglinTo;
    protected $tgloutFrom;
    protected $tgloutTo;
    protected $katakunciout;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'asc';
    protected $productFilter;

    function __construct($tglinFrom, $tglinTo, $tgloutFrom, $tgloutTo, $katakunciout, $sortColumn = 'jam_in', $sortDirection = 'asc', $productFilter = null)
    {
        $this->tglinFrom = $tglinFrom;
        $this->tglinTo = $tglinTo;
        $this->tgloutFrom = $tgloutFrom;
        $this->tgloutTo = $tgloutTo;
        $this->katakunciout = $katakunciout;
        $this->sortColumn = $sortColumn;
        $this->sortDirection = $sortDirection;
        $this->productFilter = $productFilter;
    }

    public function collection()
    {
        if (empty($this->tglinFrom) && empty($this->tglinTo) && empty($this->tgloutFrom) && empty($this->tgloutTo)) {
            $latestIn = DB::connection('sqlsrv')->table('trscaleb19s')
                ->whereNotNull('netto')
                ->max('jam_in');

            $latestDate = $latestIn ? Carbon::parse($latestIn) : Carbon::now();
            $this->tglinFrom = $latestDate->copy()->subDays(4)->format('Y-m-d');
            $this->tglinTo = $latestDate->format('Y-m-d');
        }

        return DB::connection('sqlsrv')->table('trscaleb19s')
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
            ->get();
    }

    public function headings(): array
    {
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
            $hasil->jam_in ? date('d-m-Y H:i:s', strtotime($hasil->jam_in)) : '-',
            $hasil->jam_out ? date('d-m-Y H:i:s', strtotime($hasil->jam_out)) : '-',
        ];
    }
}
