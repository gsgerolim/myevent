<?php
require_once __DIR__ . '/db.php';

$pdo = getPDO();
$stmt = $pdo->query("SELECT * FROM global_config ORDER BY id ASC LIMIT 1");
$config = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$config) {
    $config = [
        'site_title' => 'EventHub',
        'page_title' => 'EventHub - Gerenciamento de Eventos',
        'favicon_path' => 'assets/uploads/favicon.png',
        'logo_path' => 'assets/uploads/logo.png',
        'theme_light' => json_encode([
            'primary' => '#0d6efd',
            'secondary' => '#6c757d',
            'background' => '#ffffff',
            'text' => '#000000',
        ]),
        'theme_dark' => json_encode([
            'primary' => '#0d6efd',
            'secondary' => '#6c757d',
            'background' => '#121212',
            'text' => '#ffffff',
        ]),
    ];
}
