<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportTimbangOut implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $tglin;
    protected $katakunciout;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'asc';
    

    function __construct($tglin,$katakunciout) {
            $this->tglin = $tglin;
            $this->katakunciout = $katakunciout;
            
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc'?'desc' : 'asc';
        
    }

    public function collection()
    {
        $tglawal=date('d-m-Y',strtotime(Carbon::now()->subDay(4)));
        

        if (($this->katakunciout )  !=null) {
            
            //  dd('satu');   
            $hasil = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunciout . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunciout . '%')->wheredate('jam_in','>=',$this->tglin)->orderby($this->sortColumn ,$this->sortDirection)->get();
        
            

            
        } elseif (($this->tglin  )  !=null) {
            // dd('dua'); 
            $hasil = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->wheredate('jam_in','>=',$this->tglin)->whereNotNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->get();
        } else {
             
            $this->tglin = $tglawal;
            $hasil = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->wheredate('jam_in','>=',$this->tglin)->whereNotNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->get();
        
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
                'Customer',
                'Transporter',
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
                $hasil->custName,
                $hasil->transpName,
                $hasil->itemName,
                $hasil->timbangin,
                $hasil->timbangout,
                $hasil->netto,
                date('d-m-Y H:i:s',strtotime( $hasil->jam_in)),
                date('d-m-Y H:i:s',strtotime( $hasil->jam_out)),
                
                
                


              
                
                
                
            ];
        }
}
