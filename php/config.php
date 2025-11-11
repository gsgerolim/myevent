<?php
/*return [
    // DB
    'db' => [
        'host' => 'localhost',
        'port' => 5432,
        'dbname' => 'eventhub',
        'user' => 'postgres',
        'pass' => '123',
    ],
    // SMTP
    'smtp' => [
        'host' => 'smtp.example.com',
        'port' => 587,
        'user' => 'user@example.com',
        'pass' => 'password',
        'from_email' => 'no-reply@example.com',
        'from_name' => 'Hotsite Evento',
    ],
    // Uploads
    'upload_dir' => __DIR__ . '/../assets/uploads',
    'max_upload_mb' => 2,
];*/


// config.php

return [
    'db' => [
        'host' => 'aws-1-sa-east-1.pooler.supabase.com',
        'port' => 6543,
        'dbname' => 'postgres',
        'user' => 'postgres.boyatpyjvgoglxddnjpc',
        'pass' => 'Gsg@657259', // coloque a senha real aqui
        'sslmode' => 'require'
    ]
];


