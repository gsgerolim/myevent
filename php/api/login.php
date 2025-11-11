<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

$res = login($username, $password);

if ($res['success']) {
    echo json_encode([
        'success' => true,
        'id' => $res['id'],
        'name' => $res['name'],
        'email' => $res['email'] ?? null,
        'type' => $res['type']
    ]);
} else {
    http_response_code(401);
    echo json_encode($res);
}
