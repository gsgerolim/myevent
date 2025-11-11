<?php
// php/api/unsubscribe.php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método não permitido'], 405);
}

$user = getUser();
if (!$user) {
    json_response(['success' => false, 'message' => 'Usuário não logado'], 401);
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$event_id = intval($input['event_id'] ?? 0);

if (!$event_id) {
    json_response(['success' => false, 'message' => 'Parâmetros inválidos'], 400);
}

$pdo = getPDO();

$stmt = $pdo->prepare("DELETE FROM event_participants WHERE user_id = :user_id AND event_id = :event_id RETURNING id");
$stmt->execute(['user_id' => $user['id'], 'event_id' => $event_id]);
$deleted = $stmt->fetchColumn();

if ($deleted) {
    json_response(['success' => true]);
} else {
    json_response(['success' => false, 'message' => 'Não inscrito neste evento'], 404);
}
