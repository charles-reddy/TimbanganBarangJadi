<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class exportCardPgi implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $katakunci;
    protected $tglout;
    public $spmNo;
    public $buktiPGI;
    public $listkarung;
    public $sortColumn = 'jam_in';
    public $sortDirection = 'asc';


    function __construct($tglout,$katakunci) {
            $this->tglout = $tglout;
            $this->katakunci = $katakunci;
            
    }

    public function collection()
    {
        $tglout = DB::connection('sqlsrv')->table('trscale')->whereNotNull('netto')->orderBy('id','desc')->first();
        

        if (($this->katakunci )  !=null) { 
            $tglout=Carbon::now();
            //  dd('satu');   
            // $hasil = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->wheredate('jam_in','>=',$this->tglout)->orderby($this->sortColumn ,$this->sortDirection)->get();
            // $hasil = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNotNull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->wheredate('jam_in','>=',$this->tglout)->orderby($this->sortColumn ,$this->sortDirection)->get();
            $hasil = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->where('createspms.carID','like','%' . $this->katakunci . '%')->wheredate('jam_out','=',$tglout)->whereNotNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI', 'createspms.dnNo',  'trscale.b10QtyKarung' )->get();
         
        } elseif (($this->tglout  )  !=null) {
            // dd('dua'); 
            // $hasil = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->wheredate('jam_in','>=',$this->tglout)->whereNotNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->get();
            // $hasil = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->wheredate('jam_out','=',$this->tglout)->whereNotNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->get();
            $hasil = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->wheredate('jam_out','=',$this->tglout)->whereNotNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI', 'createspms.dnNo',  'trscale.b10QtyKarung' )->get();
        
        } else {
             
           $this->tglout = $tglout->jam_out;
            $hasil = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->wheredate('jam_out','=',$this->tglout)->whereNotNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI', 'createspms.dnNo',  'trscale.b10QtyKarung' )->get();
        
        }

        // dd($hasil);
        return $hasil;
    }


    public function headings(): array
        {
            //Put Here Header Name That you want in your excel sheet 
            return [
                'SPM',
                'No Urut Timbang',
                'SPPB',
                'Tiket Muat',
                'Sopir',
                'Plat No',
                'Customer',
                'Barang',
                'Jenis Truk',
                'Timbang Masuk',
                'Timbang Keluar',
                'Berat Kosong',
                'Berat Kotor',
                'Berat Bersih',
                'qty Karung',
                'Rata-Rata Karung',
                'No Segel',                
                'No DN',                
               
            ];
        }



    public function map($hasil): array
        {
            
            return [
                $hasil->spmNo,
                $hasil->trsID,
                $hasil->sppbNo,
                $hasil->pendfNo,
                $hasil->driver,
                $hasil->carID,
                $hasil->custName,
                $hasil->itemName,
                $hasil->jenisTruk,
                date('d-m-Y H:i:s',strtotime( $hasil->jam_in)),
                date('d-m-Y H:i:s',strtotime( $hasil->jam_out)),
                $hasil->timbangin,
                $hasil->timbangout,
                $hasil->netto,
                $hasil->b10QtyKarung,
                $hasil->avgkarung,
                $hasil->sealNo1,
                $hasil->dnNo,
                ];
        }
}
