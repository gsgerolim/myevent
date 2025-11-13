<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();

$data = json_decode(file_get_contents('php://input'), true);
$pdo = getPDO();

if (empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do usuário ausente']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT e.id, e.name, e.city, e.date_start, e.date_end, ep.subscribed_at
    FROM event_participants ep
    JOIN events e ON e.id = ep.event_id
    WHERE ep.user_id = ?
    ORDER BY ep.subscribed_at DESC
");
$stmt->execute([$data['id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'events' => $events]);
