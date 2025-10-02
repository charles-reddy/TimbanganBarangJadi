<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class exportTrukTransaction implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $katakunci;
    protected $tglout;
    public $spmNo;
    public $buktiPGI;
    public $listkarung;

    function __construct($tglout,$katakunci) {
            $this->tglout = $tglout;
            $this->katakunci = $katakunci;
            
    }
  

    public function collection()
    {

         $tglout = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->orderBy('id','desc')->first();
        // dd($tglout);
        if ($this->katakunci !=null) {
            // dd('katakunci');
            $hasil = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->where('carID','like','%' . $this->katakunci . '%')->orderBy('id', 'desc')->select('tgl', 'spmNo','pendfNo','custName','itemName', 'type', 'carID', 'driver','timbangin','timbangout','netto','avgKarung')->get();
            

        } elseif (($this->tglout  )  !=null) {
            // dd('tgl');
            $hasil = DB::connection('sqlsrv')->table('vw_truktransaction')->wheredate('tgl','=',$this->tglout)->whereNotNull('netto')->orderBy('id', 'desc')->select('tgl', 'spmNo','pendfNo','custName','itemName', 'type', 'carID', 'driver','timbangin','timbangout','netto','avgKarung')->get();
        } else {
            // dd('kosong');
            $this->tglout = $tglout->tgl;
            // dd($tglout->tgl);
             $hasil = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->whereBetween('tgl',[ Carbon::now()->addDays(-14), Carbon::now() ])->orderBy('id', 'desc')->select('tgl', 'spmNo','pendfNo','custName','itemName', 'type', 'carID', 'driver','timbangin','timbangout','netto','avgKarung')->get();
       
        }
        // dd($hasil);
        return $hasil;
        
    }

    public function headings(): array
        {
            //Put Here Header Name That you want in your excel sheet 
            return [
                        'Request No',
                        'Date Start',
                        'Date End',
                        'Destination',
                        'Number of Passenge',
                        'Tipe',
                        'Plat No',
                        'Sopir',
                        'Berat Masuk',
                        'Gross ',
                        'Netto',
                        'Rata-Rata Karung',
                        
                
            ];
        }



    public function map($hasil): array
        {
            
            return [
                        $hasil->tgl,
                        
                ];
        }
}
