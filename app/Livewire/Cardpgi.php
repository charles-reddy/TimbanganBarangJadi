<?php

namespace App\Livewire;

use App\Exports\exportCardPgi;
use App\Exports\ExportTimbangOut;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Cardpgi extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglout;
    public $spmNo;
    public $buktiPGI;
    public $listkarung;

    public function edit($id)
    {
        // dd($id);
        $data = DB::connection('sqlsrv')->table('createspms')->where('id', $id)->first();
        // dd($data->buktiPGI);
        $this->spmNo = $data->spmNo;
        $this->buktiPGI = '/storage/' . $data->buktiPGI;
        // dd($this->buktiPGI);
        
       
    }

     public function export_out()
    {
        
        return Excel::download(new exportCardPgi($this->tglout, $this->katakunci), "lappgiexport.xlsx");
    } 


     public function clear()
    {
        
        redirect('/cardpgi');
    }

    public function edit1($id)
    {
        // dd($id);
        $data = DB::connection('sqlsrv')->table('logAppAvgKarung')->where('trscaleID', $id)->get();
        // dd($data);
        $listavg = [];
        foreach ($data as  $value) {
            $listavg[] = number_format($value->avgKarung,2) ;
        }
        

        $this->listkarung = implode(", ",$listavg);
       
    }

    public function render()
    {
        $tglout = DB::connection('sqlsrv')->table('trscale')->whereNotNull('netto')->orderBy('id','desc')->first();
        if ($this->katakunci !=null) {
            // $datapgi = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNotNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->where('trscale.carID','like','%' . $this->katakunci . '%')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI' )->orderBy('createspms.id','desc')->paginate(10);
            $datapgi = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->where('trscale.carID','like','%' . $this->katakunci . '%')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI', 'createspms.dnNo',  'trscale.b10QtyKarung' )->orderBy('createspms.id','desc')->paginate(10);
        
        } elseif (($this->tglout  )  !=null) {
            // $datapgi = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->wheredate('jam_out','=',$this->tglout)->whereNotNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI' )->orderBy('createspms.id','desc')->paginate(10);
            $datapgi = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->wheredate('jam_out','=',$this->tglout)->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI', 'createspms.dnNo',  'trscale.b10QtyKarung' )->orderBy('createspms.id','desc')->paginate(10);
        
        } else {
        $this->tglout = $tglout->jam_out;
            // $datapgi = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->wheredate('jam_out','=',$this->tglout)->whereNotNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI' )->orderBy('createspms.id','desc')->paginate(10);
            $datapgi = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->wheredate('jam_out','=',$this->tglout)->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI', 'createspms.dnNo',  'trscale.b10QtyKarung' )->orderBy('createspms.id','desc')->paginate(10);
        }
        // dd($datapgi);
        return view('livewire.cardpgi',['datapgi' => $datapgi]);
    }
}
