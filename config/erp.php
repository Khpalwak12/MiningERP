<?php

return [
    'contractor_royalty' => [
        'default_rate_per_ton' => (float) env('CONTRACTOR_ROYALTY_RATE_PER_TON', 150),
    ],

    'backup' => [
        'path' => env('ERP_BACKUP_PATH', dirname(base_path()).DIRECTORY_SEPARATOR.'MiningERP-backups'),
        'mysql_bin' => env('ERP_MYSQL_BIN_PATH'),
        'mysql_method' => env('ERP_BACKUP_MYSQL_METHOD'),
        'timeout' => (int) env('ERP_BACKUP_TIMEOUT', 300),
    ],
];
