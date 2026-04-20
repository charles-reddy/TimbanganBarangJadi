<?php

namespace App\Livewire;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Createpembatasanharian extends Component
{
    use WithPagination;

    public $katakunci;
    public $quotaTglDatang;
    #[Validate('required|numeric|min:0', message: 'Quota Shift 1 harus diisi dengan angka')]
    public $quota1;
    #[Validate('required|numeric|min:0', message: 'Quota Shift 2 harus diisi dengan angka')]
    public $quota2;
    #[Validate('required|numeric|min:0', message: 'Quota Shift 3 harus diisi dengan angka')]
    public $quota3;
    public $updateData = false;
    public $id_quota;

    public function save()
    {
        $this->validate();

        try {
            if ($this->updateData) {
                // Update existing record
                $updateData = [
                    'quota1' => $this->quota1,
                    'quota2' => $this->quota2,
                    'quota3' => $this->quota3,
                    'updated_at' => Carbon::now(),
                ];

                // Hanya update quotaTglDatang jika diisi
                if (!empty($this->quotaTglDatang)) {
                    $updateData['quotaTglDatang'] = $this->quotaTglDatang;
                }

                DB::connection('sqlsrv')->table('tbl_QuotaLoading')
                    ->where('id', $this->id_quota)
                    ->update($updateData);

                session()->flash('message', 'Data berhasil diupdate');
            } else {
                // Insert new record
                $insertData = [
                    'quota1' => $this->quota1,
                    'quota2' => $this->quota2,
                    'quota3' => $this->quota3,
                    'isApprove' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                // Hanya insert quotaTglDatang jika diisi
                if (!empty($this->quotaTglDatang)) {
                    $insertData['quotaTglDatang'] = $this->quotaTglDatang;
                }

                DB::connection('sqlsrv')->table('tbl_QuotaLoading')->insert($insertData);

                session()->flash('message', 'Data berhasil disimpan');
            }

            $this->clear();
        } catch (Exception $e) {
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
            return;
        }
    }

    public function edit($id)
    {
        $data = DB::connection('sqlsrv')->table('tbl_QuotaLoading')
            ->where('id', $id)
            ->first();

        if ($data) {
            $this->quotaTglDatang = Carbon::parse($data->quotaTglDatang)->format('Y-m-d');
            $this->quota1 = $data->quota1;
            $this->quota2 = $data->quota2;
            $this->quota3 = $data->quota3;
            $this->updateData = true;
            $this->id_quota = $id;
        }
    }

    public function delete($id)
    {
        try {
            DB::connection('sqlsrv')->table('tbl_QuotaLoading')
                ->where('id', $id)
                ->delete();

            session()->flash('message', 'Data berhasil dihapus');
        } catch (Exception $e) {
            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $user = Auth::user();

            // Cek role user - hanya administrator dan manager-logistik yang bisa approve
            if (!$user->hasRole(['administrator', 'manager-logistik'])) {
                session()->flash('error', 'Anda tidak memiliki akses untuk approve data');
                return;
            }

            DB::connection('sqlsrv')->table('tbl_QuotaLoading')
                ->where('id', $id)
                ->update([
                    'isApprove' => true,
                    'approvedBy' => $user->id,
                    'approvedAt' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            session()->flash('message', 'Data berhasil di-approve');
        } catch (Exception $e) {
            session()->flash('error', 'Gagal approve data: ' . $e->getMessage());
        }
    }

    public function clear()
    {
        $this->quotaTglDatang = '';
        $this->quota1 = '';
        $this->quota2 = '';
        $this->quota3 = '';
        $this->updateData = false;
        $this->id_quota = null;
        $this->resetValidation();
    }

    public function render()
    {
        $query = DB::connection('sqlsrv')->table('tbl_QuotaLoading')
            ->leftJoin('users', 'users.id', '=', 'tbl_QuotaLoading.approvedBy')
            ->select(
                'tbl_QuotaLoading.id',
                'tbl_QuotaLoading.quotaTglDatang',
                'tbl_QuotaLoading.quota1',
                'tbl_QuotaLoading.quota2',
                'tbl_QuotaLoading.quota3',
                'tbl_QuotaLoading.isApprove',
                'tbl_QuotaLoading.approvedBy',
                'tbl_QuotaLoading.approvedAt',
                'users.name as approvedByName'
            )
            ->orderBy('tbl_QuotaLoading.quotaTglDatang', 'desc');

        if ($this->katakunci) {
            // Jika input hanya tanda "-", tampilkan data dengan tanggal null
            if (trim($this->katakunci) === '-') {
                $query->whereNull('quotaTglDatang');
            } else {
                // Cek apakah format dd-mm-yyyy
                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $this->katakunci)) {
                    // Konversi dd-mm-yyyy ke yyyy-mm-dd untuk query database
                    try {
                        $tglSearch = Carbon::createFromFormat('d-m-Y', $this->katakunci)->format('Y-m-d');
                        $query->whereDate('quotaTglDatang', $tglSearch);
                    } catch (Exception $e) {
                        // Jika konversi gagal, cari dengan LIKE biasa
                        $query->where('quotaTglDatang', 'like', '%' . $this->katakunci . '%');
                    }
                } else {
                    // Jika bukan format dd-mm-yyyy, cari dengan LIKE
                    $query->where('quotaTglDatang', 'like', '%' . $this->katakunci . '%');
                }
            }
        }

        $quotaData = $query->paginate(10);

        return view('livewire.createpembatasanharian', [
            'quotaData' => $quotaData
        ]);
    }
}
