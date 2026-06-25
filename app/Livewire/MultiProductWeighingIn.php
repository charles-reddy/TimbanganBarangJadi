<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\MultiProductWeighingService;
use App\Models\Createspm;
use App\Models\TrscaleHeader;
use App\Models\JembatanTimbang;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Exception;

class MultiProductWeighingIn extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Filter
    public $search = '';
    public $filterDate = '';

    // Selected SPMs for weighing
    public $selectedSpms = [];

    // Form data
    public $driver = '';
    public $carID = '';
    public $custID = '';
    public $custName = '';
    public $transpID = '';
    public $transpName = '';
    public $doNo = '';
    public $poNo = '';
    public $tareWeight = '';
    public $remarks = '';

    // Weighing scale data
    public $timbanganID = '';
    public $timbangin = '';
    public $manualMode = false; // Manual input mode when API unavailable

    // Modal control
    public $showModal = false;

    // Success message
    public $successMessage = '';
    public $transNo = '';

    protected $weighingService;

    public function boot(MultiProductWeighingService $weighingService)
    {
        $this->weighingService = $weighingService;
    }

    public function mount()
    {
        $this->filterDate = date('Y-m-d');
    }

    public function updatedTimbangin($value)
    {
        // Auto-update tareWeight when timbangin changed in manual mode
        if ($this->manualMode && !empty($value)) {
            $this->tareWeight = $value;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Toggle SPM selection
     */
    public function toggleSpm($spmId)
    {
        if (in_array($spmId, $this->selectedSpms)) {
            $this->selectedSpms = array_diff($this->selectedSpms, [$spmId]);
        } else {
            if (!empty($this->selectedSpms)) {
                $selectedSpm = Createspm::find($spmId);
                $firstSelectedSpm = Createspm::find($this->selectedSpms[0]);

                if ($selectedSpm && $firstSelectedSpm && (
                    $this->normalizeIdentityValue($selectedSpm->carID) !== $this->normalizeIdentityValue($firstSelectedSpm->carID)
                    || $this->normalizeIdentityValue($selectedSpm->driver) !== $this->normalizeIdentityValue($firstSelectedSpm->driver)
                )) {
                    session()->flash('error', 'Semua SPM harus dari truk dan driver yang sama');
                    return;
                }
            }

            $this->selectedSpms[] = $spmId;
        }
    }

    /**
     * Normalize string value untuk perbandingan
     * Hapus semua spasi dan ubah ke lowercase (B1234CD = b1234cd = B 1234 CD)
     */
    private function normalizeIdentityValue($value): string
    {
        return strtolower(preg_replace('/\s+/', '', trim((string) $value)));
    }

    /**
     * Get weight from scale API
     */
    public function timbang()
    {
        $this->timbangin = '';

        // If manual mode, just return
        if ($this->manualMode) {
            session()->flash('info', 'Mode manual aktif. Silakan input berat secara manual.');
            return;
        }

        if (empty($this->timbanganID)) {
            session()->flash('error', 'Pilih timbangan terlebih dahulu');
            return;
        }

        try {
            $iptimbangan = JembatanTimbang::where('timbanganID', '=', $this->timbanganID)->value('IP');

            // Ensure timbanganID is integer for comparison
            $timbanganID = (int)$this->timbanganID;

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
                    session()->flash('error', 'Timbangan ID ' . $this->timbanganID . ' tidak tersedia');
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

            $this->timbangin = $contentarray['weight'];
            $this->tareWeight = $this->timbangin; // Auto populate tareWeight

            session()->flash('success', 'Berhasil mendapatkan bobot: ' . number_format($this->timbangin, 2) . ' kg');
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
     * Open weigh-in modal with selected SPMs
     */
    public function openWeighInModal()
    {
        if (empty($this->selectedSpms)) {
            session()->flash('error', 'Pilih minimal 1 SPM untuk ditimbang');
            return;
        }

        // Get first SPM untuk populate form
        $firstSpm = Createspm::with(['customer', 'transporter'])->find($this->selectedSpms[0]);

        if ($firstSpm) {
            $this->driver = $firstSpm->driver ?? '';
            $this->carID = $firstSpm->carID ?? '';
            $this->custID = $firstSpm->custID ?? '';
            $this->custName = $firstSpm->customer->custName ?? '';
            $this->transpID = $firstSpm->transpID ?? '';
            $this->transpName = $firstSpm->transporter->transpName ?? '';
        }

        $this->showModal = true;
    }

    /**
     * Process weigh-in
     */
    public function processWeighIn()
    {
        $this->validate([
            'driver' => 'required',
            'carID' => 'required',
            'custID' => 'required',
            'tareWeight' => 'required|numeric|min:0',
        ], [
            'driver.required' => 'Driver harus diisi',
            'carID.required' => 'Nomor kendaraan harus diisi',
            'custID.required' => 'Customer harus diisi',
            'tareWeight.required' => 'Tare weight harus diisi',
            'tareWeight.numeric' => 'Tare weight harus berupa angka',
            'tareWeight.min' => 'Tare weight harus lebih dari 0',
        ]);

        try {
            $headerData = [
                'driver' => $this->driver,
                'carID' => $this->carID,
                'custID' => $this->custID,
                'custName' => $this->custName,
                'transpID' => $this->transpID,
                'transpName' => $this->transpName,
                'doNo' => $this->doNo,
                'poNo' => $this->poNo,
                'tare_weight' => $this->tareWeight,
                'remarks' => $this->remarks,
            ];

            $header = $this->weighingService->createWeighIn($headerData, $this->selectedSpms);

            $this->transNo = $header->trans_no;
            $this->successMessage = "Timbang masuk berhasil! No. Transaksi: {$this->transNo}";

            // Reset form
            $this->reset([
                'selectedSpms',
                'driver',
                'carID',
                'custID',
                'custName',
                'transpID',
                'transpName',
                'doNo',
                'poNo',
                'tareWeight',
                'remarks',
                'showModal'
            ]);

            session()->flash('success', $this->successMessage);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
        redirect()->route('multi-product-weighing-in');
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            'driver',
            'carID',
            'custID',
            'custName',
            'transpID',
            'transpName',
            'doNo',
            'poNo',
            'tareWeight',
            'remarks',
            'timbanganID',
            'timbangin'
        ]);
    }

    /**
     * Clear selection
     */
    public function clearSelection()
    {
        $this->selectedSpms = [];
        redirect()->route('multi-product-weighing-in');
    }

    public function render()
    {
        // Get available SPMs (belum ditimbang atau statusnya registered)
        $spms = Createspm::with(['product', 'customer', 'sppb', 'tiket'])
            ->where(function ($query) {
                if (!empty($this->search)) {
                    $query->where('carID', 'like', '%' . $this->search . '%')
                        ->orWhere('driver', 'like', '%' . $this->search . '%')
                        ->orWhere('spmNo', 'like', '%' . $this->search . '%');
                }
            })
            ->whereDoesntHave('trscaleDetails') // SPM yang belum ada di multi-product weighing
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Get selected SPM details
        $selectedSpmDetails = Createspm::with(['product', 'customer'])
            ->whereIn('id', $this->selectedSpms)
            ->get();

        // Get scale list
        $timbangan = JembatanTimbang::all();

        return view('livewire.multi-product-weighing-in', [
            'spms' => $spms,
            'selectedSpmDetails' => $selectedSpmDetails,
            'timbangan' => $timbangan,
        ]);
    }
}
