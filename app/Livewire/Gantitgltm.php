<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Gantitgltm extends Component
{
    use WithPagination;
    public $katakunci;
    #[Validate('required', message: 'Pilih Tiket Muat dari Customer')]
    public $transID;
    public $sppbNo;
    public $sppbID;
    #[Validate('required', message: 'Tgl Muat diisi')]
    public $tglMuat;
    public $tglMuat1;
    public $jamMuat;
    public $jamMuat1;
    #[Validate('required', message: 'Shift harus dipilih')]
    public $shift;
    public $custName;
    public $pendfNo;
    public $tmCarID;
    public $tmDriver;
    public $tmQtyKg;
    public $ip;
    public $quotaInfo;
    public $quotaSufficient = false;
    public $productType;

    public function store()
    {

        $this->validate();

        try {
            // Skip quota validation for FG-L type products
            if ($this->productType == 'FG-L') {
                // Langsung update tanpa validasi quota
                $jamMuat = $this->convertShiftToJamMuat();
                
                DB::connection('sqlsrv')->table('create_t_m_s')
                    ->where('id', $this->transID)
                    ->update([
                        'tglMuat' => $this->tglMuat,
                        'jamMuat' => $jamMuat,
                        'updated_at' => now()
                    ]);

                // Log perubahan
                DB::connection('sqlsrv')->table('tbl_log_rubah_tglMuat')->insert([
                    'transID' => $this->transID,
                    'tglMuat' => $this->tglMuat,
                    'tglMuat1' => $this->tglMuat1,
                    'jamMuat' => $jamMuat,
                    'jamMuat1' => $this->jamMuat1,
                    'updatedBy' => Auth::user()->id,
                    'created_at' => now()
                ]);

                session()->flash('message', 'Data berhasil diubah (Produk FG-L - Kuota diabaikan). ' . $this->shift);
                $this->clear();
                return;
            }

            // Validasi shift
            if (!in_array($this->shift, ['Shift 1', 'Shift 2', 'Shift 3'])) {
                session()->flash('error', 'Pilih shift yang valid (Shift 1, 2, atau 3)');
                return;
            }

            // Cek kuota loading untuk tanggal dan shift yang dipilih
            $quotaLoading = $this->getQuotaLoading($this->tglMuat);

            if (!$quotaLoading) {
                session()->flash('error', 'Kuota loading untuk tanggal ' . Carbon::parse($this->tglMuat)->format('d-m-Y') . ' belum dibuat atau belum di-approve');
                return;
            }

            // Tentukan field kuota berdasarkan shift
            $quotaField = match($this->shift) {
                'Shift 1' => 'quota1',
                'Shift 2' => 'quota2',
                'Shift 3' => 'quota3',
                default => null,
            };

            if (!$quotaField) {
                session()->flash('error', 'Shift tidak valid');
                return;
            }

            // Hitung penggunaan shift saat ini
            $currentShiftUsage = $this->getShiftUsage($this->tglMuat, $this->shift, null);
            $quotaShift = $quotaLoading->$quotaField;

            // Cek apakah penambahan ini akan melebihi kuota shift
            if ($currentShiftUsage + $this->tmQtyKg > $quotaShift) {
                $sisaKuota = $quotaShift - $currentShiftUsage;
                session()->flash('error', $this->shift . ' sudah mencapai batas. Kuota: ' . number_format($quotaShift, 0) . ' Kg, Terpakai: ' . number_format($currentShiftUsage, 0) . ' Kg, Sisa: ' . number_format($sisaKuota, 0) . ' Kg');
                return;
            }

            // Cek kuota harian SPPB untuk tanggal baru (optional)
            $quotaHarian = DB::connection('sqlsrv')->table('tbl_QuotaHarian')
                ->where('quotaTmSppbID', $this->sppbID)
                ->where('quotaTglDaftar', $this->tglMuat)
                ->first();

            // Jika ada quota SPPB, validasi
            if ($quotaHarian) {
                // Cek apakah sisa kuota SPPB mencukupi
                if ($quotaHarian->sisaQuotaKg < $this->tmQtyKg) {
                    session()->flash('error', 'Kuota harian SPPB tidak mencukupi. Sisa kuota: ' . number_format($quotaHarian->sisaQuotaKg, 0) . ' Kg');
                    return;
                }
            }
            // Jika tidak ada quota SPPB, skip validasi (dilewati)

            // Kembalikan kuota lama jika tanggal berbeda (jika ada)
            if ($this->tglMuat1 != $this->tglMuat) {
                $quotaLama = DB::connection('sqlsrv')->table('tbl_QuotaHarian')
                    ->where('quotaTmSppbID', $this->sppbID)
                    ->where('quotaTglDaftar', $this->tglMuat1)
                    ->first();

                if ($quotaLama) {
                    DB::connection('sqlsrv')->table('tbl_QuotaHarian')
                        ->where('id', $quotaLama->id)
                        ->increment('sisaQuotaKg', $this->tmQtyKg);
                }
            }

            // Update create_t_m_s
            DB::connection('sqlsrv')->table('create_t_m_s')->where('id', $this->transID)->update([
                'tglMuat' => $this->tglMuat,
                'jamMuat' => $this->jamMuat,
            ]);

            // Kurangi kuota baru jika tanggal berbeda dan quota SPPB ada
            if ($this->tglMuat1 != $this->tglMuat && $quotaHarian) {
                DB::connection('sqlsrv')->table('tbl_QuotaHarian')
                    ->where('id', $quotaHarian->id)
                    ->decrement('sisaQuotaKg', $this->tmQtyKg);
            }

            // Log perubahan
            DB::connection('sqlsrv')->table('tbl_log_rubah_tglMuat')->insert([
                'tmID' => $this->transID,
                'tglMuat' => $this->tglMuat,
                'tglMuat1' => $this->tglMuat1,
                'jamMuat' => $this->jamMuat,
                'jamMuat1' => $this->jamMuat1,
                'shift' => $this->shift,
                'usrID' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);

            $sisaShift = $quotaShift - ($currentShiftUsage + $this->tmQtyKg);
            $successMsg = 'Data berhasil diubah. ' . $this->shift . ' - Sisa kuota shift: ' . number_format($sisaShift, 0) . ' Kg';
            
            // Tambahkan info SPPB jika ada
            if ($quotaHarian) {
                $sisaSppb = $quotaHarian->sisaQuotaKg - $this->tmQtyKg;
                $successMsg .= ' | Sisa SPPB: ' . number_format($sisaSppb, 0) . ' Kg';
            }
            
            session()->flash('message', $successMsg);
            redirect('/gantitgltm');
        } catch (\Throwable $th) {

            session()->flash('error', 'gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function clear(){
        redirect('/gantitgltm');
    }


    public function edit($id)
    {
        $data = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('customers', 'customers.custID', 'create_t_m_s.custID')
            ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
            ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
            ->select(
                'create_t_m_s.id',
                'create_t_m_s.pendfNo',
                'create_t_m_s.tmCarID',
                'create_t_m_s.tmDriver',
                'create_t_m_s.noHPDriver',
                'create_t_m_s.tmTranspName',
                'create_t_m_s.tglMuat',
                'create_t_m_s.jamMuat',
                'create_t_m_s.tmQtyKg',
                'create_t_m_s.tmSppbID',
                'products.itemName',
                'products.type as productType',
                'customers.custName',
                'createsppbs.sppbNo'
            )
            ->where('create_t_m_s.id', $id)
            ->first();

        $this->pendfNo = $data->pendfNo;
        $this->transID = $id;
        $this->custName = $data->custName;
        $this->tglMuat = $data->tglMuat;
        $this->tglMuat1 = $data->tglMuat;
        $this->jamMuat = $data->jamMuat;
        $this->jamMuat1 = $this->jamMuat;
        $this->tmCarID = $data->tmCarID;
        $this->tmDriver = $data->tmDriver;
        $this->tmQtyKg = $data->tmQtyKg;
        $this->sppbID = $data->tmSppbID;
        $this->sppbNo = $data->sppbNo;
        $this->productType = $data->productType;

        // Set shift berdasarkan jamMuat yang ada
        $this->setShiftFromJamMuat();
        
        // Update quota info
        $this->updateQuotaInfo();
    }

    public function updatedShift()
    {
        // Convert shift selection to jamMuat
        $this->convertShiftToJamMuat();
        // Update quota info when shift changes
        $this->updateQuotaInfo();
        // Force re-render
        $this->dispatch('quotaUpdated');
    }

    public function updatedTglMuat()
    {
        // Reset quota sufficient when date changes
        $this->quotaSufficient = false;
        // Update quota info
        $this->updateQuotaInfo();
        // Force re-render
        $this->dispatch('quotaUpdated');
    }

    private function setShiftFromJamMuat()
    {
        if ($this->jamMuat) {
            $jamMuatTime = Carbon::parse($this->jamMuat)->format('H:i:s');
            if ($jamMuatTime >= '08:00:00' && $jamMuatTime < '12:00:00') {
                $this->shift = 'Shift 1';
            } elseif ($jamMuatTime >= '12:00:00' && $jamMuatTime < '16:00:00') {
                $this->shift = 'Shift 2';
            } elseif ($jamMuatTime >= '16:00:00' && $jamMuatTime < '20:00:00') {
                $this->shift = 'Shift 3';
            } else {
                $this->shift = '';
            }
        }
    }

    private function convertShiftToJamMuat()
    {
        // Convert shift selection to jamMuat for database storage
        switch ($this->shift) {
            case 'Shift 1':
                $this->jamMuat = '08:00';
                break;
            case 'Shift 2':
                $this->jamMuat = '12:00';
                break;
            case 'Shift 3':
                $this->jamMuat = '16:00';
                break;
            default:
                $this->jamMuat = '08:00';
        }
    }

    private function updateQuotaInfo()
    {
        // Reset quota sufficient status
        $this->quotaSufficient = false;

        // Skip quota validation for FG-L type products
        if ($this->productType == 'FG-L') {
            $this->quotaSufficient = true;
            $this->quotaInfo = '<div class="text-info"><strong>ℹ️ Produk tipe FG-L</strong><br>Validasi kuota diabaikan</div>';
            return;
        }

        if ($this->tglMuat && $this->shift) {
            // Get quota loading untuk tanggal dan shift yang dipilih
            $quotaLoading = $this->getQuotaLoading($this->tglMuat);

            if ($quotaLoading) {
                // Tentukan field kuota berdasarkan shift
                $quotaField = match($this->shift) {
                    'Shift 1' => 'quota1',
                    'Shift 2' => 'quota2',
                    'Shift 3' => 'quota3',
                    default => null,
                };

                if ($quotaField) {
                    // Calculate shift usage for the selected date and shift
                    $shiftUsage = (float)$this->getShiftUsage($this->tglMuat, $this->shift, null);
                    $quotaShift = (float)$quotaLoading->$quotaField;
                    $tmQty = (float)($this->tmQtyKg ?? 0);
                    
                    // Proyeksi terpakai setelah ditambah qty tiket ini
                    $projectedUsage = $shiftUsage + $tmQty;
                    $sisaKuotaShift = $quotaShift - $shiftUsage;
                    $sisaSetelahSimpan = $quotaShift - $projectedUsage;
                    
                    // Cek apakah quota cukup untuk shift
                    $quotaShiftSufficient = ($sisaKuotaShift >= $tmQty);
                    
                    $quotaSource = $quotaLoading->quotaTglDatang ? 'Tanggal ' . Carbon::parse($quotaLoading->quotaTglDatang)->format('d-m-Y') : 'Default';
                    
                    // Status warna berdasarkan kecukupan quota
                    $statusClass = $quotaShiftSufficient ? 'text-success' : 'text-danger';
                    $statusIcon = $quotaShiftSufficient ? '✓' : '✗';
                    
                    $this->quotaInfo = '<div class="' . $statusClass . '"><strong>' . $statusIcon . ' ' . $this->shift . '</strong><br>' . 
                                       'Kuota (' . $quotaSource . '): ' . number_format($quotaShift, 0) . ' Kg<br>' . 
                                       'Terpakai saat ini: ' . number_format($shiftUsage, 0) . ' Kg<br>' . 
                                       'Proyeksi terpakai: <strong>' . number_format($projectedUsage, 0) . ' Kg</strong> (+ ' . number_format($tmQty, 0) . ' Kg)<br>' . 
                                       'Sisa setelah simpan: <strong>' . number_format($sisaSetelahSimpan, 0) . ' Kg</strong>';
                    
                    // Tambahkan info kuota harian SPPB jika ada (untuk validasi internal saja, tidak ditampilkan)
                    if ($this->sppbID) {
                        $quotaHarian = DB::connection('sqlsrv')->table('tbl_QuotaHarian')
                            ->where('quotaTmSppbID', $this->sppbID)
                            ->where('quotaTglDaftar', $this->tglMuat)
                            ->first();

                        if ($quotaHarian) {
                            $sisaQuotaSppb = (float)$quotaHarian->sisaQuotaKg;
                            // Jika tanggal sama dengan tanggal lama, tambahkan kembali qty tiket ini
                            if ($this->tglMuat == $this->tglMuat1) {
                                $sisaQuotaSppb += $tmQty;
                            }
                            
                            // Cek apakah quota SPPB cukup
                            $quotaSppbSufficient = ($sisaQuotaSppb >= $tmQty);
                            
                            // Set quota sufficient hanya jika KEDUA quota cukup
                            $this->quotaSufficient = (bool)($quotaShiftSufficient && $quotaSppbSufficient);
                        } else {
                            // SPPB quota tidak ada, hanya validasi shift quota
                            // Set quota sufficient dari shift saja
                            $this->quotaSufficient = (bool)$quotaShiftSufficient;
                        }
                    } else {
                        // Jika tidak ada SPPB (belum pilih tiket), hanya cek shift quota
                        $this->quotaSufficient = (bool)$quotaShiftSufficient;
                    }
                    
                    $this->quotaInfo .= '</div>';
                } else {
                    // Invalid shift
                    $this->quotaInfo = '<span class="text-danger">✗ Shift tidak valid</span>';
                    $this->quotaSufficient = false;
                }
            } else {
                $this->quotaInfo = '<span class="text-danger">✗ Kuota loading untuk tanggal ini belum dibuat atau belum di-approve</span>';
                $this->quotaSufficient = false;
            }
        } elseif ($this->tglMuat && !$this->shift) {
            // Tanggal sudah dipilih tapi shift belum
            $this->quotaInfo = '<span class="text-warning">⚠️ Pilih shift untuk melihat ketersediaan kuota</span>';
            $this->quotaSufficient = false;
        } elseif (!$this->tglMuat && $this->shift) {
            // Shift sudah dipilih tapi tanggal belum
            $this->quotaInfo = '<span class="text-warning">⚠️ Pilih tanggal muat untuk melihat ketersediaan kuota</span>';
            $this->quotaSufficient = false;
        } else {
            // Belum pilih keduanya
            $this->quotaInfo = '';
            $this->quotaSufficient = false;
        }
    }

    private function getQuotaLoading($tglMuat)
    {
        // Cari quota loading berdasarkan tanggal
        $quota = DB::connection('sqlsrv')->table('tbl_QuotaLoading')
            ->where('quotaTglDatang', $tglMuat)
            ->where('isApprove', true)
            ->first();

        // Jika tidak ada, ambil yang quotaTglDatang NULL (default)
        if (!$quota) {
            $quota = DB::connection('sqlsrv')->table('tbl_QuotaLoading')
                ->whereNull('quotaTglDatang')
                ->where('isApprove', true)
                ->orderBy('id', 'desc')
                ->first();
        }

        return $quota;
    }

    private function getShiftUsage($tglMuat, $shift, $sppbID = null)
    {
        // Determine time range for the shift
        $timeRanges = [
            'Shift 1' => ['08:00:00', '11:59:59'],
            'Shift 2' => ['12:00:00', '15:59:59'],
            'Shift 3' => ['16:00:00', '19:59:59'],
        ];

        if (!isset($timeRanges[$shift])) {
            return 0;
        }

        [$startTime, $endTime] = $timeRanges[$shift];

        // Calculate total weight for this shift on the selected date
        // EXCLUDE FG-L type products from quota calculation
        $query = DB::connection('sqlsrv')->table('create_t_m_s')
            ->join('products', 'products.itemCode', '=', 'create_t_m_s.itemCode')
            ->where('create_t_m_s.tglMuat', $tglMuat)
            ->whereTime('create_t_m_s.jamMuat', '>=', $startTime)
            ->whereTime('create_t_m_s.jamMuat', '<=', $endTime)
            ->where('create_t_m_s.id', '!=', $this->transID ?? 0) // Exclude current transaction
            ->where('products.type', '<>', 'FG-L'); // Exclude FG-L products

        // Filter by SPPB jika diberikan
        if ($sppbID !== null) {
            $query->where('create_t_m_s.tmSppbID', $sppbID);
        }

        $totalUsage = $query->sum('create_t_m_s.tmQtyKg');

        return $totalUsage ?? 0;
    }


    public function render()
    {

        if ($this->katakunci  != null) {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')
                ->join('customers', 'customers.custID', 'create_t_m_s.custID')
                ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
                ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
                ->leftJoin('tbl_log_rubah_tglMuat', function ($join) {
                    $join->on('tbl_log_rubah_tglMuat.tmID', '=', 'create_t_m_s.id')
                        ->whereRaw('tbl_log_rubah_tglMuat.id = (SELECT TOP 1 id FROM tbl_log_rubah_tglMuat WHERE tmID = create_t_m_s.id ORDER BY id DESC)');
                })
                ->leftJoin('users', 'users.id', '=', 'tbl_log_rubah_tglMuat.usrID')
                ->select('create_t_m_s.id', 'create_t_m_s.pendfNo', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.noHPDriver', 'create_t_m_s.tmTranspName', 'create_t_m_s.tglMuat', 'create_t_m_s.jamMuat', 'products.itemName', 'customers.custName', 'tbl_log_rubah_tglMuat.tglMuat as tglMuatLog', 'tbl_log_rubah_tglMuat.tglMuat1 as tglMuat1Log', 'users.name as updatedBy', 'tbl_log_rubah_tglMuat.created_at')
                ->where('create_t_m_s.pendfNo', 'like', '%' . $this->katakunci . '%')
                ->whereBetween('create_t_m_s.tglMuat', [Carbon::now(), Carbon::now()->addDays(+4)])
                ->orderBy('create_t_m_s.id', 'desc')
                ->paginate(10);
        } else {
            $data = DB::connection('sqlsrv')->table('create_t_m_s')
                ->join('customers', 'customers.custID', 'create_t_m_s.custID')
                ->join('createsppbs', 'createsppbs.id', 'create_t_m_s.tmSppbID')
                ->join('products', 'products.itemCode', 'create_t_m_s.itemCode')
                ->leftJoin('tbl_log_rubah_tglMuat', function ($join) {
                    $join->on('tbl_log_rubah_tglMuat.tmID', '=', 'create_t_m_s.id')
                        ->whereRaw('tbl_log_rubah_tglMuat.id = (SELECT TOP 1 id FROM tbl_log_rubah_tglMuat WHERE tmID = create_t_m_s.id ORDER BY id DESC)');
                })
                ->leftJoin('users', 'users.id', '=', 'tbl_log_rubah_tglMuat.usrID')
                ->select('create_t_m_s.id', 'create_t_m_s.pendfNo', 'create_t_m_s.tmCarID', 'create_t_m_s.tmDriver', 'create_t_m_s.noHPDriver', 'create_t_m_s.tmTranspName', 'create_t_m_s.tglMuat', 'create_t_m_s.jamMuat', 'products.itemName', 'customers.custName', 'tbl_log_rubah_tglMuat.tglMuat as tglMuatLog', 'tbl_log_rubah_tglMuat.tglMuat1 as tglMuat1Log', 'users.name as updatedBy', 'tbl_log_rubah_tglMuat.created_at')
                
                ->orderBy('create_t_m_s.id', 'desc')
                ->paginate(10);
        }
        // $this->ip = request()->ip();
        // dd($data);
        return view('livewire.gantitgltm', ['datatm' => $data]);
    }
}
