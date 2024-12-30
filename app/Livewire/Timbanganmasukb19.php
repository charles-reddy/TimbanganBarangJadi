<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Timbanganmasukb19 extends Component
{
    public function render()
    {
        // if ($this->katakunci !=null) {
            // $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->where('driver','like','%' . $this->katakunci . '%')->whereNull('netto')->orwhere('carID','like','%' . $this->katakunci . '%')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
          
        // } else {
            $data = DB::connection('sqlsrv')->table('trscale')->join('customers', 'customers.custID', 'trscale.custID')->join('products', 'products.itemCode', 'trscale.itemCode')->whereNull('netto')->orderby($this->sortColumn ,$this->sortDirection)->paginate(5);
            
            dd($data);
           
        // }

        return view('livewire.timbanganmasukb19'); 
    }
}
