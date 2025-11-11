<?php
// php/api/get_events.php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

$pdo = getPDO();
$user = getUser();
$userId = $user['id'] ?? null;

// Busca todos os eventos ativos
$stmt = $pdo->prepare("SELECT * FROM events ORDER BY date_start ASC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Adiciona informação de inscrição para cada evento
foreach ($events as &$ev) {
    $evId = $ev['id'];
    $ev['isSubscribed'] = false;
    if ($userId) {
        $check = $pdo->prepare("SELECT 1 FROM event_participants WHERE event_id = :eid AND user_id = :uid");
        $check->execute([':eid' => $evId, ':uid' => $userId]);
        $ev['isSubscribed'] = $check->fetchColumn() ? true : false;
    }
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM event_participants WHERE event_id = :eid");
    $countStmt->execute([':eid' => $evId]);
    $ev['subscribed_count'] = (int)$countStmt->fetchColumn();
}

json_response(['success' => true, 'events' => $events]);
