<?php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';
header('Content-Type: application/json');

try {
    $pdo = getPDO();

    $stmt = $pdo->query("
        SELECT id, title, image, link, display_time
        FROM ads
        WHERE active = true
        ORDER BY created_at DESC
    ");
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'ads' => $ads]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
