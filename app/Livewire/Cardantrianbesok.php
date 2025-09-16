<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cardantrianbesok extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglmuat;
    public $katacust;
    
    public function render()
    {
        if ($this->katakunci !=null) {
            $antrianbsk = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglMuat','=', Carbon::now()->addDays(+1))->where('create_t_m_s.tmQtyKg','>',0)->where('create_t_m_s.tmCarID','like','%' . $this->katakunci . '%')->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'customers.custName', 'products.itemName','jenistruks.jenisTruk', 'createsppbs.sppbNo')->orderBy('create_t_m_s.id','desc')->paginate(10);
        
        } elseif ($this->katacust  !=null) {
                $antrianbsk = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglMuat','=', Carbon::now()->addDays(+1))->where('create_t_m_s.tmQtyKg','>',0)->where('customers.custName','like','%' . $this->katacust . '%')->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'customers.custName', 'products.itemName','jenistruks.jenisTruk', 'createsppbs.sppbNo')->orderBy('create_t_m_s.id','desc')->paginate(10);
        
        } else {
            $antrianbsk = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglMuat','=', Carbon::now()->addDays(+1))->where('create_t_m_s.tmQtyKg','>',0)->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'customers.custName', 'products.itemName','jenistruks.jenisTruk', 'createsppbs.sppbNo')->orderBy('create_t_m_s.id','desc')->paginate(10);
            
        }
        // dd($antrianbsk);
        return view('livewire.cardantrianbesok', ['antrianbsk' => $antrianbsk]);
    }
}
