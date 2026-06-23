<?php

namespace App\Livewire;

use App\Exports\ExportAntrianHariIni;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Cardantrianhariini extends Component
{
    use WithPagination;
    public $katakunci;
    public $tglmuat;
    public $tglDaftar;
    public $isAppDate;
    public $katacust;
    public $katasppb;
    public $shift;
    public $kataproduct = [];

    public function clear()
    {

        redirect('/cardantrianhariini');
    }

    public function export()
    {
        return Excel::download(new ExportAntrianHariIni($this->katakunci, $this->katacust, $this->katasppb, $this->shift, $this->kataproduct, $this->tglmuat, $this->tglDaftar, $this->isAppDate), 'antrian-hari-ini-' . date('Y-m-d') . '.xlsx');
    }

    public function render()
    {
        // Build the base query
        $query = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->where('create_t_m_s.tmQtyKg', '>', 0);

        // Apply date filter
        if ($this->tglmuat) {
            $query->whereDate('tglMuat', '=', $this->tglmuat);
        } else {
            $query->whereDate('tglMuat', '=', Carbon::now());
        }

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

        if (!empty($this->kataproduct)) {
            $query->whereIn('products.itemCode', $this->kataproduct);
        }

        if ($this->shift) {
            $query->whereRaw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
        }

        // Get all products for dropdown
        $products = DB::connection('sqlsrv')->table('products')
            ->select('itemCode', 'itemName')
            ->where('type', '!=', 'NFG')
            ->orderBy('itemName')
            ->get();

        // Select fields and paginate
        $antriantdy = $query->select(
            'create_t_m_s.id as tmsID',
            'create_t_m_s.pendfNo',
            'create_t_m_s.tglDaftar',
            'create_t_m_s.isAppDate',
            'create_t_m_s.tmCarID',
            'create_t_m_s.tmDriver',
            'create_t_m_s.tglMuat',
            'create_t_m_s.tmQtyKarung',
            'create_t_m_s.tmQtyKg',
            'customers.custName',
            'products.itemName',
            'jenistruks.jenisTruk',
            'createsppbs.sppbNo',
            DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift")
        )
            ->orderBy('create_t_m_s.id')
            ->paginate(10);

        return view('livewire.cardantrianhariini', ['antriantdy' => $antriantdy, 'products' => $products]);
    }
}
