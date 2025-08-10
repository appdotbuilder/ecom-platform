<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ResellerLevel
 *
 * @property int $id
 * @property string $name
 * @property int $level
 * @property float $discount_percentage
 * @property float $commission_percentage
 * @property float $min_sales_amount
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|ResellerLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResellerLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResellerLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResellerLevel active()
 * @method static \Database\Factories\ResellerLevelFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class ResellerLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'level',
        'discount_percentage',
        'commission_percentage',
        'min_sales_amount',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'min_sales_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users at this reseller level.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope a query to only include active reseller levels.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}