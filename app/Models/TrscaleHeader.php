<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrscaleHeader extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'trscale_headers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'trans_no',
        'trans_type',
        'driver',
        'carID',
        'custID',
        'custName',
        'transpID',
        'transpName',
        'doNo',
        'poNo',
        'tare_weight',
        'gross_weight',
        'net_weight',
        'theoretical_weight',
        'correction_factor',
        'scale_in_id',
        'scale_out_id',
        'weigh_in_time',
        'weigh_out_time',
        'user_in_id',
        'user_out_id',
        'status',
        'need_approval',
        'approved_by',
        'approved_at',
        'approval_note',
        'remarks',
        'isLoading',
        'isLoadingDate',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tare_weight' => 'decimal:2',
        'gross_weight' => 'decimal:2',
        'net_weight' => 'decimal:2',
        'theoretical_weight' => 'decimal:2',
        'correction_factor' => 'decimal:6',
        'need_approval' => 'boolean',
        'weigh_in_time' => 'datetime',
        'weigh_out_time' => 'datetime',
        'approved_at' => 'datetime',
        'isLoadingDate' => 'datetime',
    ];

    /**
     * Get the details for this header.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TrscaleDetail::class, 'header_id');
    }

    /**
     * Get the approval history for this header.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(TrscaleApproval::class, 'header_id');
    }

    /**
     * Get the user who handled weigh in.
     */
    public function userIn(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_in_id');
    }

    /**
     * Get the user who handled weigh out.
     */
    public function userOut(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_out_id');
    }

    /**
     * Get the user who approved.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope a query to only include transactions needing approval.
     */
    public function scopeNeedingApproval($query)
    {
        return $query->where('need_approval', true)
            ->where('status', 'PENDING_APPROVAL');
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['COMPLETED', 'APPROVED']);
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, ['COMPLETED', 'APPROVED']);
    }

    /**
     * Check if transaction needs approval.
     */
    public function needsApproval(): bool
    {
        return $this->need_approval && $this->status === 'PENDING_APPROVAL';
    }

    /**
     * Get total product count.
     */
    public function getTotalProductsAttribute(): int
    {
        return $this->details()->count();
    }

    /**
     * Get total karung count.
     */
    public function getTotalKarungAttribute(): int
    {
        return $this->details()->sum('qty_karung');
    }
}
