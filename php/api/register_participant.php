<?php
// php/api/register_participant.php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método não permitido'], 405);
}

if (!$user = getUser()) {
    json_response(['success' => false, 'message' => 'Usuário não logado'], 401);
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$event_id = intval($input['event_id'] ?? 0);

if (!$event_id) {
    json_response(['success' => false, 'message' => 'Evento inválido'], 400);
}

$pdo = getPDO();

// Verifica se já está inscrito
$stmt = $pdo->prepare("SELECT id FROM event_participants WHERE user_id = :user_id AND event_id = :event_id");
$stmt->execute([
    'user_id' => $user['id'],
    'event_id' => $event_id
]);
if ($stmt->fetch()) {
    json_response(['success' => false, 'message' => 'Já inscrito neste evento'], 409);
}

// Insere inscrição
$stmt = $pdo->prepare("
    INSERT INTO event_participants (user_id, event_id)
    VALUES (:user_id, :event_id)
    RETURNING id
");
$stmt->execute([
    'user_id' => $user['id'],
    'event_id' => $event_id
]);

$id = $stmt->fetchColumn();
json_response(['success' => true, 'id' => $id]);
