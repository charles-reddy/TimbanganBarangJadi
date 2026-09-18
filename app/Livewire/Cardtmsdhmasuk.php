<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;

class Cardtmsdhmasuk extends Component
{
    public $katakunci;
    public $katacust;
    public $katasppb;
    public $kataproduct = [];
    #[Url]
    public $tanggal;

    public function render()
    {
        $tanggal = $this->tanggal ?: Carbon::now()->format('Y-m-d');

        $tmsdhdatang = DB::connection('sqlsrv')->table('create_t_m_s')
            ->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')
            ->leftJoin('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->leftJoin('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->whereDate('tglMuat', $tanggal)
            ->whereNotNull('isSecCek');

        $registered = DB::connection('sqlsrv')->table('createspms')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->whereDate('tglSpm', $tanggal);

        foreach ([$tmsdhdatang, $registered] as $query) {
            if ($this->katacust) {
                $query->where('customers.custName', 'like', '%' . $this->katacust . '%');
            }
            if ($this->katasppb) {
                $query->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
            }
            if (!empty($this->kataproduct)) {
                $query->whereIn('products.itemCode', $this->kataproduct);
            }
        }
        if ($this->katakunci) {
            $tmsdhdatang->where('create_t_m_s.tmCarID', 'like', '%' . $this->katakunci . '%');
            $registered->where('createspms.carID', 'like', '%' . $this->katakunci . '%');
        }

        $tmsdhdatang = $tmsdhdatang->select('create_t_m_s.*', 'customers.custName', 'createsppbs.sppbNo as displaySppbNo')->orderBy('isSecCekDate')->paginate(50, ['*'], 'securityPage');
        $registered = $registered->select('createspms.id as spmID', 'createspms.spmNo', 'createsppbs.sppbNo as displaySppbNo', 'createspms.carID', 'createspms.driver', 'createspms.qtyKg', 'create_t_m_s.tglMuat', 'create_t_m_s.pendfNo', 'products.itemName', 'customers.custName', 'jenistruks.jenisTruk', 'isSecCekDate')->orderBy('isSecCekDate')->paginate(50, ['*'], 'registeredPage');
        $products = DB::connection('sqlsrv')->table('products')->select('itemCode', 'itemName')->where('type', '!=', 'NFG')->orderBy('itemName')->get();


        // dd($tmsdhdatang, $registered);

        return view('livewire.cardtmsdhmasuk', ['registered' => $registered, 'tmsdhdatang' => $tmsdhdatang, 'products' => $products]);
    }
}
