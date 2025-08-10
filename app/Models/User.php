<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property int|null $reseller_level_id
 * @property int|null $upline_id
 * @property float $total_sales
 * @property float $total_commission_earned
 * @property bool $is_affiliate
 * @property string|null $affiliate_code
 * @property string|null $address
 * @property string|null $phone
 * @property string $user_type
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ResellerLevel|null $resellerLevel
 * @property-read \App\Models\User|null $upline
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $downlines
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShoppingCart[] $cartItems
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $posOrders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Commission[] $commissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PosSession[] $posSessions
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User resellers()
 * @method static \Illuminate\Database\Eloquent\Builder|User affiliates()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'reseller_level_id',
        'upline_id',
        'total_sales',
        'total_commission_earned',
        'is_affiliate',
        'affiliate_code',
        'address',
        'phone',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'total_sales' => 'decimal:2',
            'total_commission_earned' => 'decimal:2',
            'is_affiliate' => 'boolean',
        ];
    }

    /**
     * Get the reseller level for this user.
     */
    public function resellerLevel(): BelongsTo
    {
        return $this->belongsTo(ResellerLevel::class);
    }

    /**
     * Get the upline (parent) reseller.
     */
    public function upline(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upline_id');
    }

    /**
     * Get the downlines (child) resellers.
     */
    public function downlines(): HasMany
    {
        return $this->hasMany(User::class, 'upline_id');
    }

    /**
     * Get the shopping cart items for this user.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(ShoppingCart::class);
    }

    /**
     * Get the orders for this user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the POS orders handled by this user (as reseller).
     */
    public function posOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'reseller_id');
    }

    /**
     * Get the commissions earned by this user.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'reseller_id');
    }

    /**
     * Get the POS sessions for this user.
     */
    public function posSessions(): HasMany
    {
        return $this->hasMany(PosSession::class, 'cashier_id');
    }

    /**
     * Scope a query to only include resellers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeResellers($query)
    {
        return $query->where('user_type', 'reseller');
    }

    /**
     * Scope a query to only include affiliates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAffiliates($query)
    {
        return $query->where('is_affiliate', true);
    }

    /**
     * Get all upline resellers (for commission distribution).
     *
     * @param  int  $maxLevels
     * @return \Illuminate\Support\Collection
     */
    public function getUplineChain(int $maxLevels = 10)
    {
        $uplines = collect();
        $currentUser = $this;
        $level = 1;

        while ($currentUser->upline && $level <= $maxLevels) {
            $uplines->push([
                'user' => $currentUser->upline,
                'level' => $level
            ]);
            $currentUser = $currentUser->upline;
            $level++;
        }

        return $uplines;
    }

    /**
     * Generate a unique affiliate code.
     *
     * @return string
     */
    public static function generateAffiliateCode(): string
    {
        do {
            $code = 'AFF' . strtoupper(substr(hash('sha256', uniqid()), 0, 8));
        } while (self::where('affiliate_code', $code)->exists());

        return $code;
    }
}