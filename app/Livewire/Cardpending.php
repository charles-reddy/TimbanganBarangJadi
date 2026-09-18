<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Cardpending extends Component
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
        $tanggal = $this->tanggal ?: Carbon::now()->addDays(-1)->format('Y-m-d');

        // $registrasikmrblmmasuk = DB::connection('sqlsrv')->table('createspms')->leftJoin('create_t_m_s', 'create_t_m_s.id','createspms.tiketID')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->whereDate('tglSpm','=', Carbon::now()->addDays(-1) )->where('isIN','=',0)->paginate(10);
        $registrasikmrblmmasuk = DB::connection('sqlsrv')->table('createspms')->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->leftJoin('products', 'products.itemCode', 'create_t_m_s.itemCode')->leftJoin('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglSpm', $tanggal)->where('isIN', 0);
        // $timbanginkmrblmkeluar = DB::connection('sqlsrv')->table('trscale')->leftJoin('createspms', 'createspms.id','trscale.spmID')->leftJoin('create_t_m_s', 'create_t_m_s.id','createspms.tiketID')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->whereDate('trscale.created_at','=', Carbon::now()->addDays(-1) )->wherenull('timbangout')->paginate(10);
        $timbanginkmrblmkeluar = DB::connection('sqlsrv')->table('trscale')->leftJoin('createspms', 'createspms.id', 'trscale.spmID')->leftJoin('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->leftJoin('products', 'products.itemCode', 'create_t_m_s.itemCode')->leftJoin('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('trscale.created_at', $tanggal)->whereNull('timbangout');
        $tidakdatang = DB::connection('sqlsrv')->table('create_t_m_s')->leftJoin('customers', 'customers.custID', 'create_t_m_s.custID')->leftJoin('products', 'products.itemCode', 'create_t_m_s.itemCode')->leftJoin('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglMuat', $tanggal)->whereNull('isSecCek');

        foreach ([$registrasikmrblmmasuk, $timbanginkmrblmkeluar, $tidakdatang] as $query) {
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
            $registrasikmrblmmasuk->where('createspms.carID', 'like', '%' . $this->katakunci . '%');
            $timbanginkmrblmkeluar->where('trscale.carID', 'like', '%' . $this->katakunci . '%');
            $tidakdatang->where('create_t_m_s.tmCarID', 'like', '%' . $this->katakunci . '%');
        }

        $registrasikmrblmmasuk = $registrasikmrblmmasuk->select('createspms.spmNo', 'createspms.driver', 'createspms.carID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglMuat', 'customers.custName', 'createsppbs.sppbNo as displaySppbNo')->paginate(20, ['*'], 'registrationPage');
        $timbanginkmrblmkeluar = $timbanginkmrblmkeluar->select('createspms.spmNo', 'createspms.driver', 'createspms.carID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglMuat', 'customers.custName', 'createsppbs.sppbNo as displaySppbNo')->paginate(20, ['*'], 'weighingPage');
        $tidakdatang = $tidakdatang->select('create_t_m_s.pendfNo', 'create_t_m_s.tmDriver', 'create_t_m_s.tmCarID', 'create_t_m_s.tglMuat', 'customers.custName', 'createsppbs.sppbNo as displaySppbNo')->paginate(20, ['*'], 'absentPage');
        $products = DB::connection('sqlsrv')->table('products')->select('itemCode', 'itemName')->where('type', '!=', 'NFG')->orderBy('itemName')->get();
        // dd($registrasikmrblmmasuk);

        return view('livewire.cardpending', ['registrasikmrblmmasuk' => $registrasikmrblmmasuk, 'timbanginkmrblmkeluar' => $timbanginkmrblmkeluar, 'tidakdatang' => $tidakdatang, 'products' => $products]);
    }
}
