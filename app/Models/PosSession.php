<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PosSession
 *
 * @property int $id
 * @property int $cashier_id
 * @property float $opening_cash
 * @property float|null $closing_cash
 * @property float $total_sales
 * @property int $total_transactions
 * @property \Illuminate\Support\Carbon $opened_at
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $cashier
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|PosSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PosSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PosSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|PosSession open()
 * @method static \Database\Factories\PosSessionFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class PosSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cashier_id',
        'opening_cash',
        'closing_cash',
        'total_sales',
        'total_transactions',
        'opened_at',
        'closed_at',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the cashier for this session.
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Scope a query to only include open sessions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}