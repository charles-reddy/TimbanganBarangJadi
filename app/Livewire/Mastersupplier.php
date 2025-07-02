<?php

namespace App\Livewire;

use App\Models\supplier;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Mastersupplier extends Component
{
    protected $paginationTheme = 'bootstrap';
    #[Validate('required', message: 'Nama Supplier harus diisi')]
    public $suppName;
    #[Validate('required', message: 'Alamat Supplier harus diisi')]
    public $suppAdd;
    public $sortColumn = 'suppID';
    public $sortDirection = 'desc';
    public $updateData = false;
    public $transID;
    public $katakunci;

    public function store ()
    {
        $this->validate();
        try {
            // dd($this->tiketID, $this->sppbNo);
            DB::connection('sqlsrv')->table('suppliers')->insert([
                'suppName' => $this->suppName,
                'suppAdd' => $this->suppAdd,
                
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
        $this->suppName = '';
        $this->suppAdd = '';
        
        redirect('/mastersupplier');
    }

    public function edit($id)
    {
        
        $data = supplier::where('suppID',$id)->first();
        // dd($data);
        $this->suppName = $data->suppName;
        $this->suppAdd = $data->suppAdd;
        $this->updateData = true;
        $this->transID = $id;
    }

    public function update()
    {
        $this->validate();
        try {
            // dd($this->tiketID, $this->sppbNo);
            DB::connection('sqlsrv')->table('suppliers')->where('suppID',$this->transID)->update([
                'suppName' => $this->suppName,
                'suppAdd' => $this->suppAdd,
                
            ]);

            session()->flash('message', 'Data berhasil dimasukkan');
            $this->clear();
        } catch (Exception $e) {
            throw $e;
            // session()->flash('error', 'failed to store data');
            return;
        }

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
            $data = DB::connection('sqlsrv')->table('suppliers')->where('suppName','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        } else {
            $data = DB::connection('sqlsrv')->table('suppliers')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
        }
        return view('livewire.mastersupplier', ['msupplier' => $data]);
    }
}
