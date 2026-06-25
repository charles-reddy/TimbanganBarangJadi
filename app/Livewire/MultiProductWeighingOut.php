<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\MultiProductWeighingService;
use App\Models\TrscaleHeader;
use App\Models\JembatanTimbang;
use GuzzleHttp\Client;
use Exception;

class MultiProductWeighingOut extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Filter
    public $search = '';

    // Selected transaction for weigh-out
    public $selectedTransactionId = null;
    public $selectedTransaction = null;

    // Form data
    public $grossWeight = '';

    // Weighing scale data
    public $timbanganoutID = '';
    public $timbangout = '';
    public $netto = '';
    public $manualMode = false; // Manual input mode when API unavailable

    // Modal control
    public $showModal = false;
    public $showDetailModal = false;

    // Success message
    public $successMessage = '';

    protected $weighingService;

    public function boot(MultiProductWeighingService $weighingService)
    {
        $this->weighingService = $weighingService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedTimbangout($value)
    {
        // Auto-update grossWeight and calculate netto when timbangout changed in manual mode
        if ($this->manualMode && !empty($value) && $this->selectedTransaction) {
            $this->grossWeight = $value;

            // Calculate netto
            $timbangin = $this->selectedTransaction->tare_weight;
            $this->netto = $value - $timbangin;

            if ($this->netto < 0) {
                $this->netto = $timbangin - $value;
            }
        }
    }

    /**
     * Get weight from scale API
     */
    public function timbang()
    {
        $this->timbangout = '';
        $this->netto = '';

        // If manual mode, just return
        if ($this->manualMode) {
            session()->flash('info', 'Mode manual aktif. Silakan input berat secara manual.');
            return;
        }

        if (empty($this->timbanganoutID)) {
            session()->flash('error', 'Pilih timbangan terlebih dahulu');
            return;
        }

        if (!$this->selectedTransaction) {
            session()->flash('error', 'Data transaksi tidak ditemukan');
            return;
        }

        try {
            $iptimbangan = JembatanTimbang::where('timbanganID', '=', $this->timbanganoutID)->value('IP');

            // Ensure timbanganoutID is integer for comparison
            $timbanganID = (int)$this->timbanganoutID;

            switch ($timbanganID) {
                case 1:
                    $data = "http://10.20.1.49:3000/api/weight/SCALE_10";
                    break;
                case 2:
                    $data = "http://10.20.1.49:3000/api/weight/SCALE_09";
                    break;
                case 3:
                    $data = "http://10.20.1.49:3000/api/weight/SCALE_08";
                    break;
                case 5:
                    $data = "http://10.20.1.49:3000/api/weight/SCALE_07";
                    break;
                default:
                    session()->flash('error', 'Timbangan ID ' . $this->timbanganoutID . ' tidak tersedia');
                    return;
            }

            $client = new Client([
                'timeout' => 10,  // 10 second timeout
                'connect_timeout' => 5,  // 5 second connect timeout
                'verify' => false,  // Disable SSL verification if needed
            ]);
            $response = $client->request('GET', $data);
            $content = $response->getBody()->getContents();
            $contentarray = json_decode($content, true);

            // Validate response
            if (!isset($contentarray['weight'])) {
                session()->flash('error', 'Response API tidak valid. Response: ' . $content);
                return;
            }

            $this->timbangout = $contentarray['weight'];
            $this->grossWeight = $this->timbangout; // Auto populate grossWeight

            // Calculate netto
            $timbangin = $this->selectedTransaction->tare_weight;
            $this->netto = $this->timbangout - $timbangin;

            if ($this->netto < 0) {
                $this->netto = $timbangin - $this->timbangout;
            }

            session()->flash('success', 'Berhasil mendapatkan bobot: ' . number_format($this->timbangout, 2) . ' kg, Netto: ' . number_format($this->netto, 2) . ' kg');
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            session()->flash('error', 'Gagal terhubung ke timbangan di: ' . ($data ?? 'N/A') . '. Aktifkan "Mode Manual" untuk input manual.');
            return;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            session()->flash('error', 'Error request ke API: ' . $e->getMessage());
            return;
        } catch (Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
            return;
        }
    }

    /**
     * Open weigh-out modal
     */
    public function openWeighOutModal($transactionId)
    {
        $this->selectedTransactionId = $transactionId;
        $this->selectedTransaction = TrscaleHeader::with(['details.product'])
            ->findOrFail($transactionId);

        $this->showModal = true;
    }

    /**
     * Process weigh-out
     */
    public function processWeighOut()
    {
        $this->validate([
            'grossWeight' => 'required|numeric|min:0',
        ], [
            'grossWeight.required' => 'Gross weight harus diisi',
            'grossWeight.numeric' => 'Gross weight harus berupa angka',
            'grossWeight.min' => 'Gross weight harus lebih dari 0',
        ]);

        // Validate gross > tare
        if ($this->selectedTransaction && $this->grossWeight <= $this->selectedTransaction->tare_weight) {
            session()->flash('error', 'Gross weight harus lebih besar dari tare weight');
            return;
        }

        try {
            $header = $this->weighingService->processWeighOut(
                $this->selectedTransactionId,
                $this->grossWeight
            );

            $netWeight = $header->net_weight;
            $correctionFactor = $header->correction_factor;
            $needApproval = $header->need_approval;

            // Build success message
            $message = "Timbang keluar berhasil! No. Transaksi: {$header->trans_no}<br>";
            $message .= "Net Weight: " . number_format($netWeight, 2) . " kg<br>";
            $message .= "Correction Factor (K): " . number_format($correctionFactor, 4) . "<br>";

            if ($needApproval) {
                $message .= "<span class='text-warning'><strong>⚠️ Transaksi memerlukan approval (ada produk out of range)</strong></span>";
            } else {
                $message .= "<span class='text-success'><strong>✓ Semua produk dalam range, transaksi COMPLETED</strong></span>";
            }

            $this->successMessage = $message;

            // Reset form
            $this->reset([
                'selectedTransactionId',
                'selectedTransaction',
                'grossWeight',
                'timbanganoutID',
                'timbangout',
                'netto',
                'showModal'
            ]);

            session()->flash('success', $this->successMessage);
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
        $this->selectedTransaction = TrscaleHeader::with(['details.product', 'userIn'])
            ->findOrFail($transactionId);

        $this->showDetailModal = true;
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            'selectedTransactionId',
            'selectedTransaction',
            'grossWeight',
            'timbanganoutID',
            'timbangout',
            'netto'
        ]);
    }

    /**
     * Close detail modal
     */
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->reset(['selectedTransactionId', 'selectedTransaction']);
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction($transactionId)
    {
        try {
            $this->weighingService->cancelTransaction($transactionId, 'Dibatalkan dari UI');

            session()->flash('success', 'Transaksi berhasil dibatalkan');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Get transactions yang statusnya WEIGHING_IN (sudah timbang masuk, belum keluar)
        $transactions = TrscaleHeader::with(['details', 'approvals'])
            ->where('status', 'WEIGHING_IN')
            ->where(function ($query) {
                if (!empty($this->search)) {
                    $query->where('trans_no', 'like', '%' . $this->search . '%')
                        ->orWhere('carID', 'like', '%' . $this->search . '%')
                        ->orWhere('driver', 'like', '%' . $this->search . '%');
                }
            })
            ->orderBy('weigh_in_time', 'desc')
            ->paginate(10);

        // Get completed/approved transactions (COMPLETED atau APPROVED) untuk cetak
        $completedTransactions = TrscaleHeader::with(['details.product', 'userIn', 'userOut', 'approvals'])
            ->whereIn('status', ['COMPLETED', 'APPROVED'])
            ->where(function ($query) {
                if (!empty($this->search)) {
                    $query->where('trans_no', 'like', '%' . $this->search . '%')
                        ->orWhere('carID', 'like', '%' . $this->search . '%')
                        ->orWhere('driver', 'like', '%' . $this->search . '%');
                }
            })
            ->orderBy('weigh_out_time', 'desc')
            ->paginate(10, ['*'], 'completedPage');

        // Get scale list
        $timbangan = JembatanTimbang::all();

        return view('livewire.multi-product-weighing-out', [
            'transactions' => $transactions,
            'completedTransactions' => $completedTransactions,
            'timbangan' => $timbangan,
        ]);
    }
}
