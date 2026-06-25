<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrscaleDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'trscale_details';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'header_id',
        'spm_id',
        'sppb_id',
        'itemCode',
        'itemName',
        'itemType',
        'qty_karung',
        'weight_std',
        'gross_min',
        'gross_max',
        'theoretical_weight',
        'actual_weight',
        'avg_per_karung',
        'is_in_range',
        'need_approval',
        'remarks',
        'isLoading',
        'isLoadingDate',
        'isLoadingDone',
        'isLoadingDoneDate',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'qty_karung' => 'integer',
        'weight_std' => 'decimal:2',
        'gross_min' => 'decimal:2',
        'gross_max' => 'decimal:2',
        'theoretical_weight' => 'decimal:2',
        'actual_weight' => 'decimal:2',
        'avg_per_karung' => 'decimal:2',
        'is_in_range' => 'boolean',
        'need_approval' => 'boolean',
    ];

    /**
     * Get the header that owns this detail.
     */
    public function header(): BelongsTo
    {
        return $this->belongsTo(TrscaleHeader::class, 'header_id');
    }

    /**
     * Get the SPM reference.
     */
    public function spm(): BelongsTo
    {
        return $this->belongsTo(Createspm::class, 'spm_id');
    }

    /**
     * Get the SPPB reference.
     */
    public function sppb(): BelongsTo
    {
        return $this->belongsTo(Createsppb::class, 'sppb_id');
    }

    /**
     * Get the product info.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'itemCode', 'itemCode');
    }

    /**
     * Scope a query to only include out of range products.
     */
    public function scopeOutOfRange($query)
    {
        return $query->where('is_in_range', false);
    }

    /**
     * Scope a query to only include products needing approval.
     */
    public function scopeNeedingApproval($query)
    {
        return $query->where('need_approval', true);
    }

    /**
     * Check if this product is in acceptable range.
     */
    public function isInRange(): bool
    {
        return $this->is_in_range ?? false;
    }

    /**
     * Check if this product needs approval.
     */
    public function needsApproval(): bool
    {
        return $this->need_approval ?? false;
    }

    /**
     * Get deviation from standard.
     */
    public function getDeviationAttribute(): ?float
    {
        if (!$this->avg_per_karung || !$this->weight_std) {
            return null;
        }

        return $this->avg_per_karung - $this->weight_std;
    }

    /**
     * Get deviation percentage from standard.
     */
    public function getDeviationPercentAttribute(): ?float
    {
        if (!$this->weight_std || $this->weight_std == 0) {
            return null;
        }

        return ($this->deviation / $this->weight_std) * 100;
    }

    /**
     * Get deviation status (UNDER, OVER, or NORMAL).
     */
    public function getDeviationStatusAttribute(): string
    {
        if ($this->is_in_range) {
            return 'NORMAL';
        }

        if ($this->avg_per_karung < $this->gross_min) {
            return 'UNDER';
        }

        if ($this->avg_per_karung > $this->gross_max) {
            return 'OVER';
        }

        return 'UNKNOWN';
    }

    /**
     * Get difference from min or max boundary.
     */
    public function getBoundaryDiffAttribute(): ?float
    {
        if ($this->is_in_range) {
            return 0;
        }

        if ($this->avg_per_karung < $this->gross_min) {
            return $this->gross_min - $this->avg_per_karung;
        }

        if ($this->avg_per_karung > $this->gross_max) {
            return $this->avg_per_karung - $this->gross_max;
        }

        return null;
    }
}
