<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Revisisegeltruk extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $katakunci;
    public $transID;
    public $spmNo;
    public $driver;
    public $carID;
    public $custName;
    public $itemName;
    public $jenisTruk;
    public $sealNo1;
    public $sealNo2;
    public $sealNo3;
    public $sealNo4;
    public $sealNo5;
    public $fotoLama = [];
    public $fotoSealNo1;
    public $fotoSealNo2;
    public $fotoSealNo3;
    public $fotoSealNo4;
    public $fotoSealNo5;

    protected function rules()
    {
        return [
            'fotoSealNo1' => 'nullable|image|max:1024',
            'fotoSealNo2' => 'nullable|image|max:1024',
            'fotoSealNo3' => 'nullable|image|max:1024',
            'fotoSealNo4' => 'nullable|image|max:1024',
            'fotoSealNo5' => 'nullable|image|max:1024',
        ];
    }

    protected function messages()
    {
        return [
            '*.image' => 'File revisi harus berupa gambar.',
            '*.max' => 'Ukuran setiap foto maksimal 1 MB.',
        ];
    }

    public function updatingKatakunci()
    {
        $this->resetPage();
    }

    public function pilihTransaksi($id)
    {
        $data = DB::connection('sqlsrv')->table('createspms')
            ->leftJoin('customers', 'customers.custID', '=', 'createspms.custID')
            ->leftJoin('products', 'products.itemCode', '=', 'createspms.itemCode')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'createspms.spmJenisTruk')
            ->select(
                'createspms.*',
                'customers.custName',
                'products.itemName',
                'jenistruks.jenisTruk'
            )
            ->where('createspms.id', $id)
            ->whereNotNull('createspms.fotoSealNo1')
            ->where('createspms.tglSpm', '>=', Carbon::today()->subDay())
            ->first();

        if (!$data) {
            session()->flash('error', 'Transaksi segel tidak ditemukan atau sudah melewati batas revisi.');
            return;
        }

        $this->resetValidation();
        $this->resetFotoBaru();
        $this->transID = $data->id;
        $this->spmNo = $data->spmNo;
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->custName = $data->custName;
        $this->itemName = $data->itemName;
        $this->jenisTruk = $data->jenisTruk;

        for ($nomor = 1; $nomor <= 5; $nomor++) {
            $this->{'sealNo' . $nomor} = $data->{'sealNo' . $nomor};
            $this->fotoLama[$nomor] = $data->{'fotoSealNo' . $nomor};
        }
    }

    public function simpanRevisi()
    {
        if (!$this->transID) {
            $this->addError('transID', 'Pilih transaksi yang akan direvisi.');
            return;
        }

        $masihBisaDirevisi = DB::connection('sqlsrv')->table('createspms')
            ->where('id', $this->transID)
            ->whereNotNull('fotoSealNo1')
            ->where('tglSpm', '>=', Carbon::today()->subDay())
            ->exists();

        if (!$masihBisaDirevisi) {
            $this->addError('transID', 'Transaksi hanya dapat direvisi pada hari ini atau kemarin.');
            return;
        }

        $this->validate();

        $fotoBaru = collect(range(1, 5))
            ->filter(fn($nomor) => $this->{'fotoSealNo' . $nomor});

        if ($fotoBaru->isEmpty()) {
            $this->addError('foto', 'Pilih minimal satu foto yang akan direvisi.');
            return;
        }

        try {
            $spmIDs = [$this->transID];
            $headerID = DB::connection('sqlsrv')->table('trscale_details')
                ->where('spm_id', $this->transID)
                ->value('header_id');

            if ($headerID) {
                $spmIDs = DB::connection('sqlsrv')->table('trscale_details')
                    ->where('header_id', $headerID)
                    ->whereNotNull('spm_id')
                    ->pluck('spm_id')
                    ->unique()
                    ->values()
                    ->all();
            }

            $perubahan = [];
            $fotoTersimpan = [];
            $namaDasar = preg_replace('/[^A-Za-z0-9_-]/', '-', $this->spmNo);
            $waktu = Carbon::now()->format('YmdHis');

            foreach ($fotoBaru as $nomor) {
                $foto = $this->{'fotoSealNo' . $nomor};
                $namaFile = $namaDasar . '-rev-' . $waktu . '-' . $nomor . '.' . $foto->extension();
                $path = $foto->storeAs('uploads/segel', $namaFile, 'public');
                $perubahan['fotoSealNo' . $nomor] = $path;
                $fotoTersimpan[$nomor] = $path;
            }

            DB::connection('sqlsrv')->table('createspms')
                ->whereIn('id', $spmIDs)
                ->update($perubahan);

            foreach ($fotoTersimpan as $nomor => $path) {
                $fotoLama = $this->fotoLama[$nomor] ?? null;
                if ($fotoLama && $fotoLama !== $path) {
                    Storage::disk('public')->delete($fotoLama);
                }
            }

            session()->flash('message', 'Foto segel berhasil direvisi.');
            $this->batal();
        } catch (\Throwable $th) {
            report($th);
            session()->flash('error', 'Foto segel gagal direvisi. Silakan coba lagi.');
        }
    }

    public function batal()
    {
        $this->reset([
            'transID',
            'spmNo',
            'driver',
            'carID',
            'custName',
            'itemName',
            'jenisTruk',
            'sealNo1',
            'sealNo2',
            'sealNo3',
            'sealNo4',
            'sealNo5',
            'fotoLama',
        ]);
        $this->resetFotoBaru();
        $this->resetValidation();
    }

    private function resetFotoBaru()
    {
        $this->reset([
            'fotoSealNo1',
            'fotoSealNo2',
            'fotoSealNo3',
            'fotoSealNo4',
            'fotoSealNo5',
        ]);
    }

    public function render()
    {
        $transaksi = DB::connection('sqlsrv')->table('createspms')
            ->leftJoin('customers', 'customers.custID', '=', 'createspms.custID')
            ->leftJoin('products', 'products.itemCode', '=', 'createspms.itemCode')
            ->leftJoin('jenistruks', 'jenistruks.id', '=', 'createspms.spmJenisTruk')
            ->select(
                'createspms.id',
                'createspms.tglSpm',
                'createspms.spmNo',
                'createspms.driver',
                'createspms.carID',
                'customers.custName',
                'products.itemName',
                'jenistruks.jenisTruk'
            )
            ->whereNotNull('createspms.fotoSealNo1')
            ->where('createspms.tglSpm', '>=', Carbon::today()->subDay())
            ->when($this->katakunci, function ($query) {
                $query->where(function ($query) {
                    $query->where('createspms.carID', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createspms.spmNo', 'like', '%' . $this->katakunci . '%');
                });
            })
            ->orderByDesc('createspms.id')
            ->paginate(10);

        return view('livewire.revisisegeltruk', compact('transaksi'));
    }
}
