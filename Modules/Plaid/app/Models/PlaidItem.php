<?php

namespace Modules\Plaid\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Crypt;
use Modules\User\Models\User;

class PlaidItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plaid_link_token_id',
        'item_id',
        'institution_id',
        'institution_name',
        'webhook',
        'available_products',
        'billed_products',
        'consented_products',
        'error',
        'update_type',
        'status',
        'last_synced_at',
        'cursor',
        'cursor_updated_at',
        'encrypted_access_token',
        'access_token_hash',
        'request_id',
    ];

    protected $casts = [
        'available_products' => 'array',
        'billed_products' => 'array',
        'consented_products' => 'array',
        'error' => 'array',
        'last_synced_at' => 'datetime',
        'cursor_updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function linkToken(): BelongsTo
    {
        return $this->belongsTo(PlaidLinkToken::class, 'plaid_link_token_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(PlaidAccount::class);
    }

    public function transactionSyncs(): HasMany
    {
        return $this->hasMany(PlaidTransactionSync::class);
    }

    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(PlaidTransaction::class, PlaidAccount::class);
    }

    public function getAccessToken(): string
    {
        return (string) Crypt::decryptString($this->encrypted_access_token);
    }
}
