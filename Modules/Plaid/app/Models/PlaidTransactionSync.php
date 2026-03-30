<?php

namespace Modules\Plaid\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaidTransactionSync extends Model
{
    use HasFactory;

    protected $fillable = [
        'plaid_item_id',
        'cursor',
        'has_more',
        'request_id',
        'added_count',
        'modified_count',
        'removed_count',
        'initiator',
        'summary',
        'synced_at',
    ];

    protected $casts = [
        'has_more' => 'boolean',
        'summary' => 'array',
        'synced_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PlaidItem::class, 'plaid_item_id');
    }
}
