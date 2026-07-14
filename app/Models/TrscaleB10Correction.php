<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrscaleB10Correction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'trscale_b10_corrections';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'header_id',
        'detail_id',
        'correction_number',
        'old_b10_qty_karung',
        'new_b10_qty_karung',
        'old_avg_per_karung',
        'new_avg_per_karung',
        'reason',
        'corrected_by',
        'corrected_at',
        'bukti_foto_1',
        'bukti_foto_2',
        'bukti_foto_3',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'old_avg_per_karung' => 'decimal:2',
        'new_avg_per_karung' => 'decimal:2',
        'corrected_at' => 'datetime',
    ];

    /**
     * Get the header that owns this correction.
     */
    public function header(): BelongsTo
    {
        return $this->belongsTo(TrscaleHeader::class, 'header_id');
    }

    /**
     * Get the detail that owns this correction.
     */
    public function detail(): BelongsTo
    {
        return $this->belongsTo(TrscaleDetail::class, 'detail_id');
    }

    /**
     * Get the user who made the correction.
     */
    public function corrector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
