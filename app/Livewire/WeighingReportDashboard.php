<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\WeighingReportService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportAllWeighing;
use Carbon\Carbon;

class WeighingReportDashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $dateFrom;
    public $dateTo;
    public $transType = 'ALL'; // ALL, SINGLE, MULTI
    public $search = '';

    protected $weighingReportService;

    public function boot(WeighingReportService $weighingReportService)
    {
        $this->weighingReportService = $weighingReportService;
    }

    public function mount()
    {
        // Default filter: current month
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTransType()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        $fileName = 'Laporan_Timbangan_' . Carbon::now()->format('YmdHis') . '.xlsx';

        return Excel::download(
            new ExportAllWeighing($this->dateFrom, $this->dateTo, $this->transType, $this->search),
            $fileName
        );
    }

    public function render()
    {
        // Build filters array
        $filters = [
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];

        if ($this->transType !== 'ALL') {
            $filters['trans_type'] = $this->transType;
        }

        if (!empty($this->search)) {
            $filters['search'] = $this->search;
        }

        // Get summary statistics
        $summary = $this->weighingReportService->getSummary($filters);

        // Get paginated transactions
        $transactions = $this->weighingReportService->getAllTransactions($filters, 15);

        return view('livewire.weighing-report-dashboard', [
            'summary' => $summary,
            'transactions' => $transactions,
        ]);
    }
}
