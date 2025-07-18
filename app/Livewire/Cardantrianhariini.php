<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cardantrianhariini extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglmuat;
    public $katacust;
    
    public function render()
    {
        // dd(1);
        if ($this->katakunci !=null) {
            $antriantdy = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->whereDate('tglMuat','=', Carbon::now())->where('create_t_m_s.tmCarID','like','%' . $this->katakunci . '%')->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver','create_t_m_s.tglMuat', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'customers.custName', 'products.itemName','jenistruks.jenisTruk')->where('create_t_m_s.tmQtyKg','>',0)->paginate(10);
        // dd($dataantrian);
        } elseif ($this->katacust  !=null) {
            $antriantdy = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->whereDate('tglMuat','=', Carbon::now())->where('customers.custName','like','%' . $this->katacust . '%')->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver','create_t_m_s.tglMuat', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'customers.custName', 'products.itemName','jenistruks.jenisTruk')->where('create_t_m_s.tmQtyKg','>',0)->paginate(10);
        // dd($dataantrian);
        } else {
            $antriantdy = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->whereDate('tglMuat','=', Carbon::now())->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg','create_t_m_s.tglMuat', 'customers.custName', 'products.itemName','jenistruks.jenisTruk')->where('create_t_m_s.tmQtyKg','>',0)->paginate(10);
            
        }

        
        return view('livewire.cardantrianhariini', ['antriantdy' => $antriantdy]);
    }
}
