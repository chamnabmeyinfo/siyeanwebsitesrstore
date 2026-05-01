<?php

return [
    'notifications' => [
        'low_stock_threshold' => (int) ($_ENV['LOW_STOCK_THRESHOLD'] ?? 2),
        'email' => [
            'enabled' => filter_var($_ENV['MAIL_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'from' => $_ENV['MAIL_FROM'] ?? 'no-reply@example.com',
            'to' => $_ENV['MAIL_TO'] ?? '',
        ],
        'telegram' => [
            'enabled' => filter_var($_ENV['TELEGRAM_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'bot_token' => $_ENV['8238072671:AAGx6kTBhZ-uRaT_0lakEKSvdw28wjZQYuc'] ?? '',
            'chat_id' => $_ENV['559530272'] ?? '',
        ],
    ],
];

