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
    protected $tglout1;
    protected $tglout2;
    public $spmNo;
    public $buktiPGI;
    public $listkarung;
    protected $katacust;

    function __construct($tglout1, $tglout2, $katakunci, $katacust)
    {
        $this->tglout1 = $tglout1;
        $this->tglout2 = $tglout2;
        $this->katakunci = $katakunci;
        $this->katacust = $katacust;
    }


    public function collection()
    {

        $tglout = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->orderBy('id', 'desc')->first();
        // dd($tglout);
        if ($this->katakunci != null) {
            // dd('katakunci');
            $hasil = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->where('carID', 'like', '%' . $this->katakunci . '%')->orWhere('dnNo', 'like', '%' . $this->katakunci . '%')->orWhere('sppbNo', 'like', '%' . $this->katakunci . '%')->orderBy('id', 'desc')->select('isSecCekDate','tgl_tim_in','tgl','sppbNo', 'spmNo', 'pendfNo', 'custName', 'itemName', 'type', 'carID', 'driver', 'timbangin', 'timbangout', 'netto', 'b10QtyKarung', 'dnNo', 'avgKarung')->get();
        } elseif (($this->katacust)  != null) {
            $hasil = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->where('custName', 'like', '%' . $this->katacust . '%')->orWhere('dnNo', 'like', '%' . $this->katacust . '%')->orderBy('id', 'desc')->select('isSecCekDate','tgl_tim_in','tgl','sppbNo', 'spmNo', 'pendfNo', 'custName', 'itemName', 'type', 'carID', 'driver', 'timbangin', 'timbangout', 'netto', 'b10QtyKarung', 'dnNo', 'avgKarung')->get();
            } elseif (($this->tglout1)  != null) {
            // dd('tgl');
            // $hasil = DB::connection('sqlsrv')->table('vw_truktransaction')->wheredate('tgl','=',$this->tglout1)->whereNotNull('netto')->orderBy('id', 'desc')->select('tgl', 'spmNo','pendfNo','custName','itemName', 'type', 'carID', 'driver','timbangin','timbangout','netto','b10QtyKarung','dnNo','avgKarung')->get();
            $hasil = DB::connection('sqlsrv')->table('vw_truktransaction')->whereBetween('tgl', [$this->tglout1, $this->tglout2])->whereNotNull('netto')->orderBy('id', 'desc')->select('isSecCekDate','tgl_tim_in','tgl','sppbNo', 'spmNo', 'pendfNo', 'custName', 'itemName', 'type', 'carID', 'driver', 'timbangin', 'timbangout', 'netto', 'b10QtyKarung', 'dnNo', 'avgKarung')->get();
        } else {
            // dd('kosong');
            $this->tglout1 = $tglout->tgl;
            // dd($tglout->tgl);
            $hasil = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->whereBetween('tgl', [Carbon::now()->addDays(-14), Carbon::now()])->orderBy('id', 'desc')->select('isSecCekDate','tgl_tim_in','tgl','sppbNo', 'spmNo', 'pendfNo', 'custName', 'itemName', 'type', 'carID', 'driver', 'timbangin', 'timbangout', 'netto', 'b10QtyKarung', 'dnNo', 'avgKarung')->get();
        }
        // dd($hasil);
        return $hasil;
    }

    public function headings(): array
    {
        //Put Here Header Name That you want in your excel sheet 
        return [
            'Tgl Registrasi',
            'Tgl Timbang Masuk',
            'Tgl Timbang Keluar',
            'SPPB',
            'SPM',
            'Tiket Muat',
            'Customer',
            'Item',
            'Tipe',
            'Plat No ',
            'Sopir',
            'Berat Masuk',
            'Gross',
            'Netto',
            'qty Karung',
            'No DN',
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
