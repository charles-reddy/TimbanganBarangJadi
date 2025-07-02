<?php

namespace App\Livewire;

use App\Models\Trscale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Startloading extends Component
{
    use WithPagination;
    public $katakunci;
    public $driver;
    public $carID;
    public $custName;
    public $itemName;
    public $timbangin;
    #[Validate('required', message: 'Pilih kendaraan Muat dari Customer')]
    public $transID;


    public function store()
    {
        $tgl = Carbon::now();
        $userIDApp = Auth::user()->id;
        $usernameApp = Auth::user()->username;
        $this->validate();

        try {
               
            DB::connection('sqlsrv')->table('trscale')->where('id',$this->transID)->update([
                'isLoading' => 1,
                'isLoadingDate' => $tgl,
                
            ]);
            
            session()->flash('message', 'silahkan loading barang');
            $this->clear();
            redirect('/startloading');
            

        } catch (\Throwable $th) {
            
            
            session()->flash('error', 'gagal menyimpan data');
            
        }
    }


    public function clear()
    {
        redirect('/startloading');
    }

    public function edit($id)
    {   
       
        $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('id', $id)->first();
        // dd($data);
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->custName = $data->custName;
        $this->itemName = $data->itemName;
        $this->timbangin = $data->timbangin;
        $this->transID = $id;
 
    }
    
    public function render()
    {
        if ($this->katakunci !=null) {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('createspms', 'createspms.id', 'trscale.spmID')->join('products', 'products.itemCode', 'trscale.itemCode')->select('trscale.id as id','trscale.driver','trscale.carID','trscale.timbangin','trscale.jam_in','customers.custName','products.itemName')->whereNull('isLoading')->whereDate('jam_in','>', Carbon::now()->addDays(-5) )->where('trscale.carID','like','%' . $this->katakunci . '%')->orderBy('trscale.id','desc')->paginate(5);
        
        } else {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('createspms', 'createspms.id', 'trscale.spmID')->join('products', 'products.itemCode', 'trscale.itemCode')->select('trscale.id as id','trscale.driver','trscale.carID','trscale.timbangin','trscale.jam_in','customers.custName','products.itemName')->whereNull('isLoading')->whereDate('jam_in','>', Carbon::now()->addDays(-5) )->orderBy('trscale.id','desc')->paginate(5);
            //   dd($data);
        }
        return view('livewire.startloading',[ 'siaploading' => $data]);
    }
}
