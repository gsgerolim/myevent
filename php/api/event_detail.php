<?php
// php/api/event_detail.php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

$pdo = getPDO();
$user = getUser();
$userId = $user['id'] ?? null;

$input = json_decode(file_get_contents('php://input'), true) ?: $_GET;
$event_id = intval($input['event_id'] ?? 0);

if (!$event_id) {
    json_response(['success' => false, 'message' => 'Evento inválido'], 400);
}

// Busca detalhes do evento
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute([':id' => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    json_response(['success' => false, 'message' => 'Evento não encontrado'], 404);
}

// Verifica inscrição do usuário
$isSubscribed = false;
if ($userId) {
    $stmt2 = $pdo->prepare("SELECT 1 FROM event_participants WHERE event_id = :eid AND user_id = :uid");
    $stmt2->execute([':eid' => $event_id, ':uid' => $userId]);
    $isSubscribed = $stmt2->fetchColumn() ? true : false;
}

$event['isSubscribed'] = $isSubscribed;
$event['subscribed_count'] = (int)($event['subscribed_count'] ?? 0);

json_response(['success' => true, 'event' => $event]);
