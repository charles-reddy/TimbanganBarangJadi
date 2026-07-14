<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\TrscaleHeader;
use App\Models\TrscaleDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MultiProductInputB10 extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterDate = '';
    public $showModal = false;
    public $selectedHeader = null;

    // Form data per product (array)
    public $b10Data = [];
    public $uploadedFiles = [];

    public function mount()
    {
        $this->filterDate = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Open input B10 modal
     */
    public function openInputModal($headerId)
    {
        $this->selectedHeader = TrscaleHeader::with(['details.spm.product'])->findOrFail($headerId);
        
        // Initialize b10Data array
        $this->b10Data = [];
        foreach ($this->selectedHeader->details as $detail) {
            $this->b10Data[$detail->id] = [
                'b10QtyKarung' => $detail->b10QtyKarung ?? '',
                'b10BatchNo' => $detail->b10BatchNo ?? '',
                'kontainerNo' => $detail->kontainerNo ?? '',
                'krani' => $detail->krani ?? '',
            ];
        }

        $this->showModal = true;
    }

    /**
     * Save B10 data for all products
     */
    public function saveB10Data()
    {
        // Validation
        $rules = [];
        $messages = [];
        
        foreach ($this->selectedHeader->details as $detail) {
            $rules["b10Data.{$detail->id}.b10QtyKarung"] = 'required|integer|min:1';
            $rules["b10Data.{$detail->id}.b10BatchNo"] = 'required|string|max:50';
            $rules["b10Data.{$detail->id}.krani"] = 'required|string|max:100';
            $rules["uploadedFiles.{$detail->id}"] = 'required|image|max:1024'; // Max 1MB
            
            $messages["b10Data.{$detail->id}.b10QtyKarung.required"] = "Qty Karung untuk {$detail->itemName} wajib diisi";
            $messages["b10Data.{$detail->id}.b10QtyKarung.integer"] = "Qty Karung harus angka";
            $messages["b10Data.{$detail->id}.b10BatchNo.required"] = "Batch No untuk {$detail->itemName} wajib diisi";
            $messages["b10Data.{$detail->id}.krani.required"] = "Nama Krani untuk {$detail->itemName} wajib diisi";
            $messages["uploadedFiles.{$detail->id}.required"] = "Foto Form Loading untuk {$detail->itemName} wajib diupload";
            $messages["uploadedFiles.{$detail->id}.image"] = "File harus berupa gambar";
            $messages["uploadedFiles.{$detail->id}.max"] = "Ukuran foto maksimal 1 MB";
        }

        $this->validate($rules, $messages);

        DB::beginTransaction();

        try {
            foreach ($this->selectedHeader->details as $detail) {
                $data = $this->b10Data[$detail->id];
                
                // Upload photo
                $photoPath = null;
                if (isset($this->uploadedFiles[$detail->id])) {
                    $spmNo = str_replace("/", "-", $detail->spm->spmNo);
                    $fileName = $spmNo . '.jpg';
                    $photoPath = 'uploads/formloading/' . $fileName;
                    $this->uploadedFiles[$detail->id]->storeAs('uploads/formloading', $fileName, 'public');
                }

                // Update detail
                $detail->update([
                    'b10QtyKarung' => $data['b10QtyKarung'],
                    'b10BatchNo' => $data['b10BatchNo'],
                    'kontainerNo' => $data['kontainerNo'] ?? null,
                    'krani' => $data['krani'],
                    'imgFormLoading' => $photoPath,
                    'isLoadingDone' => true,
                    'isLoadingDoneDate' => Carbon::now(),
                ]);
            }

            // Update header status
            $this->selectedHeader->update([
                'status' => 'READY_FOR_WEIGH_OUT',
                'b10_input_by' => Auth::id(),
                'b10_input_at' => Carbon::now(),
            ]);

            DB::commit();

            session()->flash('success', "Input B10 berhasil untuk Trans No: {$this->selectedHeader->trans_no}. Transaksi siap untuk timbang keluar.");
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['selectedHeader', 'b10Data', 'uploadedFiles']);
    }

    public function render()
    {
        $headers = TrscaleHeader::with(['details.spm.product', 'userIn'])
            ->where('status', 'WEIGHING_IN')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('trans_no', 'like', '%' . $this->search . '%')
                      ->orWhere('carID', 'like', '%' . $this->search . '%')
                      ->orWhere('driver', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('weigh_in_time', $this->filterDate);
            })
            ->orderBy('weigh_in_time', 'desc')
            ->paginate(10);

        return view('livewire.multi-product-input-b10', [
            'headers' => $headers,
        ]);
    }
}
