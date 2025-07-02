<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;


class Ttdstruktimbangmgr  extends Component 
{
    use WithPagination;
    public $transID;
    public $sppbNo;
    public $custName;
    public $spmNo;
    public $tglMuat;
    public $itemName;
    public $qtyKg;
    public $signature;
    


    public function store()
    {
        dd($this->signature);
       
    //     $this->validate();

    //     try {
               
    //         DB::connection('sqlsrv')->table('trscale')->where('id',$this->transID)->update([
    //             'isSecCek' => 1,
    //             'isSecCekDate' => 2,
                
    //         ]);
            
    //         session()->flash('message', 'Data berhasil dimasukkan');
    //         $this->clear();
    //         redirect('/appsecurity');
            

    //     } catch (\Throwable $th) {
            
            
    //         session()->flash('error', 'gagal menyimpan data');
            
    //     }
    }
    

     public function edit($id)
    {
        $data = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNull('ttd')->whereNotNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'createspms.buktiPGI', 'create_t_m_s.tglMuat' )->where('trscale.id', $id)->first();
        // dd($data, $id);
        $this->sppbNo = $data->sppbNo;
        $this->transID = $id;
        $this->custName = $data->custName;
        $this->spmNo = $data->spmNo;
        $this->tglMuat = $data->tglMuat;
        $this->itemName = $data->itemName;
        $this->qtyKg = $data->netto;
    }

     public function clear()
    {
        redirect('/ttdstruktimbangmgr');
    }

    public function render()
    {
        $data = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereNull('ttd')->whereNotNull('buktiPGI')->whereNotNull('netto')->whereNotNull('createspms.sealNo1')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo', 'create_t_m_s.tglMuat', 'createspms.buktiPGI' )->paginate(10);
        // dd($data);
        return view('livewire.ttdstruktimbangmgr', ['datasdhpgi' => $data]);
    }
}
