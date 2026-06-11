<?php

namespace App\Livewire;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Masterproduct extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Validate('required', message: 'Kode Item harus diisi')]
    #[Validate('unique:products,itemCode', message: 'Kode Item sudah ada')]
    public $itemCode;

    #[Validate('required', message: 'Nama Produk harus diisi')]
    public $itemName;

    #[Validate('required', message: 'Tipe Produk harus diisi')]
    public $type;

    public $uom;

    public $sortColumn = 'itemCode';
    public $sortDirection = 'asc';
    public $updateData = false;
    public $transID;
    public $katakunci;

    public function store()
    {
        $this->validate();
        try {
            DB::connection('sqlsrv')->table('products')->insert([
                'itemCode' => $this->itemCode,
                'itemName' => $this->itemName,
                'type' => $this->type,
                'uom' => $this->uom,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            session()->flash('message', 'Data produk berhasil ditambahkan');
            $this->clear();
        } catch (Exception $e) {
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
            return;
        }
    }

    public function clear()
    {
        $this->itemCode = '';
        $this->itemName = '';
        $this->type = '';
        $this->uom = '';
        $this->updateData = false;
        $this->resetValidation();

        redirect('/masterproduct');
    }

    public function edit($id)
    {
        $data = DB::connection('sqlsrv')->table('products')->where('itemCode', $id)->first();

        if ($data) {
            $this->itemCode = $data->itemCode;
            $this->itemName = $data->itemName;
            $this->type = $data->type ?? '';
            $this->uom = $data->uom ?? '';
            $this->updateData = true;
            $this->transID = $id;
        }
    }

    public function update()
    {
        $this->validate([
            'itemName' => 'required',
            'type' => 'required',
        ]);

        try {
            DB::connection('sqlsrv')->table('products')->where('itemCode', $this->transID)->update([
                'itemName' => $this->itemName,
                'type' => $this->type,
                'uom' => $this->uom,
                'updated_at' => Carbon::now(),
            ]);

            session()->flash('message', 'Data produk berhasil diupdate');
            $this->clear();
        } catch (Exception $e) {
            session()->flash('error', 'Gagal mengupdate data: ' . $e->getMessage());
            return;
        }
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
    }

    public function render()
    {
        if ($this->katakunci != null) {
            $data = DB::connection('sqlsrv')
                ->table('products')
                ->where('itemName', 'like', '%' . $this->katakunci . '%')
                ->orWhere('itemCode', 'like', '%' . $this->katakunci . '%')
                ->orWhere('type', 'like', '%' . $this->katakunci . '%')
                ->orWhere('uom', 'like', '%' . $this->katakunci . '%')
                ->orderby($this->sortColumn, $this->sortDirection)
                ->paginate(10);
        } else {
            $data = DB::connection('sqlsrv')
                ->table('products')
                ->orderby($this->sortColumn, $this->sortDirection)
                ->paginate(10);
        }

        return view('livewire.masterproduct', ['mproduct' => $data]);
    }
}
