<?php

namespace Modules\Plaid\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlaidAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'plaid_item_id',
        'account_id',
        'name',
        'official_name',
        'mask',
        'type',
        'subtype',
        'holder_category',
        'balance_available',
        'balance_current',
        'balance_limit',
        'iso_currency_code',
        'unofficial_currency_code',
        'is_active',
        'last_synced_at',
        'raw_balances',
        'raw',
    ];

    protected $casts = [
        'balance_available' => 'decimal:4',
        'balance_current' => 'decimal:4',
        'balance_limit' => 'decimal:4',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
        'raw_balances' => 'array',
        'raw' => 'array',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PlaidItem::class, 'plaid_item_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PlaidTransaction::class);
    }
}
