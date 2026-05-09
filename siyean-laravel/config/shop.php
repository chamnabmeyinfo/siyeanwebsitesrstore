<?php

return [
    'name' => env('SHOP_NAME', 'SR Mac Shop'),
    'tagline_en' => 'Authentic MacBooks & Apple accessories.',
    'tagline_km' => 'MacBook និងគ្រឿងបន្លាស់ Apple ពិតប្រាកដ.',
    'address' => env('SHOP_ADDRESS', 'Phnom Penh, Cambodia'),
    'phone' => env('SHOP_PHONE', '+855 12 345 678'),
    'whatsapp' => env('SHOP_WHATSAPP', '85512345678'),
    'email' => env('SHOP_EMAIL', 'hello@srmacshop.com'),
    'hours' => env('SHOP_HOURS', 'Mon–Sat 9:00–19:00'),
    'map_url' => env('SHOP_MAP_URL', 'https://maps.google.com/?q=Phnom+Penh'),
    'map_embed' => env('SHOP_MAP_EMBED', 'https://www.google.com/maps?q=Phnom+Penh&output=embed'),
    'currency_symbol' => '$',
    'tax_rate' => (float) env('SHOP_TAX_RATE', 0),
    'low_stock_threshold' => 2,
    'locales' => ['en' => 'EN', 'km' => 'ខ្មែរ'],
    'categories' => [
        'macbook-air' => 'MacBook Air',
        'macbook-pro' => 'MacBook Pro',
        'accessories' => 'Accessories',
        'protection' => 'Protection',
    ],
];
