<?php
// php/api/cancel.php
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

// Remove inscrição
$stmt = $pdo->prepare("DELETE FROM event_participants WHERE user_id = :user_id AND event_id = :event_id");
$success = $stmt->execute(['user_id' => $user['id'], 'event_id' => $event_id]);

if ($success) {
    json_response(['success' => true]);
} else {
    json_response(['success' => false, 'message' => 'Falha ao cancelar inscrição'], 500);
}
