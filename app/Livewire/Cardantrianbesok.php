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
    public $katasppb;
    public $shift;
    public $kataproduct;

    public function clear()
    {

        redirect('/cardantrianbesok');
    }

    public function render()
    {
        // Build the base query
        $query = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->whereDate('tglMuat', '=', Carbon::now()->addDays(+1))
            ->where('create_t_m_s.tmQtyKg', '>', 0);

        // Apply filters conditionally
        if ($this->katakunci) {
            $query->where('create_t_m_s.tmCarID', 'like', '%' . $this->katakunci . '%');
        }

        if ($this->katacust) {
            $query->where('customers.custName', 'like', '%' . $this->katacust . '%');
        }

        if ($this->katasppb) {
            $query->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%');
        }

        if ($this->kataproduct) {
            $query->where('products.itemName', 'like', '%' . $this->kataproduct . '%');
        }

        if ($this->shift) {
            $query->whereRaw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
        }

        // Select fields and paginate
        $antrianbsk = $query->select(
            'create_t_m_s.id as tmsID',
            'create_t_m_s.pendfNo',
            'create_t_m_s.tglDaftar',
            'create_t_m_s.tglMuat',
            'create_t_m_s.tmCarID',
            'create_t_m_s.tmDriver',
            'create_t_m_s.tmQtyKarung',
            'create_t_m_s.tmQtyKg',
            'customers.custName',
            'products.itemName',
            'jenistruks.jenisTruk',
            'createsppbs.sppbNo',
            DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift")
        )
            ->orderBy('create_t_m_s.id', 'desc')
            ->paginate(10);

        return view('livewire.cardantrianbesok', ['antrianbsk' => $antrianbsk]);
    }
}
