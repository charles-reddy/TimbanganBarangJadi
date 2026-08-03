<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\TrscaleHeader;
use App\Models\TrscaleDetail;
use App\Models\TrscaleB10Correction;
use App\Services\MultiProductWeighingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MultiProductKoreksiB10 extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterDate = '';
    public $showModal = false;
    public $selectedHeader = null;

    // Correction data
    public $corrections = [];
    public $correctionReason = '';
    public $buktiFiles = [];

    // Preview calculation
    public $previewCalculation = [];

    protected $weighingService;

    public function boot(MultiProductWeighingService $weighingService)
    {
        $this->weighingService = $weighingService;
    }

    public function mount()
    {
        $this->filterDate = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Open koreksi modal
     */
    public function openKoreksiModal($headerId)
    {
        $this->selectedHeader = TrscaleHeader::with(['details.spm.product', 'details.corrections'])->findOrFail($headerId);

        // Initialize corrections array with current qty
        $this->corrections = [];
        foreach ($this->selectedHeader->details as $detail) {
            $this->corrections[$detail->id] = $detail->b10QtyKarung;
        }

        $this->calculatePreview();
        $this->showModal = true;
    }

    /**
     * Calculate preview when qty changed
     */
    public function updatedCorrections()
    {
        $this->calculatePreview();
    }

    /**
     * Hook when bukti files updated
     */
    public function updatedBuktiFiles($value, $key)
    {
        // Parse key to get detail_id and index (e.g., "123.0" -> detail_id=123, index=0)
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $detailId = $parts[0];
            $index = $parts[1];

            if ($value) {
                // Validate file size (max 2MB per file)
                if ($value->getSize() > 2048 * 1024) {
                    $this->addError("buktiFiles.{$key}", "Ukuran file maksimal 2MB");
                    unset($this->buktiFiles[$detailId][$index]);
                    return;
                }

                // Validate file type
                if (!in_array($value->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                    $this->addError("buktiFiles.{$key}", "File harus berformat JPG atau PNG");
                    unset($this->buktiFiles[$detailId][$index]);
                    return;
                }
            }
        }
    }

    /**
     * Calculate preview of new ranges and averages
     */
    private function calculatePreview()
    {
        if (!$this->selectedHeader) return;

        $totalRangeMin = 0;
        $totalRangeMax = 0;
        $this->previewCalculation = [];

        foreach ($this->selectedHeader->details as $detail) {
            $newQty = (int) ($this->corrections[$detail->id] ?? $detail->b10QtyKarung);

            // Calculate new average
            $actualWeight = (float) $detail->actual_weight;
            $newAvg = $newQty > 0 ? $actualWeight / $newQty : 0;

            // Calculate range
            $grossMin = (float) $detail->gross_min;
            $grossMax = (float) $detail->gross_max;
            $rangeMin = $newQty * $grossMin;
            $rangeMax = $newQty * $grossMax;

            $totalRangeMin += $rangeMin;
            $totalRangeMax += $rangeMax;

            // Check if in range
            $inRange = ($newAvg >= $grossMin) && ($newAvg <= $grossMax);

            $this->previewCalculation[$detail->id] = [
                'newQty' => $newQty,
                'newAvg' => round($newAvg, 2),
                'rangeMin' => round($rangeMin, 2),
                'rangeMax' => round($rangeMax, 2),
                'inRange' => $inRange,
            ];
        }

        // Check if net weight is in total range
        $netWeight = (float) $this->selectedHeader->net_weight;
        $this->previewCalculation['total'] = [
            'totalRangeMin' => round($totalRangeMin, 2),
            'totalRangeMax' => round($totalRangeMax, 2),
            'netWeight' => $netWeight,
            'inRange' => ($netWeight >= $totalRangeMin) && ($netWeight <= $totalRangeMax),
        ];
    }

    /**
     * Save koreksi B10
     */
    public function saveKoreksi()
    {
        // Validation
        $this->validate([
            'correctionReason' => 'required|string|min:10',
        ], [
            'correctionReason.required' => 'Alasan koreksi wajib diisi',
            'correctionReason.min' => 'Alasan koreksi minimal 10 karakter',
        ]);

        // Validate at least one bukti foto is uploaded (mandatory)
        $hasBukti = false;
        $fileCount = 0;

        foreach ($this->selectedHeader->details as $detail) {
            if (isset($this->buktiFiles[$detail->id])) {
                // Count all uploaded files for this detail
                for ($i = 0; $i < 3; $i++) {
                    if (isset($this->buktiFiles[$detail->id][$i]) && $this->buktiFiles[$detail->id][$i]) {
                        $hasBukti = true;
                        $fileCount++;
                    }
                }
            }
        }

        if (!$hasBukti) {
            session()->flash('error', 'Wajib upload minimal 1 foto bukti');
            return;
        }

        // Validate that products with qty changes must have at least foto 1
        foreach ($this->selectedHeader->details as $detail) {
            $newQty = (int) $this->corrections[$detail->id];
            $oldQty = (int) $detail->b10QtyKarung;

            if ($newQty != $oldQty) {
                // This product qty is being corrected, must have at least foto 1
                if (!isset($this->buktiFiles[$detail->id][0])) {
                    session()->flash('error', "Product {$detail->itemName} dikoreksi qtynya tapi belum upload Foto Bukti 1");
                    return;
                }
            }
        }

        DB::beginTransaction();

        try {
            $hasQtyChanges = false;
            $uploadedCount = 0;

            foreach ($this->selectedHeader->details as $detail) {
                $newQty = (int) $this->corrections[$detail->id];
                $oldQty = (int) $detail->b10QtyKarung;

                // Check if qty changed
                $qtyChanged = ($newQty != $oldQty);

                // Initialize variables for correction history
                $oldAvg = 0;
                $newAvg = 0;

                if ($qtyChanged) {
                    $hasQtyChanges = true;
                    $oldAvg = (float) $detail->avg_per_karung;

                    // Save original qty if first correction
                    if (!$detail->b10QtyKarung_original) {
                        $detail->b10QtyKarung_original = $oldQty;
                    }

                    // Update qty karung
                    $detail->b10QtyKarung = $newQty;
                    $detail->b10_correction_count = ($detail->b10_correction_count ?? 0) + 1;
                    $detail->b10_corrected_by = Auth::id();
                    $detail->b10_corrected_at = Carbon::now();

                    // Recalculate avg_per_karung
                    $actualWeight = (float) $detail->actual_weight;
                    $newAvg = $newQty > 0 ? $actualWeight / $newQty : 0;
                    $detail->avg_per_karung = $newAvg;
                }

                // Upload bukti foto (can upload even without qty change)
                if (isset($this->buktiFiles[$detail->id])) {
                    $spmNo = str_replace("/", "-", $detail->spm->spmNo);
                    // Use correction count + 1 for file naming
                    $correctionNum = ($detail->b10_correction_count ?? 0) + 1;

                    // Files are already in array format from separate inputs [0], [1], [2]
                    $files = $this->buktiFiles[$detail->id];

                    $fileNames = [
                        $spmNo . "-koreksike{$correctionNum}-1.jpg",
                        $spmNo . "-koreksike{$correctionNum}-2.jpg",
                        $spmNo . "-koreksike{$correctionNum}-3.jpg",
                    ];

                    // Upload up to 3 files
                    foreach ($files as $index => $file) {
                        if ($index >= 3) break; // Max 3 files

                        if ($file) {
                            $path = $file->storeAs('uploads/koreksi', $fileNames[$index], 'public');
                            $uploadedCount++;

                            // Set to appropriate column
                            if ($index === 0) {
                                $detail->buktiKoreksi1 = 'uploads/koreksi/' . $fileNames[0];
                            } elseif ($index === 1) {
                                $detail->buktiKoreksi2 = 'uploads/koreksi/' . $fileNames[1];
                            } elseif ($index === 2) {
                                $detail->buktiKoreksi3 = 'uploads/koreksi/' . $fileNames[2];
                            }
                        }
                    }
                }

                $detail->save();

                // Create correction history only if qty changed
                if ($qtyChanged) {
                    TrscaleB10Correction::create([
                        'header_id' => $this->selectedHeader->id,
                        'detail_id' => $detail->id,
                        'correction_number' => $detail->b10_correction_count,
                        'old_b10_qty_karung' => $oldQty,
                        'new_b10_qty_karung' => $newQty,
                        'old_avg_per_karung' => $oldAvg,
                        'new_avg_per_karung' => $newAvg,
                        'reason' => $this->correctionReason,
                        'corrected_by' => Auth::id(),
                        'corrected_at' => Carbon::now(),
                        'bukti_foto_1' => $detail->buktiKoreksi1,
                        'bukti_foto_2' => $detail->buktiKoreksi2,
                        'bukti_foto_3' => $detail->buktiKoreksi3,
                    ]);
                }
            }

            // Recalculate total range (always do this, even if no qty changes)
            $totalRangeMin = 0;
            $totalRangeMax = 0;

            foreach ($this->selectedHeader->details->fresh() as $detail) {
                $qtyKarung = (int) $detail->b10QtyKarung;
                $grossMin = (float) $detail->gross_min;
                $grossMax = (float) $detail->gross_max;

                $totalRangeMin += $qtyKarung * $grossMin;
                $totalRangeMax += $qtyKarung * $grossMax;
            }

            // Check range lagi
            $netWeight = (float) $this->selectedHeader->net_weight;
            $isInRange = ($netWeight >= $totalRangeMin) &&
                ($netWeight <= $totalRangeMax);

            $this->selectedHeader->update([
                'total_range_min' => $totalRangeMin,
                'total_range_max' => $totalRangeMax,
            ]);

            // Update status
            if ($isInRange) {
                // Koreksi berhasil, sekarang in range!
                foreach ($this->selectedHeader->details as $detail) {
                    $detail->update(['is_in_range' => true]);
                }

                // Kembali ke status READY_FOR_WEIGH_OUT untuk timbang out lagi
                $this->selectedHeader->update([
                    'status' => 'READY_FOR_WEIGH_OUT',
                    'need_approval' => false,
                    'needs_b10_correction' => false,
                    'correction_submitted' => false,
                ]);

                session()->flash('success', "Koreksi B10 berhasil! Trans No: {$this->selectedHeader->trans_no} sekarang dalam range. Silakan lakukan <strong>Timbang Out</strong> ulang. ({$uploadedCount} foto bukti terupload)");
            } else {
                // Masih out of range
                foreach ($this->selectedHeader->details as $detail) {
                    $avgInRange = ($detail->avg_per_karung >= $detail->gross_min) &&
                        ($detail->avg_per_karung <= $detail->gross_max);
                    $detail->update(['is_in_range' => $avgInRange]);
                }

                session()->flash('warning', "Koreksi B10 berhasil disimpan ({$uploadedCount} foto bukti terupload), namun masih out of range. Silakan koreksi lagi atau submit untuk approval.");
            }

            DB::commit();
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Submit untuk approval tanpa koreksi
     */
    public function submitForApproval()
    {
        // Validation
        $this->validate([
            'correctionReason' => 'required|string|min:10',
        ], [
            'correctionReason.required' => 'Alasan wajib diisi',
            'correctionReason.min' => 'Alasan minimal 10 karakter',
        ]);

        try {
            $this->selectedHeader->update([
                'status' => 'PENDING_APPROVAL',
                'correction_submitted' => true,
                'remarks' => ($this->selectedHeader->remarks ? $this->selectedHeader->remarks . "\n\n" : '') .
                    "Submitted for approval: " . $this->correctionReason,
            ]);

            session()->flash('success', "Trans No: {$this->selectedHeader->trans_no} telah disubmit untuk approval.");
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['selectedHeader', 'corrections', 'correctionReason', 'buktiFiles', 'previewCalculation']);
    }

    public function render()
    {
        $headers = TrscaleHeader::with(['details.spm.product', 'userOut'])
            ->where('status', 'PENDING_B10_CORRECTION')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('trans_no', 'like', '%' . $this->search . '%')
                        ->orWhere('carID', 'like', '%' . $this->search . '%')
                        ->orWhere('driver', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('weigh_out_time', $this->filterDate);
            })
            ->orderBy('weigh_out_time', 'desc')
            ->paginate(10);

        return view('livewire.multi-product-koreksi-b10', [
            'headers' => $headers,
        ]);
    }
}
