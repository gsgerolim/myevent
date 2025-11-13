<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/db.php';
$pdo = getPDO();
$config = $pdo->query("SELECT theme_light, theme_dark FROM global_config WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
    'themeLight' => json_decode($config['theme_light'], true),
    'themeDark'  => json_decode($config['theme_dark'], true)
]);
