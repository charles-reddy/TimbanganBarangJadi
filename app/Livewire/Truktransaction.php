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
        $tglout = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->orderBy('id', 'desc')->first();
        // dd($tglout);
        if ($this->katakunci != null) {
            $data = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->where('carID', 'like', '%' . $this->katakunci . '%')->orWhere('dnNo', 'like', '%' . $this->katakunci . '%')->orWhere('sppbNo', 'like', '%' . $this->katakunci . '%')->orderBy('id', 'desc')->paginate(10);
        } elseif (($this->katacust)  != null) {

            $data = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->where('custName', 'like', '%' . $this->katacust . '%')->orWhere('dnNo', 'like', '%' . $this->katacust . '%')->orderBy('id', 'desc')->paginate(10);
        } elseif (($this->tglout1)  != null) {
            try {
                // Pastikan tglout2 tidak null, jika null set ke hari ini
                $tglout2 = $this->tglout2 ?? Carbon::now()->format('Y-m-d');

                // Konversi ke format yang diterima SQL Server
                $tglFrom = Carbon::parse($this->tglout1)->format('Y-m-d');
                $tglTo = Carbon::parse($tglout2)->format('Y-m-d');

                $data = DB::connection('sqlsrv')->table('vw_truktransaction')
                    ->whereNotNull('netto')
                    ->whereBetween('tgl', [$tglFrom, $tglTo])
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            } catch (\Exception $e) {
                // Jika terjadi error parsing, reset dan tampilkan data default
                $this->tglout1 = null;
                $this->tglout2 = null;
                session()->flash('error', 'Error parsing tanggal: Salah format tgl ');
                $data = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->whereBetween('tgl', [Carbon::now()->addDays(-14), Carbon::now()])->orderBy('id', 'desc')->paginate(10);
            }
        } else {
            $this->tglout1 = $tglout->tgl;
            // dd($tglout->tgl);
            $data = DB::connection('sqlsrv')->table('vw_truktransaction')->whereNotNull('netto')->whereBetween('tgl', [Carbon::now()->addDays(-14), Carbon::now()])->orderBy('id', 'desc')->paginate(10);
        }
        // dd($data);
        return view('livewire.truktransaction', ['data' => $data]);
    }
}
