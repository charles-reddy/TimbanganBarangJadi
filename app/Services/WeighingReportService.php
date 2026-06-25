<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WeighingReportService
{
    /**
     * Get all weighing transactions (single + multi product)
     * Menggunakan database view v_all_weighing_transactions
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTransactions(array $filters = [], int $perPage = 20)
    {
        $query = DB::connection('sqlsrv')
            ->table('v_all_weighing_transactions')
            ->orderBy('weigh_in_time', 'desc');

        // Filter by trans_type
        if (isset($filters['trans_type']) && in_array($filters['trans_type'], ['SINGLE', 'MULTI'])) {
            $query->where('trans_type', $filters['trans_type']);
        }

        // Filter by date range
        if (isset($filters['date_from'])) {
            $query->whereDate('weigh_in_time', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('weigh_in_time', '<=', $filters['date_to']);
        }

        // Filter by specific date
        if (isset($filters['date'])) {
            $query->whereDate('weigh_in_time', $filters['date']);
        }

        // Filter by carID (truck)
        if (isset($filters['carID']) && !empty($filters['carID'])) {
            $query->where('carID', 'like', '%' . $filters['carID'] . '%');
        }

        // Filter by driver
        if (isset($filters['driver']) && !empty($filters['driver'])) {
            $query->where('driver', 'like', '%' . $filters['driver'] . '%');
        }

        // Filter by custID
        if (isset($filters['custID']) && !empty($filters['custID'])) {
            $query->where('custID', $filters['custID']);
        }

        // Filter by itemCode (untuk single product)
        if (isset($filters['itemCode']) && !empty($filters['itemCode'])) {
            $query->where('itemCode', 'like', '%' . $filters['itemCode'] . '%');
        }

        // Filter by status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by need_approval
        if (isset($filters['need_approval'])) {
            $query->where('need_approval', $filters['need_approval']);
        }

        // Search by trans_no
        if (isset($filters['trans_no']) && !empty($filters['trans_no'])) {
            $query->where('trans_no', 'like', '%' . $filters['trans_no'] . '%');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get summary statistics untuk dashboard
     * 
     * @param array $filters
     * @return array
     */
    public function getSummary(array $filters = [])
    {
        $query = DB::connection('sqlsrv')
            ->table('v_all_weighing_transactions');

        // Apply date filter
        if (isset($filters['date_from'])) {
            $query->whereDate('weigh_in_time', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('weigh_in_time', '<=', $filters['date_to']);
        }

        // Get counts by trans_type
        $singleCount = (clone $query)->where('trans_type', 'SINGLE')->count();
        $multiCount = (clone $query)->where('trans_type', 'MULTI')->count();

        // Get total weight
        $totalNetWeight = (clone $query)->sum('net_weight');

        // Get pending approval count
        $pendingApprovalCount = (clone $query)
            ->where('need_approval', 1)
            ->where('status', 'PENDING_APPROVAL')
            ->count();

        // Get completed count
        $completedCount = (clone $query)
            ->whereIn('status', ['COMPLETED', 'APPROVED'])
            ->count();

        // Get average correction factor untuk multi product
        $avgCorrectionFactor = DB::connection('sqlsrv')
            ->table('v_all_weighing_transactions')
            ->where('trans_type', 'MULTI')
            ->whereNotNull('correction_factor');

        if (isset($filters['date_from'])) {
            $avgCorrectionFactor->whereDate('weigh_in_time', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $avgCorrectionFactor->whereDate('weigh_in_time', '<=', $filters['date_to']);
        }

        $avgCorrectionFactor = $avgCorrectionFactor->avg('correction_factor');

        return [
            'total_transactions' => $singleCount + $multiCount,
            'single_product_count' => $singleCount,
            'multi_product_count' => $multiCount,
            'total_net_weight' => round($totalNetWeight, 2),
            'pending_approval_count' => $pendingApprovalCount,
            'completed_count' => $completedCount,
            'avg_correction_factor' => round($avgCorrectionFactor ?? 0, 4),
        ];
    }

    /**
     * Get detail transaksi by trans_no
     * Untuk multi product, include detail per product
     * 
     * @param string $transNo
     * @return array|null
     */
    public function getDetailByTransNo(string $transNo)
    {
        $header = DB::connection('sqlsrv')
            ->table('v_all_weighing_transactions')
            ->where('trans_no', $transNo)
            ->first();

        if (!$header) {
            return null;
        }

        $result = (array) $header;

        // Jika multi product, ambil detail per product
        if ($header->trans_type === 'MULTI') {
            $details = DB::connection('sqlsrv')
                ->table('trscale_details')
                ->where('header_id', $header->id)
                ->get();

            $result['details'] = $details;
        }

        return $result;
    }

    /**
     * Get today's transactions
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getTodayTransactions()
    {
        return DB::connection('sqlsrv')
            ->table('v_all_weighing_transactions')
            ->whereDate('weigh_in_time', Carbon::today())
            ->orderBy('weigh_in_time', 'desc')
            ->get();
    }

    /**
     * Get transactions by date
     * 
     * @param string $date
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionsByDate(string $date)
    {
        return DB::connection('sqlsrv')
            ->table('v_all_weighing_transactions')
            ->whereDate('weigh_in_time', $date)
            ->orderBy('weigh_in_time', 'desc')
            ->get();
    }

    /**
     * Get transactions by customer
     * 
     * @param string $custID
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTransactionsByCustomer(string $custID, array $filters = [], int $perPage = 20)
    {
        $filters['custID'] = $custID;
        return $this->getAllTransactions($filters, $perPage);
    }

    /**
     * Get transactions by truck (carID)
     * 
     * @param string $carID
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTransactionsByTruck(string $carID, array $filters = [], int $perPage = 20)
    {
        $filters['carID'] = $carID;
        return $this->getAllTransactions($filters, $perPage);
    }

    /**
     * Export data untuk Excel/PDF
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function exportData(array $filters = [])
    {
        $query = DB::connection('sqlsrv')
            ->table('v_all_weighing_transactions')
            ->orderBy('weigh_in_time', 'desc');

        // Apply same filters as getAllTransactions
        if (isset($filters['trans_type']) && in_array($filters['trans_type'], ['SINGLE', 'MULTI'])) {
            $query->where('trans_type', $filters['trans_type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('weigh_in_time', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('weigh_in_time', '<=', $filters['date_to']);
        }

        if (isset($filters['date'])) {
            $query->whereDate('weigh_in_time', $filters['date']);
        }

        if (isset($filters['carID']) && !empty($filters['carID'])) {
            $query->where('carID', 'like', '%' . $filters['carID'] . '%');
        }

        if (isset($filters['driver']) && !empty($filters['driver'])) {
            $query->where('driver', 'like', '%' . $filters['driver'] . '%');
        }

        if (isset($filters['custID']) && !empty($filters['custID'])) {
            $query->where('custID', $filters['custID']);
        }

        return $query->get();
    }

    /**
     * Get statistics by product
     * Untuk melihat product mana yang sering out of range
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getProductStatistics(array $filters = [])
    {
        $query = DB::connection('sqlsrv')
            ->table('trscale_details as td')
            ->join('trscale_headers as th', 'th.id', '=', 'td.header_id')
            ->select([
                'td.itemCode',
                'td.itemName',
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN td.is_in_range = 0 THEN 1 ELSE 0 END) as out_of_range_count'),
                DB::raw('AVG(td.avg_per_karung) as avg_weight'),
                DB::raw('AVG(td.weight_std) as standard_weight'),
                DB::raw('MIN(td.gross_min) as min_range'),
                DB::raw('MAX(td.gross_max) as max_range'),
            ])
            ->whereIn('th.status', ['COMPLETED', 'APPROVED']);

        // Apply date filters
        if (isset($filters['date_from'])) {
            $query->whereDate('th.weigh_out_time', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('th.weigh_out_time', '<=', $filters['date_to']);
        }

        return $query->groupBy('td.itemCode', 'td.itemName')
            ->orderBy('out_of_range_count', 'desc')
            ->get();
    }

    /**
     * Get correction factor trends
     * Untuk monitoring apakah correction factor stabil atau berubah-ubah
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getCorrectionFactorTrends(array $filters = [])
    {
        $query = DB::connection('sqlsrv')
            ->table('trscale_headers')
            ->select([
                DB::raw('CAST(weigh_out_time AS DATE) as date'),
                DB::raw('AVG(correction_factor) as avg_factor'),
                DB::raw('MIN(correction_factor) as min_factor'),
                DB::raw('MAX(correction_factor) as max_factor'),
                DB::raw('COUNT(*) as transaction_count'),
            ])
            ->whereNotNull('correction_factor')
            ->whereIn('status', ['COMPLETED', 'APPROVED']);

        // Apply date filters
        if (isset($filters['date_from'])) {
            $query->whereDate('weigh_out_time', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('weigh_out_time', '<=', $filters['date_to']);
        }

        return $query->groupBy(DB::raw('CAST(weigh_out_time AS DATE)'))
            ->orderBy('date', 'desc')
            ->get();
    }
}
