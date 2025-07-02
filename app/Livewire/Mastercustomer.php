<?php

namespace App\Livewire;

use App\Models\Customer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Mastercustomer extends Component
{
    protected $paginationTheme = 'bootstrap';
    #[Validate('required', message: 'Nama Customer harus diisi')]
    public $custName;
    #[Validate('required', message: 'Alamat Customer harus diisi')]
    public $custAdd;
    public $sortColumn = 'custID';
    public $sortDirection = 'desc';
    public $updateData = false;
    public $transID;
    public $katakunci;


    public function store()
    {
        
        $this->validate();
        try {
            // dd($this->tiketID, $this->sppbNo);
            DB::connection('sqlsrv')->table('customers')->insert([
                'custName' => $this->custName,
                'custAdd' => $this->custAdd,
                
            ]);

            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();
        } catch (Exception $e) {
            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }
    }

    public function update()
    {
        $this->validate();
        try {
            // dd($this->tiketID, $this->sppbNo);
            DB::connection('sqlsrv')->table('customers')->where('custID',$this->transID)->update([
                'custName' => $this->custName,
                'custAdd' => $this->custAdd,
                
            ]);

            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();
        } catch (Exception $e) {
            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }

    }
    
    public function clear()
    {
        $this->custName = '';
        $this->custAdd = '';
        
        redirect('/mastercustomer');
    }

    public function edit($id)
    {
        
        $data = Customer::where('custID',$id)->first();
        // dd($data);
        $this->custName = $data->custName;
        $this->custAdd = $data->custAdd;
        $this->updateData = true;
        $this->transID = $id;
    }
    



    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc'?'desc' : 'asc';
        
    }


    public function render()
    {

        if ($this->katakunci   !=null) {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('transporters', 'transporters.transpID', 'trscale.transpID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->wherenull('b10QtyKarung')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->wherenull('b10QtyKarung')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            $data = DB::connection('sqlsrv')->table('customers')->where('custName','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('customers')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        
            //    dd($data);
        }
        return view('livewire.mastercustomer', ['mcustomer' => $data]); 
    }
}
