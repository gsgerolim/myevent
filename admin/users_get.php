<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';

requireAdmin();

$pdo = getPDO();
$stmt = $pdo->query("SELECT id, username, name, email, type, active, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

json_response(['success' => true, 'users' => $users]);
