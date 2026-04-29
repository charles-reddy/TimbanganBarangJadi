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
    public $katasppb;
    public $shift;

    public function clear()
    {

        redirect('/cardantrianhariini');
    }


    public function render()
    {
        // dd(1);
        if ($this->katakunci != null) {
            $antriantdy = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglMuat', '=', Carbon::now())->where('create_t_m_s.tmCarID', 'like', '%' . $this->katakunci . '%')->when($this->shift, function ($query) {
                $query->whereRaw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
            })->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.tglMuat', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'customers.custName', 'products.itemName', 'jenistruks.jenisTruk', 'createsppbs.sppbNo', DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift"))->where('create_t_m_s.tmQtyKg', '>', 0)->orderBy('create_t_m_s.id')->paginate(10);
            // dd($dataantrian);
        } elseif ($this->katacust  != null) {
            $antriantdy = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglMuat', '=', Carbon::now())->where('customers.custName', 'like', '%' . $this->katacust . '%')->when($this->shift, function ($query) {
                $query->whereRaw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
            })->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.tglMuat', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'customers.custName', 'products.itemName', 'jenistruks.jenisTruk', 'createsppbs.sppbNo', DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift"))->where('create_t_m_s.tmQtyKg', '>', 0)->orderBy('create_t_m_s.id')->paginate(10);
            // dd($dataantrian);
        } elseif ($this->katasppb  != null) {
            $antriantdy = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglMuat', '=', Carbon::now())->where('createsppbs.sppbNo', 'like', '%' . $this->katasppb . '%')->when($this->shift, function ($query) {
                $query->whereRaw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
            })->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.tglMuat', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'customers.custName', 'products.itemName', 'jenistruks.jenisTruk', 'createsppbs.sppbNo', DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift"))->where('create_t_m_s.tmQtyKg', '>', 0)->orderBy('create_t_m_s.id')->paginate(10);
            // dd($dataantrian);
        } else {
            $antriantdy = DB::connection('sqlsrv')->table('create_t_m_s')->join('products', 'products.itemCode', 'create_t_m_s.itemCode')->join('customers', 'customers.custID', 'create_t_m_s.custID')->join('jenistruks', 'jenistruks.id', 'create_t_m_s.jenisTruk')->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')->whereDate('tglMuat', '=', Carbon::now())->when($this->shift, function ($query) {
                $query->whereRaw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
            })->select('create_t_m_s.id as tmsID', 'create_t_m_s.pendfNo', 'create_t_m_s.tglDaftar', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.tmQtyKarung', 'create_t_m_s.tmQtyKg', 'create_t_m_s.tglMuat', 'customers.custName', 'products.itemName', 'jenistruks.jenisTruk', 'createsppbs.sppbNo', DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift"))->where('create_t_m_s.tmQtyKg', '>', 0)->orderBy('create_t_m_s.id')->paginate(10);
            // dd($antriantdy);
        }


        return view('livewire.cardantrianhariini', ['antriantdy' => $antriantdy]);
    }
}
