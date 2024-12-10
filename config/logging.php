<?php

return [
    'channels' => [
        'my-package' => [
            'driver' => 'daily',
            'path' => storage_path('logs/my-package.log'),
            'level' => 'debug',
            'days' => 7,
        ],
    ],
];
