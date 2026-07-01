<?php

namespace App\Services;

use App\Models\TrscaleHeader;
use App\Models\TrscaleDetail;
use App\Models\TrscaleApproval;
use App\Models\Createspm;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MultiProductWeighingService
{
    /**
     * Create weigh-in transaction (timbang masuk)
     * 
     * @param array $headerData - Data header (driver, carID, custID, etc)
     * @param array $spmIds - Array of SPM IDs untuk multi product
     * @return TrscaleHeader
     */
    public function createWeighIn(array $headerData, array $spmIds)
    {
        DB::beginTransaction();

        try {
            // Generate transaction number
            $transNo = $this->generateTransNo();

            // Get SPM details for each product
            $spmDetails = Createspm::with(['sppb', 'product'])
                ->whereIn('id', $spmIds)
                ->get();

            if ($spmDetails->isEmpty()) {
                throw new \Exception('SPM tidak ditemukan');
            }

            // Validate all SPMs belong to same truck (carID harus sama, driver boleh berbeda)
            // Normalize: hapus spasi & case-insensitive (B1234CD = b1234cd = B 1234 CD)
            $firstSpm = $spmDetails->first();
            foreach ($spmDetails as $spm) {
                $normalizedSpmCarID = strtolower(preg_replace('/\s+/', '', trim((string) $spm->carID)));
                $normalizedFirstCarID = strtolower(preg_replace('/\s+/', '', trim((string) $firstSpm->carID)));

                if ($normalizedSpmCarID !== $normalizedFirstCarID) {
                    throw new \Exception('Semua SPM harus dari truk/kendaraan yang sama (No. Polisi harus sama)');
                }
            }

            // Create header
            $header = TrscaleHeader::create([
                'trans_no' => $transNo,
                'driver' => $headerData['driver'] ?? $firstSpm->driver,
                'carID' => $headerData['carID'] ?? $firstSpm->carID,
                'custID' => $headerData['custID'] ?? $firstSpm->custID,
                'custName' => $headerData['custName'] ?? $firstSpm->customer->custName ?? null,
                'transpID' => $headerData['transpID'] ?? $firstSpm->transpID,
                'transpName' => $headerData['transpName'] ?? $firstSpm->transporter->transpName ?? null,
                'doNo' => $headerData['doNo'] ?? null,
                'poNo' => $headerData['poNo'] ?? null,
                'tare_weight' => $headerData['tare_weight'],
                'weigh_in_time' => now(),
                'user_in_id' => Auth::id(),
                'status' => 'WEIGHING_IN',
                'remarks' => $headerData['remarks'] ?? null,
            ]);

            // Create detail untuk setiap product
            $totalTheoreticalWeight = 0;

            foreach ($spmDetails as $spm) {
                $product = $spm->product;

                if (!$product) {
                    throw new \Exception("Product tidak ditemukan untuk SPM: {$spm->spmNo}");
                }

                // Calculate theoretical weight = qty_karung × weight_std
                $theoreticalWeight = $spm->qtyKarung * $product->weight_std;
                $totalTheoreticalWeight += $theoreticalWeight;

                TrscaleDetail::create([
                    'header_id' => $header->id,
                    'spm_id' => $spm->id,
                    'sppb_id' => $spm->sppbNo,
                    'itemCode' => $product->itemCode,
                    'itemName' => $product->itemName,
                    'qty_karung' => $spm->qtyKarung,
                    'weight_std' => $product->weight_std,
                    'gross_min' => $product->gross_min,
                    'gross_max' => $product->gross_max,
                    'theoretical_weight' => $theoreticalWeight,
                    // actual_weight dan avg_per_karung akan dihitung saat weigh out
                ]);
            }

            // Update total theoretical weight di header
            $header->update([
                'theoretical_weight' => $totalTheoreticalWeight,
            ]);

            DB::commit();

            return $header->load('details');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process weigh-out transaction (timbang keluar)
     * Calculate correction factor and validate ranges
     * 
     * @param int $headerId
     * @param float $grossWeight
     * @return TrscaleHeader
     */
    public function processWeighOut(int $headerId, float $grossWeight)
    {
        DB::beginTransaction();

        try {
            $header = TrscaleHeader::with('details')->findOrFail($headerId);

            // Validate status
            if ($header->status !== 'WEIGHING_IN') {
                throw new \Exception('Status transaksi harus WEIGHING_IN untuk melakukan timbang keluar');
            }

            // Calculate net weight
            $netWeight = $grossWeight - $header->tare_weight;

            if ($netWeight <= 0) {
                throw new \Exception('Net weight harus lebih besar dari 0');
            }

            // Calculate correction factor: K = net_weight / theoretical_weight
            $correctionFactor = $header->theoretical_weight > 0
                ? $netWeight / $header->theoretical_weight
                : 0;

            // Update header
            $header->update([
                'gross_weight' => $grossWeight,
                'net_weight' => $netWeight,
                'correction_factor' => $correctionFactor,
                'weigh_out_time' => now(),
                'user_out_id' => Auth::id(),
                'status' => 'WEIGHING_OUT',
            ]);

            // Calculate total range min dan max dari semua produk
            $totalRangeMin = 0;
            $totalRangeMax = 0;
            
            foreach ($header->details as $detail) {
                // Total range min = sum of (qty_karung × gross_min)
                $totalRangeMin += $detail->qty_karung * $detail->gross_min;
                
                // Total range max = sum of (qty_karung × gross_max)
                $totalRangeMax += $detail->qty_karung * $detail->gross_max;
            }

            // Check if net_weight is within total range
            $isInRange = ($netWeight >= $totalRangeMin) && ($netWeight <= $totalRangeMax);
            $needApproval = !$isInRange;

            // Calculate actual weight untuk setiap detail (untuk display)
            $outOfRangeProducts = [];

            foreach ($header->details as $detail) {
                // actual_weight = theoretical_weight × correction_factor
                $actualWeight = $detail->theoretical_weight * $correctionFactor;

                // avg_per_karung = actual_weight / qty_karung (untuk display/pembanding)
                $avgPerKarung = $detail->qty_karung > 0
                    ? $actualWeight / $detail->qty_karung
                    : 0;

                // Update detail
                $detail->update([
                    'actual_weight' => $actualWeight,
                    'avg_per_karung' => $avgPerKarung,
                    'is_in_range' => $isInRange, // Semua detail punya status yang sama (based on total range)
                    'isLoadingDone' => True, // Mark detail as completed
                    'isLoadingDoneDate' => Carbon::now(), // Set loading date
                ]);

                // Jika transaksi out of range, simpan info semua produk untuk approval
                if ($needApproval) {
                    $outOfRangeProducts[] = [
                        'itemCode' => $detail->itemCode,
                        'itemName' => $detail->itemName,
                        'qty_karung' => $detail->qty_karung,
                        'avg_per_karung' => $avgPerKarung,
                        'gross_min' => $detail->gross_min,
                        'gross_max' => $detail->gross_max,
                        'range_min_total' => $detail->qty_karung * $detail->gross_min,
                        'range_max_total' => $detail->qty_karung * $detail->gross_max,
                    ];
                }
            }

            // Tambahkan informasi range total ke header untuk tracking
            $header->update([
                'total_range_min' => $totalRangeMin,
                'total_range_max' => $totalRangeMax,
            ]);

            // Update status berdasarkan apakah perlu approval
            if ($needApproval) {
                $header->update([
                    'need_approval' => true,
                    'status' => 'PENDING_APPROVAL',
                ]);
            } else {
                $header->update([
                    'need_approval' => false,
                    'status' => 'COMPLETED',
                ]);
            }

            DB::commit();

            return $header->fresh(['details']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Approve transaction yang out of range
     * 
     * @param int $headerId
     * @param string $remarks
     * @return TrscaleHeader
     */
    public function approve(int $headerId, string $remarks = null)
    {
        DB::beginTransaction();

        try {
            $header = TrscaleHeader::with('details')->findOrFail($headerId);

            // Validate status
            if ($header->status !== 'PENDING_APPROVAL') {
                throw new \Exception('Transaksi harus dalam status PENDING_APPROVAL untuk di-approve');
            }

            // Get out of range products
            $outOfRangeProducts = [];
            foreach ($header->details as $detail) {
                if (!$detail->is_in_range) {
                    $outOfRangeProducts[] = [
                        'itemCode' => $detail->itemCode,
                        'itemName' => $detail->itemName,
                        'avg_per_karung' => $detail->avg_per_karung,
                        'gross_min' => $detail->gross_min,
                        'gross_max' => $detail->gross_max,
                        'deviation' => $detail->deviation,
                        'deviation_percent' => $detail->deviation_percent,
                    ];
                }
            }

            // Create approval record
            TrscaleApproval::create([
                'header_id' => $header->id,
                'approver_id' => Auth::id(),
                'action' => 'APPROVED',
                'remarks' => $remarks,
                'out_of_range_products' => $outOfRangeProducts,
                'approved_at' => now(),
            ]);

            // Update header
            $header->update([
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'status' => 'APPROVED',
            ]);

            DB::commit();

            return $header->fresh(['details', 'approvals']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reject transaction yang out of range
     * 
     * @param int $headerId
     * @param string $remarks
     * @return TrscaleHeader
     */
    public function reject(int $headerId, string $remarks = null)
    {
        DB::beginTransaction();

        try {
            $header = TrscaleHeader::with('details')->findOrFail($headerId);

            // Validate status
            if ($header->status !== 'PENDING_APPROVAL') {
                throw new \Exception('Transaksi harus dalam status PENDING_APPROVAL untuk di-reject');
            }

            // Get out of range products
            $outOfRangeProducts = [];
            foreach ($header->details as $detail) {
                if (!$detail->is_in_range) {
                    $outOfRangeProducts[] = [
                        'itemCode' => $detail->itemCode,
                        'itemName' => $detail->itemName,
                        'avg_per_karung' => $detail->avg_per_karung,
                        'gross_min' => $detail->gross_min,
                        'gross_max' => $detail->gross_max,
                        'deviation' => $detail->deviation,
                        'deviation_percent' => $detail->deviation_percent,
                    ];
                }
            }

            // Create approval record
            TrscaleApproval::create([
                'header_id' => $header->id,
                'approver_id' => Auth::id(),
                'action' => 'REJECTED',
                'remarks' => $remarks,
                'out_of_range_products' => $outOfRangeProducts,
                'approved_at' => now(),
            ]);

            // Update header
            $header->update([
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'status' => 'REJECTED',
            ]);

            DB::commit();

            return $header->fresh(['details', 'approvals']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate transaction number dengan format TRX/YYYY/MM/0001
     * 
     * @return string
     */
    public function generateTransNo(): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');

        // Get last transaction number untuk bulan ini
        $lastTrans = TrscaleHeader::where('trans_no', 'like', "TRX/{$year}/{$month}/%")
            ->orderBy('trans_no', 'desc')
            ->first();

        if ($lastTrans) {
            // Extract nomor urut dari trans_no
            $parts = explode('/', $lastTrans->trans_no);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: TRX/YYYY/MM/0001
        return sprintf('TRX/%s/%s/%04d', $year, $month, $newNumber);
    }

    /**
     * Get transaction by trans_no
     * 
     * @param string $transNo
     * @return TrscaleHeader|null
     */
    public function getByTransNo(string $transNo)
    {
        return TrscaleHeader::with(['details', 'approvals', 'userIn', 'userOut', 'approver'])
            ->where('trans_no', $transNo)
            ->first();
    }

    /**
     * Get pending approval transactions
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingApprovals()
    {
        return TrscaleHeader::with(['details'])
            ->needingApproval()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cancel transaction (only if status is WEIGHING_IN or PENDING_APPROVAL)
     * 
     * @param int $headerId
     * @param string $reason
     * @return bool
     */
    public function cancelTransaction(int $headerId, string $reason = null)
    {
        DB::beginTransaction();

        try {
            $header = TrscaleHeader::findOrFail($headerId);

            // Validate status
            if (!in_array($header->status, ['WEIGHING_IN', 'PENDING_APPROVAL'])) {
                throw new \Exception('Hanya transaksi dengan status WEIGHING_IN atau PENDING_APPROVAL yang bisa dibatalkan');
            }

            // Delete details
            $header->details()->delete();

            // Delete header
            $header->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Re-weigh a rejected transaction
     * Reset status to WEIGHING_IN so it can be weighed out again
     * 
     * @param int $headerId
     * @return TrscaleHeader
     */
    public function reweigh(int $headerId)
    {
        DB::beginTransaction();

        try {
            $header = TrscaleHeader::with('details')->findOrFail($headerId);

            // Validate status
            if ($header->status !== 'REJECTED') {
                throw new \Exception('Hanya transaksi dengan status REJECTED yang bisa di-reweigh');
            }

            // Reset header data
            $header->update([
                'gross_weight' => null,
                'net_weight' => null,
                'correction_factor' => null,
                'scale_out_id' => null,
                'weigh_out_time' => null,
                'user_out_id' => null,
                'status' => 'WEIGHING_IN',
                'need_approval' => false,
                'approved_by' => null,
                'approved_at' => null,
                'approval_note' => null,
            ]);

            // Reset detail calculations
            foreach ($header->details as $detail) {
                $detail->update([
                    'actual_weight' => null,
                    'avg_per_karung' => null,
                    'is_in_range' => null,
                    'need_approval' => false,
                ]);
            }

            // Keep approval history (untuk audit trail)
            // Tidak menghapus TrscaleApproval records

            DB::commit();

            return $header->fresh(['details']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
