<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Cardabnormal extends Component
{
    use WithPagination;
    public $katakunci;
    public $katacust;
    public $katasppb;
    public $kataproduct = [];
    #[Url]
    public $tglout;

    public function render()
    {
        $tglout = DB::connection('sqlsrv')->table('trscale')->where('isApp', 1)->whereNotNull('netto')->orderBy('id', 'desc')->first();
        if (!$this->tglout) {
            $this->tglout = $tglout->jam_out;
        }

        $dataabnormal = DB::connection('sqlsrv')->table('trscale')
            ->join('createspms', 'createspms.id', 'trscale.spmID')
            ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
            ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
            ->join('products', 'products.itemCode', 'trscale.itemCode')
            ->join('customers', 'customers.custID', 'trscale.custID')
            ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
            ->where('isApp', 1)
            ->whereDate('jam_out', $this->tglout)
            ->whereNotNull('timbangout');

        if ($this->katakunci) {
            $dataabnormal->where('trscale.carID', 'like', '%' . $this->katakunci . '%');
        }
        if ($this->katacust) {
            $dataabnormal->where('customers.custName', 'like', '%' . $this->katacust . '%');
        }
        if ($this->katasppb) {
            $dataabnormal->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
        }
        if (!empty($this->kataproduct)) {
            $dataabnormal->whereIn('products.itemCode', $this->kataproduct);
        }

        $dataabnormal = $dataabnormal->select('createspms.id as spmID', 'createspms.sealNo1', 'createspms.driver', 'createspms.carID', 'createspms.spmNo', 'products.itemName', 'customers.custName', 'jenistruks.jenisTruk', 'trscale.jam_in', 'products.type', 'trscale.id as trsID', 'trscale.jam_out', 'trscale.timbangin', 'trscale.timbangout', 'trscale.netto', 'trscale.avgkarung', 'createspms.sealNo', 'createsppbs.sppbNo', 'create_t_m_s.pendfNo')->paginate(10);
        $products = DB::connection('sqlsrv')->table('products')->select('itemCode', 'itemName')->where('type', '!=', 'NFG')->orderBy('itemName')->get();

        return view('livewire.cardabnormal', ['dataabnormal' => $dataabnormal, 'products' => $products]);
    }
}
