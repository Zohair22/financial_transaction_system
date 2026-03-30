<?php

namespace Modules\Plaid\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaidTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'plaid_account_id',
        'transaction_id',
        'pending_transaction_id',
        'amount',
        'iso_currency_code',
        'unofficial_currency_code',
        'date',
        'authorized_date',
        'datetime',
        'authorized_datetime',
        'name',
        'merchant_name',
        'merchant_entity_id',
        'payment_channel',
        'pending',
        'transaction_code',
        'transaction_type',
        'account_owner',
        'category_primary',
        'category_detailed',
        'personal_finance_confidence',
        'personal_finance_primary',
        'personal_finance_detailed',
        'logo_url',
        'website',
        'counterparties',
        'location',
        'payment_meta',
        'raw',
        'removed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'date' => 'date',
        'authorized_date' => 'date',
        'datetime' => 'datetime',
        'authorized_datetime' => 'datetime',
        'pending' => 'boolean',
        'counterparties' => 'array',
        'location' => 'array',
        'payment_meta' => 'array',
        'raw' => 'array',
        'removed_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PlaidAccount::class, 'plaid_account_id');
    }
}
