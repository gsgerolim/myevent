<?php
// php/api/attend.php
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

// Verifica se já está inscrito
$stmtCheck = $pdo->prepare("SELECT id FROM event_participants WHERE user_id = :user_id AND event_id = :event_id");
$stmtCheck->execute(['user_id' => $user['id'], 'event_id' => $event_id]);
if ($stmtCheck->fetch()) {
    json_response(['success' => false, 'message' => 'Usuário já inscrito'], 409);
}

// Inscreve o usuário
$stmt = $pdo->prepare("INSERT INTO event_participants (user_id, event_id) VALUES (:user_id, :event_id) RETURNING id");
$stmt->execute(['user_id' => $user['id'], 'event_id' => $event_id]);
$inserted = $stmt->fetchColumn();

if ($inserted) {
    json_response(['success' => true]);
} else {
    json_response(['success' => false, 'message' => 'Falha ao inscrever-se'], 500);
}
