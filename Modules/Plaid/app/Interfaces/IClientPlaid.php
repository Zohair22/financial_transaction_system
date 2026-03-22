<?php

namespace Modules\Plaid\Interfaces;

interface IClientPlaid
{
    /**
     * @return array<string, mixed>|null
     */
    public function postRequest(string $endpoint, array $data): ?array;
}
