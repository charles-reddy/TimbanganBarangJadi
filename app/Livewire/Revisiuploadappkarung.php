<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Revisiuploadappkarung extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $katakunci;
    public $spmID;
    public $spmNo;
    public $driver;
    public $carID;
    public $custName;
    public $itemName;
    public $doNo;
    public $b10QtyKarung;
    public $avgKarung;
    public $fotoLama = [];
    public $buktiAppKarung1;
    public $buktiAppKarung2;
    public $buktiAppKarung3;

    protected function rules()
    {
        return [
            'buktiAppKarung1' => 'nullable|image|max:1024',
            'buktiAppKarung2' => 'nullable|image|max:1024',
            'buktiAppKarung3' => 'nullable|image|max:1024',
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
        $data = $this->queryTransaksiSelesai()
            ->select(
                'createspms.*',
                'customers.custName',
                'products.itemName',
                'trscale.doNo',
                'trscale.b10QtyKarung',
                'trscale.avgKarung'
            )
            ->where('createspms.id', $id)
            ->first();

        if (!$data) {
            session()->flash('error', 'Transaksi tidak ditemukan atau sudah melewati batas revisi.');
            return;
        }

        $this->resetValidation();
        $this->resetFotoBaru();
        $this->spmID = $data->id;
        $this->spmNo = $data->spmNo;
        $this->driver = $data->driver;
        $this->carID = $data->carID;
        $this->custName = $data->custName;
        $this->itemName = $data->itemName;
        $this->doNo = $data->doNo;
        $this->b10QtyKarung = $data->b10QtyKarung;
        $this->avgKarung = $data->avgKarung;

        for ($nomor = 1; $nomor <= 3; $nomor++) {
            $this->fotoLama[$nomor] = $data->{'buktiAppKarung' . $nomor};
        }
    }

    public function simpanRevisi()
    {
        if (!$this->spmID) {
            $this->addError('spmID', 'Pilih transaksi yang akan direvisi.');
            return;
        }

        $masihBisaDirevisi = $this->queryTransaksiSelesai()
            ->where('createspms.id', $this->spmID)
            ->exists();

        if (!$masihBisaDirevisi) {
            $this->addError('spmID', 'Transaksi hanya dapat direvisi pada hari ini atau kemarin.');
            return;
        }

        $this->validate();

        $fotoBaru = collect(range(1, 3))
            ->filter(fn($nomor) => $this->{'buktiAppKarung' . $nomor});

        if ($fotoBaru->isEmpty()) {
            $this->addError('foto', 'Pilih minimal satu foto yang akan direvisi.');
            return;
        }

        try {
            $perubahan = [];
            $fotoTersimpan = [];
            $namaDasar = preg_replace('/[^A-Za-z0-9_-]/', '-', $this->spmNo);
            $waktu = Carbon::now()->format('YmdHis');

            foreach ($fotoBaru as $nomor) {
                $foto = $this->{'buktiAppKarung' . $nomor};
                $namaFile = $namaDasar . '-rev-' . $waktu . '-' . $nomor . '.' . $foto->extension();
                $path = $foto->storeAs('uploads/appkarung', $namaFile, 'public');
                $perubahan['buktiAppKarung' . $nomor] = $path;
                $fotoTersimpan[$nomor] = $path;
            }

            DB::connection('sqlsrv')->table('createspms')
                ->where('id', $this->spmID)
                ->update($perubahan);

            foreach ($fotoTersimpan as $nomor => $path) {
                $fotoLama = $this->fotoLama[$nomor] ?? null;
                if ($fotoLama && $fotoLama !== $path) {
                    Storage::disk('public')->delete($fotoLama);
                }
            }

            session()->flash('message', 'Foto bukti pengecekan karung berhasil direvisi.');
            $this->batal();
        } catch (\Throwable $th) {
            report($th);
            session()->flash('error', 'Foto bukti pengecekan karung gagal direvisi. Silakan coba lagi.');
        }
    }

    public function batal()
    {
        $this->reset([
            'spmID',
            'spmNo',
            'driver',
            'carID',
            'custName',
            'itemName',
            'doNo',
            'b10QtyKarung',
            'avgKarung',
            'fotoLama',
        ]);
        $this->resetFotoBaru();
        $this->resetValidation();
    }

    private function resetFotoBaru()
    {
        $this->reset(['buktiAppKarung1', 'buktiAppKarung2', 'buktiAppKarung3']);
    }

    private function queryTransaksiSelesai()
    {
        return DB::connection('sqlsrv')->table('createspms')
            ->join('trscale', 'trscale.spmID', '=', 'createspms.id')
            ->join('customers', 'customers.custID', '=', 'createspms.custID')
            ->join('products', 'products.itemCode', '=', 'createspms.itemCode')
            ->where('products.type', '<>', 'FG-L')
            ->whereNotNull('trscale.avgKarung')
            ->whereNotNull('createspms.buktiAppKarung1')
            ->whereNotNull('createspms.buktiAppKarung2')
            ->whereNotNull('createspms.buktiAppKarung3')
            ->where('createspms.tglSpm', '>=', Carbon::today()->subDay())
            ->where('createspms.tglSpm', '<', Carbon::tomorrow());
    }

    public function render()
    {
        $transaksi = $this->queryTransaksiSelesai()
            ->select(
                'createspms.id',
                'createspms.tglSpm',
                'createspms.spmNo',
                'createspms.driver',
                'createspms.carID',
                'customers.custName',
                'products.itemName',
                'trscale.doNo',
                'trscale.b10QtyKarung',
                'trscale.avgKarung'
            )
            ->when($this->katakunci, function ($query) {
                $query->where(function ($query) {
                    $query->where('createspms.carID', 'like', '%' . $this->katakunci . '%')
                        ->orWhere('createspms.spmNo', 'like', '%' . $this->katakunci . '%');
                });
            })
            ->orderByDesc('createspms.id')
            ->paginate(10);

        return view('livewire.revisiuploadappkarung', compact('transaksi'));
    }
}
