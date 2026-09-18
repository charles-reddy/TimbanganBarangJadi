<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Cardregistered extends Component
{
    use WithPagination;
    public $katakunci;
    public $katacust;
    public $katasppb;
    public $kataproduct = [];
    #[Url]
    public $tanggal;
    public function render()
    {
        $tanggal = $this->tanggal ?: Carbon::now()->format('Y-m-d');


        $registered = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('createspms', 'createspms.tiketID', 'create_t_m_s.id')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')
            ->whereDate('create_t_m_s.tglMuat', $tanggal)
            ->whereNotNull('isSPM');

        if ($this->katakunci) {
            $registered->where('createspms.carID', 'like', '%' . $this->katakunci . '%');
        }
        if ($this->katacust) {
            $registered->where('customers.custName', 'like', '%' . $this->katacust . '%');
        }
        if ($this->katasppb) {
            $registered->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
        }
        if (!empty($this->kataproduct)) {
            $registered->whereIn('products.itemCode', $this->kataproduct);
        }

        $registered = $registered->select('createspms.id as spmID', 'createspms.spmNo', 'createsppbs.sppbNo', 'createspms.carID', 'createspms.driver', 'createspms.qtyKg', 'create_t_m_s.tglMuat', 'create_t_m_s.pendfNo', 'products.itemName', 'customers.custName', 'jenistruks.jenisTruk')->paginate(10);
        $products = DB::connection('sqlsrv')->table('products')->select('itemCode', 'itemName')->where('type', '!=', 'NFG')->orderBy('itemName')->get();

        return view('livewire.cardregistered', ['registered' => $registered, 'products' => $products]);
    }
}
