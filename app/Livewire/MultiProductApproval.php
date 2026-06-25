<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\MultiProductWeighingService;
use App\Models\TrscaleHeader;

class MultiProductApproval extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Filter
    public $search = '';
    public $filterStatus = 'PENDING_APPROVAL'; // PENDING_APPROVAL, APPROVED, REJECTED, ALL

    // Selected transaction for approval
    public $selectedTransactionId = null;
    public $selectedTransaction = null;

    // Form data
    public $remarks = '';

    // Modal control
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $showDetailModal = false;

    protected $weighingService;

    public function boot(MultiProductWeighingService $weighingService)
    {
        $this->weighingService = $weighingService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    /**
     * Open approve modal
     */
    public function openApproveModal($transactionId)
    {
        $this->selectedTransactionId = $transactionId;
        $this->selectedTransaction = TrscaleHeader::with(['details.product'])
            ->findOrFail($transactionId);

        $this->showApproveModal = true;
    }

    /**
     * Open reject modal
     */
    public function openRejectModal($transactionId)
    {
        $this->selectedTransactionId = $transactionId;
        $this->selectedTransaction = TrscaleHeader::with(['details.product'])
            ->findOrFail($transactionId);

        $this->showRejectModal = true;
    }

    /**
     * Approve transaction
     */
    public function approveTransaction()
    {
        try {
            $header = $this->weighingService->approve(
                $this->selectedTransactionId,
                $this->remarks
            );

            session()->flash('success', "Transaksi {$header->trans_no} berhasil di-approve");

            // Reset form
            $this->reset(['selectedTransactionId', 'selectedTransaction', 'remarks', 'showApproveModal']);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Reject transaction
     */
    public function rejectTransaction()
    {
        $this->validate([
            'remarks' => 'required',
        ], [
            'remarks.required' => 'Alasan reject harus diisi',
        ]);

        try {
            $header = $this->weighingService->reject(
                $this->selectedTransactionId,
                $this->remarks
            );

            session()->flash('success', "Transaksi {$header->trans_no} berhasil di-reject");

            // Reset form
            $this->reset(['selectedTransactionId', 'selectedTransaction', 'remarks', 'showRejectModal']);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Re-weigh a rejected transaction
     */
    public function reweighTransaction($transactionId)
    {
        try {
            $header = $this->weighingService->reweigh($transactionId);

            session()->flash('success', "Transaksi {$header->trans_no} berhasil di-reset. Silakan lakukan timbang keluar ulang.");
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * View transaction details
     */
    public function viewDetails($transactionId)
    {
        $this->selectedTransactionId = $transactionId;
        $this->selectedTransaction = TrscaleHeader::with([
            'details.product',
            'userIn',
            'userOut',
            'approver',
            'approvals'
        ])->findOrFail($transactionId);

        $this->showDetailModal = true;
    }

    /**
     * Close approve modal
     */
    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->reset(['selectedTransactionId', 'selectedTransaction', 'remarks']);
    }

    /**
     * Close reject modal
     */
    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->reset(['selectedTransactionId', 'selectedTransaction', 'remarks']);
    }

    /**
     * Close detail modal
     */
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->reset(['selectedTransactionId', 'selectedTransaction']);
    }

    public function render()
    {
        $query = TrscaleHeader::with(['details', 'userOut', 'approver']);

        // Filter by status
        if ($this->filterStatus !== 'ALL') {
            $query->where('status', $this->filterStatus);
        } else {
            $query->whereIn('status', ['PENDING_APPROVAL', 'APPROVED', 'REJECTED']);
        }

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('trans_no', 'like', '%' . $this->search . '%')
                    ->orWhere('carID', 'like', '%' . $this->search . '%')
                    ->orWhere('driver', 'like', '%' . $this->search . '%');
            });
        }

        $transactions = $query->orderBy('weigh_out_time', 'desc')
            ->paginate(10);

        // Get pending count untuk badge
        $pendingCount = TrscaleHeader::where('status', 'PENDING_APPROVAL')->count();

        return view('livewire.multi-product-approval', [
            'transactions' => $transactions,
            'pendingCount' => $pendingCount,
        ]);
    }
}
