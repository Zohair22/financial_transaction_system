<?php

namespace Modules\Plaid\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlaidTransactionsMapped
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<string, mixed>  $mapped
     */
    public function __construct(
        public array $mapped
    ) {}
}
