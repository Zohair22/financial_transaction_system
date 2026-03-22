<?php

return [
    'client_id' => env('PLAID_CLIENT_ID'),
    'secret' => env('PLAID_SECRET'),

    'link' => [
        'country_codes' => ['US'],
        'language' => 'en',
        'products' => ['transactions'],
    ],
];
