<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrscaleApproval extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'trscale_approvals';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'header_id',
        'action',
        'approved_by',
        'approved_by_name',
        'approval_note',
        'approved_at',
        'out_of_range_products',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'approved_at' => 'datetime',
        'out_of_range_products' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the header that owns this approval.
     */
    public function header(): BelongsTo
    {
        return $this->belongsTo(TrscaleHeader::class, 'header_id');
    }

    /**
     * Get the user who made the approval.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include approved actions.
     */
    public function scopeApproved($query)
    {
        return $query->where('action', 'APPROVED');
    }

    /**
     * Scope a query to only include rejected actions.
     */
    public function scopeRejected($query)
    {
        return $query->where('action', 'REJECTED');
    }

    /**
     * Check if this is an approval action.
     */
    public function isApproved(): bool
    {
        return $this->action === 'APPROVED';
    }

    /**
     * Check if this is a rejection action.
     */
    public function isRejected(): bool
    {
        return $this->action === 'REJECTED';
    }

    /**
     * Get count of out of range products.
     */
    public function getOutOfRangeCountAttribute(): int
    {
        return is_array($this->out_of_range_products)
            ? count($this->out_of_range_products)
            : 0;
    }

    /**
     * Get formatted approval info.
     */
    public function getFormattedInfoAttribute(): string
    {
        $action = $this->isApproved() ? 'Disetujui' : 'Ditolak';
        $by = $this->approved_by_name ?? 'Unknown';
        $date = $this->approved_at ? $this->approved_at->format('d-m-Y H:i') : '-';

        return "{$action} oleh {$by} pada {$date}";
    }
}
