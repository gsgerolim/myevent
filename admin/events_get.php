<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';

requireAdmin();

$pdo = getPDO();


$id = intval($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    json_response(['success' => true, 'event' => $event]);
} else {
    $stmt = $pdo->query("SELECT id, name, summary, date_start, date_end, address, city, capacity, unlimited, cost, created_by FROM events ORDER BY date_start DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


json_response(['success' => true, 'events' => $events]);
