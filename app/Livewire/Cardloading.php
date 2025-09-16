<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cardloading extends Component
{
    use WithPagination;
    public $katakunci;
    
    public function render()
    {
        if ($this->katakunci !=null) {
            $dataloading = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereDate('jam_in','=', Carbon::now() )->whereNotNull('timbangin')->wherenotNull('trscale.isLoading')->whereNull('timbangout')->whereNull('isLoadingDone')->where('trscale.carID','like','%' . $this->katakunci . '%')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto' )->paginate(10);
        } else {
            $dataloading = DB::connection('sqlsrv')->table('trscale')->join('createspms', 'createspms.id', 'trscale.spmID')->join('products', 'products.itemCode', 'trscale.itemCode')->join('customers', 'customers.custID', 'trscale.custID')->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')->whereDate('jam_in','=', Carbon::now() )->whereNotNull('timbangin')->wherenotNull('trscale.isLoading')->whereNull('timbangout')->whereNull('isLoadingDone')->select('createspms.id as spmID','createspms.sealNo1','createspms.driver','createspms.carID','createspms.spmNo','products.itemName','customers.custName','jenistruks.jenisTruk', 'trscale.jam_in','products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto' )->paginate(10);
        }
        return view('livewire.cardloading',['dataloading' => $dataloading]);
    }
}
