<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Resettiketmuat extends Component
{
    use WithPagination;

    // Filter properties
    public $katakunci = '';
    public $katacust = '';
    public $kataproduct = [];
    public $tglMuat = '';
    public $sudahPabrik = '';

    // Modal confirmation properties
    public $showModal = false;
    public $resetId = null;
    public $resetData = [];

    // Bulk reset properties
    public $selectedItems = [];
    public $selectAll = false;
    public $isBulkMode = false;

    // Pagination theme
    protected $paginationTheme = 'bootstrap';

    // Reset pagination when filters change
    public function updatingKatakunci()
    {
        $this->resetPage();
    }

    public function updatingKatacust()
    {
        $this->resetPage();
    }

    public function updatingKataproduct()
    {
        $this->resetPage();
    }

    public function updatingTglMuat()
    {
        $this->resetPage();
    }

    public function updatingSudahPabrik()
    {
        $this->resetPage();
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            // Get all IDs from current page
            $this->selectedItems = DB::connection('sqlsrv')->table('create_t_m_s')
                ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
                ->join('customers', 'customers.custID', 'create_t_m_s.custID')
                ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
                ->whereNull('isSecCek')
                ->where('create_t_m_s.tmQtyKg', '>', 0)
                ->where(function ($query) {
                    $query->where('products.itemCode', 'S8B000390D')
                        ->orWhere('products.itemCode', 'S8A000390D');
                })
                ->pluck('create_t_m_s.id')
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        // Update selectAll checkbox state based on selected items
        $allIds = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->whereNull('isSecCek')
            ->where('create_t_m_s.tmQtyKg', '>', 0)
            ->where(function ($query) {
                $query->where('products.itemCode', 'S8B000390D')
                    ->orWhere('products.itemCode', 'S8A000390D');
            })
            ->pluck('create_t_m_s.id')
            ->toArray();

        $this->selectAll = count($this->selectedItems) === count($allIds) && count($allIds) > 0;
    }

    public function confirmBulkReset()
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Tidak ada item yang dipilih.');
            return;
        }

        // Get the data for all selected items
        $bulkData = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->whereIn('create_t_m_s.id', $this->selectedItems)
            ->select(
                'create_t_m_s.id',
                'pendfNo',
                'sppbNo',
                'custName',
                'itemName',
                'tmQtyKg',
                'tmQtyKarung',
                'tglMuat',
                'tmCarID'
            )
            ->get()
            ->toArray();

        if ($bulkData) {
            $this->resetData = array_map(fn($item) => (array) $item, $bulkData);
            $this->isBulkMode = true;
            $this->showModal = true;
        }
    }

    public function confirmReset($id)
    {
        // Get the data for confirmation modal
        $data = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->where('create_t_m_s.id', $id)
            ->select(
                'create_t_m_s.id',
                'pendfNo',
                'sppbNo',
                'custName',
                'itemName',
                'tmQtyKg',
                'tmQtyKarung',
                'tglMuat',
                'tmCarID'
            )
            ->first();

        if ($data) {
            $this->resetId = $id;
            $this->resetData = (array) $data;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetId = null;
        $this->resetData = [];
        $this->isBulkMode = false;
    }

    public function resetApproval($id = null)
    {
        try {
            DB::connection('sqlsrv')->beginTransaction();

            if ($this->isBulkMode) {
                // Bulk reset mode
                if (empty($this->selectedItems)) {
                    session()->flash('error', 'Tidak ada item yang dipilih.');
                    return;
                }

                $totalBerat = 0;
                $processedCount = 0;

                foreach ($this->selectedItems as $itemId) {
                    $tiketMuat = DB::connection('sqlsrv')->table('create_t_m_s')
                        ->where('id', $itemId)
                        ->first();

                    if ($tiketMuat) {
                        $beratKg = $tiketMuat->tmQtyKg;
                        $sppbId = $tiketMuat->tmSppbID;

                        // Update create_t_m_s: set tmQtyKg to 0 and reset approval
                        DB::connection('sqlsrv')->table('create_t_m_s')
                            ->where('id', $itemId)
                            ->update([
                                'tmQtyKg' => 0,
                                'isMktApp' => null,
                                'isMktAppID' => null,
                                'isAppDate' => null,
                            ]);

                        // Update createsppbs: increment openQtyKarung by the original tmQtyKg
                        DB::connection('sqlsrv')->table('createsppbs')
                            ->where('id', $sppbId)
                            ->increment('openQtyKg', $beratKg);

                        $totalBerat += $beratKg;
                        $processedCount++;
                    }
                }

                DB::connection('sqlsrv')->commit();

                // Clear selections and close modal
                $this->selectedItems = [];
                $this->selectAll = false;
                $this->closeModal();

                session()->flash('message', "Berhasil mereset {$processedCount} tiket muat. Total berat " . number_format($totalBerat) . ' Kg telah dikembalikan ke SPPB.');
            } else {
                // Single reset mode
                $targetId = $id ?? $this->resetId;

                if (!$targetId) {
                    session()->flash('error', 'ID tidak valid.');
                    return;
                }

                // Get the current record to retrieve tmQtyKg and tmSppbID
                $tiketMuat = DB::connection('sqlsrv')->table('create_t_m_s')
                    ->where('id', $targetId)
                    ->first();

                if (!$tiketMuat) {
                    session()->flash('error', 'Tiket muat tidak ditemukan.');
                    return;
                }

                $beratKg = $tiketMuat->tmQtyKg;
                $sppbId = $tiketMuat->tmSppbID;

                // Update create_t_m_s: set tmQtyKg to 0 and reset approval
                DB::connection('sqlsrv')->table('create_t_m_s')
                    ->where('id', $targetId)
                    ->update([
                        'tmQtyKg' => 0,
                        'isMktApp' => null,
                        'isMktAppID' => null,
                        'isAppDate' => null,
                    ]);

                // Update createsppbs: increment openQtyKarung by the original tmQtyKg
                DB::connection('sqlsrv')->table('createsppbs')
                    ->where('id', $sppbId)
                    ->increment('openQtyKg', $beratKg);

                DB::connection('sqlsrv')->commit();

                // Close modal and clear data
                $this->closeModal();

                session()->flash('message', 'Approval has been reset successfully. Berat ' . number_format($beratKg) . ' Kg telah dikembalikan ke SPPB.');
            }
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            session()->flash('error', 'Failed to reset approval: ' . $e->getMessage());
        }
    }

    public function render()
    {

        // Build the base query
        $query = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->whereNull('isSecCek')
            ->where('tmQtyKg', '>', 0)
            ->where(function ($query) {
                $query->where('products.itemCode', 'S8B000390D')
                    ->orWhere('products.itemCode', 'S8A000390D');
            });

        // Apply filters conditionally
        if ($this->katakunci) {
            $query->where('pendfNo', 'like', '%' . $this->katakunci . '%');
        }

        if ($this->katacust) {
            $query->where('custName', 'like', '%' . $this->katacust . '%');
        }

        if ($this->tglMuat) {
            $query->whereDate('tglMuat', '=', $this->tglMuat);
        }

        if (!empty($this->kataproduct)) {
            $query->whereIn('products.itemCode', $this->kataproduct);
        }

        if ($this->sudahPabrik !== null && $this->sudahPabrik !== '') {
            if ($this->sudahPabrik == '1') {
                $query->whereNotNull('isSecCek');
            } else {
                $query->whereNull('isSecCek');
            }
        }

        // Get all products for dropdown
        $products = DB::connection('sqlsrv')->table('products')
            ->select('itemCode', 'itemName')
            ->where('type', '!=', 'NFG')
            ->orderBy('itemName')
            ->get();

        // Select fields and paginate
        $datatiketmuat = $query->select(
            'create_t_m_s.id',
            'pendfNo',
            'tmQtyKg',
            'tmQtyKarung',
            'sppbNo',
            'tmCarID',
            'isMktApp',
            'tglMuat',
            'custName',
            'isSecCek',
            'tmTranspName',
            'isSecCekDate',
            'itemName'
        )
            ->orderBy('id', 'desc')
            ->paginate(10);


        return view('livewire.resettiketmuat', [
            'datatiketmuat' => $datatiketmuat,
            'products' => $products,
        ]);
    }
}
