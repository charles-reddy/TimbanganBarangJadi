<?php

namespace App\Livewire;

use App\Exports\exportTrukTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Truktransaction extends Component
{
    use WithPagination;
    public $katakunci;
    public $katacust;
    public $tglout1;
    public $tglout2;
    public $shift;

    public function updatedTglout1($value)
    {
        if ($value) {
            try {
                // Validasi format tanggal dengan regex
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $this->tglout1 = null;
                    $this->dispatch('show-alert', message: 'Format tanggal tidak valid! Gunakan format YYYY-MM-DD.');
                    return;
                }

                // Validasi format tanggal dengan DateTime
                $date = \DateTime::createFromFormat('Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    $this->tglout1 = null;
                    $this->dispatch('show-alert', message: 'Tanggal tidak valid! Periksa hari, bulan, dan tahun.');
                    return;
                }

                // Validasi dengan Carbon untuk memastikan tanggal valid
                $carbonDate = Carbon::parse($value);

                // Validasi tanggal tidak boleh di masa depan
                if ($carbonDate->isAfter(Carbon::now())) {
                    $this->tglout1 = null;
                    $this->dispatch('show-alert', message: 'Tanggal tidak boleh lebih dari hari ini!');
                    return;
                }

                // Validasi jika tglout2 sudah diisi, tglout1 tidak boleh lebih besar dari tglout2
                if ($this->tglout2) {
                    try {
                        $carbonDate2 = Carbon::parse($this->tglout2);
                        if ($carbonDate->isAfter($carbonDate2)) {
                            $this->tglout1 = null;
                            $this->dispatch('show-alert', message: 'Tanggal From tidak boleh lebih besar dari tanggal To!');
                            return;
                        }
                    } catch (\Exception $e) {
                        // tglout2 invalid, abaikan validasi ini
                    }
                }

                // Set default tglout2 ke hari ini jika belum diisi
                if (!$this->tglout2) {
                    $this->tglout2 = Carbon::now()->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $this->tglout1 = null;
                $this->dispatch('show-alert', message: 'Format tanggal tidak valid! Error: ' . $e->getMessage());
                return;
            }
        }
    }

    public function updatedTglout2($value)
    {
        if ($value) {
            try {
                // Validasi format tanggal dengan regex
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $this->tglout2 = null;
                    $this->dispatch('show-alert', message: 'Format tanggal tidak valid! Gunakan format YYYY-MM-DD.');
                    return;
                }

                // Validasi format tanggal dengan DateTime
                $date = \DateTime::createFromFormat('Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    $this->tglout2 = null;
                    $this->dispatch('show-alert', message: 'Tanggal tidak valid! Periksa hari, bulan, dan tahun.');
                    return;
                }

                // Validasi dengan Carbon untuk memastikan tanggal valid
                $carbonDate = Carbon::parse($value);

                // Validasi tanggal tidak boleh di masa depan
                if ($carbonDate->isAfter(Carbon::now())) {
                    $this->tglout2 = null;
                    $this->dispatch('show-alert', message: 'Tanggal tidak boleh lebih dari hari ini!');
                    return;
                }

                // Validasi jika tglout1 sudah diisi, tglout2 tidak boleh lebih kecil dari tglout1
                if ($this->tglout1) {
                    try {
                        $carbonDate1 = Carbon::parse($this->tglout1);
                        if ($carbonDate->isBefore($carbonDate1)) {
                            $this->tglout2 = null;
                            $this->dispatch('show-alert', message: 'Tanggal To tidak boleh lebih kecil dari tanggal From!');
                            return;
                        }
                    } catch (\Exception $e) {
                        // tglout1 invalid, abaikan validasi ini
                    }
                }
            } catch (\Exception $e) {
                $this->tglout2 = null;
                $this->dispatch('show-alert', message: 'Format tanggal tidak valid! Error: ' . $e->getMessage());
                return;
            }
        }
    }


    public function export_out()
    {

        return Excel::download(new exportTrukTransaction($this->tglout1, $this->tglout2, $this->katakunci, $this->katacust), "Truktransaction-export.xlsx");
    }


    public function clear()
    {

        redirect('/truktransaction');
    }

    public function render()
    {
        $tglout = DB::connection('sqlsrv')->table('trscale')->whereNotNull('netto')->orderBy('id', 'desc')->first();
        // dd($tglout);
        if ($this->katakunci != null) {
            $data = DB::connection('sqlsrv')->table('trscale')
                ->join('createspms', 'createspms.id', 'trscale.spmID')
                ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale.netto')
                ->where(function ($query) {
                    $query->where('createspms.carID', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createspms.dnNo', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createsppbs.sppbNo', 'like', '%' . $this->katakunci . '%');
                })
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
                ->select(
                    'trscale.id',
                    'create_t_m_s.isSecCekDate',
                    'trscale.jam_in as tgl_tim_in',
                    'trscale.jam_out as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'customers.custName',
                    'products.itemName',
                    'products.type',
                    'createspms.carID',
                    'createspms.driver',
                    'trscale.timbangin',
                    'trscale.timbangout',
                    'trscale.netto',
                    'trscale.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale.avgkarung as avgKarung',
                    'trscale.isApp',
                    'createspms.buktiPGI',
                    'createspms.id as spmID',
                    'create_t_m_s.tglDaftar',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_wbin")
                )
                ->orderBy('trscale.id', 'desc')
                ->paginate(10);
        } elseif (($this->katacust)  != null) {
            $data = DB::connection('sqlsrv')->table('trscale')
                ->join('createspms', 'createspms.id', 'trscale.spmID')
                ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale.netto')
                ->where(function ($query) {
                    $query->where('customers.custName', 'like', '%' . $this->katacust . '%')
                        ->orWhere('createspms.dnNo', 'like', '%' . $this->katacust . '%');
                })
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
                ->select(
                    'trscale.id',
                    'create_t_m_s.isSecCekDate',
                    'trscale.jam_in as tgl_tim_in',
                    'trscale.jam_out as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'customers.custName',
                    'products.itemName',
                    'products.type',
                    'createspms.carID',
                    'createspms.driver',
                    'trscale.timbangin',
                    'trscale.timbangout',
                    'trscale.netto',
                    'trscale.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale.avgkarung as avgKarung',
                    'trscale.isApp',
                    'createspms.buktiPGI',
                    'createspms.id as spmID',
                    'create_t_m_s.tglDaftar',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_wbin")
                )
                ->orderBy('trscale.id', 'desc')
                ->paginate(10);
        } elseif (($this->tglout1)  != null) {
            try {
                // Pastikan tglout2 tidak null, jika null set ke hari ini
                $tglout2 = $this->tglout2 ?? Carbon::now()->format('Y-m-d');

                // Konversi ke format yang diterima SQL Server
                $tglFrom = Carbon::parse($this->tglout1)->format('Y-m-d');
                $tglTo = Carbon::parse($tglout2)->format('Y-m-d');

                $data = DB::connection('sqlsrv')->table('trscale')
                    ->join('createspms', 'createspms.id', 'trscale.spmID')
                    ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                    ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                    ->join('products', 'products.itemCode', 'trscale.itemCode')
                    ->join('customers', 'customers.custID', 'trscale.custID')
                    ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                    ->whereNotNull('trscale.netto')
                    ->whereDate('trscale.jam_out', '>=', $tglFrom)
                    ->whereDate('trscale.jam_out', '<=', $tglTo)
                    ->when($this->shift, function ($query) {
                        $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                    })
                    ->select(
                        'trscale.id',
                        'create_t_m_s.isSecCekDate',
                        'trscale.jam_in as tgl_tim_in',
                        'trscale.jam_out as tgl',
                        'createsppbs.sppbNo',
                        'createspms.spmNo',
                        'create_t_m_s.pendfNo',
                        'customers.custName',
                        'products.itemName',
                        'products.type',
                        'createspms.carID',
                        'createspms.driver',
                        'trscale.timbangin',
                        'trscale.timbangout',
                        'trscale.netto',
                        'trscale.b10QtyKarung',
                        'createspms.dnNo',
                        'trscale.avgkarung as avgKarung',
                        'trscale.isApp',
                        'createspms.buktiPGI',
                        'createspms.id as spmID',
                        'create_t_m_s.tglDaftar',
                        DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                        DB::raw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_wbin")
                    )
                    ->orderBy('trscale.id', 'desc')
                    ->paginate(10);
            } catch (\Exception $e) {
                // Jika terjadi error parsing, reset dan tampilkan data default
                $this->tglout1 = null;
                $this->tglout2 = null;
                session()->flash('error', 'Error parsing tanggal: Salah format tgl ');
                $data = DB::connection('sqlsrv')->table('trscale')
                    ->join('createspms', 'createspms.id', 'trscale.spmID')
                    ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                    ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                    ->join('products', 'products.itemCode', 'trscale.itemCode')
                    ->join('customers', 'customers.custID', 'trscale.custID')
                    ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                    ->whereNotNull('trscale.netto')
                    ->whereDate('trscale.jam_out', '>=', Carbon::now()->addDays(-14))
                    ->whereDate('trscale.jam_out', '<=', Carbon::now())
                    ->when($this->shift, function ($query) {
                        $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                    })
                    ->select(
                        'trscale.id',
                        'create_t_m_s.isSecCekDate',
                        'trscale.jam_in as tgl_tim_in',
                        'trscale.jam_out as tgl',
                        'createsppbs.sppbNo',
                        'createspms.spmNo',
                        'create_t_m_s.pendfNo',
                        'customers.custName',
                        'products.itemName',
                        'products.type',
                        'createspms.carID',
                        'createspms.driver',
                        'trscale.timbangin',
                        'trscale.timbangout',
                        'trscale.netto',
                        'trscale.b10QtyKarung',
                        'createspms.dnNo',
                        'trscale.avgkarung as avgKarung',
                        'trscale.isApp',
                        'createspms.buktiPGI',
                        'createspms.id as spmID',
                        'create_t_m_s.tglDaftar',
                        DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                        DB::raw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_wbin")
                    )
                    ->orderBy('trscale.id', 'desc')
                    ->paginate(10);
            }
        } else {
            $this->tglout1 = $tglout->jam_out;
            // dd($tglout->tgl);
            $data = DB::connection('sqlsrv')->table('trscale')
                ->join('createspms', 'createspms.id', 'trscale.spmID')
                ->join('create_t_m_s', 'create_t_m_s.id', 'createspms.tiketID')
                ->join('createsppbs', 'createsppbs.id', 'createspms.sppbNo')
                ->join('products', 'products.itemCode', 'trscale.itemCode')
                ->join('customers', 'customers.custID', 'trscale.custID')
                ->join('jenistruks', 'jenistruks.id', 'createspms.spmJenisTruk')
                ->whereNotNull('trscale.netto')
                ->whereDate('trscale.jam_out', '>=', Carbon::now()->addDays(-14))
                ->whereDate('trscale.jam_out', '<=', Carbon::now())
                ->when($this->shift, function ($query) {
                    $query->whereRaw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END = ?", [$this->shift]);
                })
                ->select(
                    'trscale.id',
                    'create_t_m_s.isSecCekDate',
                    'trscale.jam_in as tgl_tim_in',
                    'trscale.jam_out as tgl',
                    'createsppbs.sppbNo',
                    'createspms.spmNo',
                    'create_t_m_s.pendfNo',
                    'customers.custName',
                    'products.itemName',
                    'products.type',
                    'createspms.carID',
                    'createspms.driver',
                    'trscale.timbangin',
                    'trscale.timbangout',
                    'trscale.netto',
                    'trscale.b10QtyKarung',
                    'createspms.dnNo',
                    'trscale.avgkarung as avgKarung',
                    'trscale.isApp',
                    'createspms.buktiPGI',
                    'createspms.id as spmID',
                    'create_t_m_s.tglDaftar',
                    DB::raw("CASE WHEN CAST(create_t_m_s.jamMuat as TIME) >= '08:00' AND CAST(create_t_m_s.jamMuat as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '12:00' AND CAST(create_t_m_s.jamMuat as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(create_t_m_s.jamMuat as TIME) >= '16:00' AND CAST(create_t_m_s.jamMuat as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_tm"),
                    DB::raw("CASE WHEN CAST(trscale.jam_in as TIME) >= '08:00' AND CAST(trscale.jam_in as TIME) < '12:00' THEN 'Shift 1' WHEN CAST(trscale.jam_in as TIME) >= '12:00' AND CAST(trscale.jam_in as TIME) < '16:00' THEN 'Shift 2' WHEN CAST(trscale.jam_in as TIME) >= '16:00' AND CAST(trscale.jam_in as TIME) < '20:00' THEN 'Shift 3' ELSE 'Outside' END as shift_wbin")
                )
                ->orderBy('trscale.id', 'desc')
                ->paginate(10);
        }
        // dd($data);
        return view('livewire.truktransaction', ['data' => $data]);
    }
}
