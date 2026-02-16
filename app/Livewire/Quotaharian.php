<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Quotaharian extends Component
{

    #[Validate('required', message: 'SPPB Harus diisi')]
    public $sppbNo;
    #[Validate('required', message: 'Tgl Harus diisi')]
    public $tglMuat;
    #[Validate('required', message: 'quota Harus diisi')]
    #[Validate('numeric', message: 'quota Harus numeric')]
    public $quotaKg;
    public $updateMode = false;
    public $katakunci;
    public $transID;

    public function store()
    {
        // dd($this->sppbNo);
        // $this->validate([
        //     'sppbNo' => 'required',
        //     'tglMuat' => 'required',
        //     'quotaKg' => 'required|numeric',
        // ]);

        $this->validate();
        DB::connection('sqlsrv')->table('tbl_QuotaHarian')->insert([
            'quotaTmSppbID' => $this->sppbNo,
            'quotaTglDaftar' => $this->tglMuat,
            'quotaKg' => $this->quotaKg,
            'sisaQuotaKg' => $this->quotaKg,
            'created_at' => Carbon::now(),

        ]);

        // Reset form fields
        $this->sppbNo = '';
        $this->tglMuat = '';
        $this->quotaKg = '';

        session()->flash('message', 'Quota Harian berhasil ditambahkan.');
        $this->clear();
    }

    public function clear()
    {
        // Reset form fields
        $this->sppbNo = '';
        $this->tglMuat = '';
        $this->quotaKg = '';
        $this->updateMode = false;
        redirect()->route('quotaharian');
    }

    public function update()
    {
        $this->validate();
        DB::connection('sqlsrv')->table('tbl_QuotaHarian')->where('id', $this->transID)->update([

            'quotaTglDaftar' => $this->tglMuat,
            'quotaKg' => $this->quotaKg,
        ]);
        session()->flash('message', 'Quota Harian berhasil diupdate.');
        $this->clear();
    }

    public function edit($id)
    {
        $this->updateMode = true;
        $data = DB::connection('sqlsrv')->table('tbl_QuotaHarian')->join('createsppbs', 'createsppbs.id', 'tbl_QuotaHarian.quotaTmSppbID')->select('tbl_QuotaHarian.id', 'tbl_QuotaHarian.quotaTglDaftar', 'tbl_QuotaHarian.sisaQuotaKg', 'tbl_QuotaHarian.quotaTmSppbID', 'tbl_QuotaHarian.sisaQuotaKg', 'createsppbs.sppbNo')->where('tbl_QuotaHarian.id', $id)->first();
        // dd($data);
        $this->sppbNo = $data->quotaTmSppbID . $data->sppbNo;
        $this->tglMuat = Carbon::parse($data->quotaTglDaftar)->format('Y-m-d');
        $this->quotaKg = $data->sisaQuotaKg;
        $this->transID = $data->id;
    }

    public function delete($id)
    {
        // dd($id);
        DB::connection('sqlsrv')->table('tbl_QuotaHarian')->where('id', $id)->delete();
        session()->flash('message', 'Quota Harian berhasil dihapus.');
    }

    public function render()
    {
        $datasppb = DB::connection('sqlsrv')->table('createsppbs')->where('openQtyKg', '>', 0)->get();
        $dataquota = DB::connection('sqlsrv')->table('tbl_QuotaHarian')->join('createsppbs', 'createsppbs.id', 'tbl_QuotaHarian.quotaTmSppbID')->select('tbl_QuotaHarian.id', 'tbl_QuotaHarian.quotaTglDaftar', 'tbl_QuotaHarian.sisaQuotaKg', 'tbl_QuotaHarian.quotaTmSppbID', 'createsppbs.sppbNo')->orderBy('tbl_QuotaHarian.id', 'desc')->paginate(20);
        // dd($dataquota);
        return view('livewire.quotaharian', ['datasppb' => $datasppb, 'dataquota' => $dataquota]);
    }
}
