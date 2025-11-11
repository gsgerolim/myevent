<?php
require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin(); 
$pdo = getPDO();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
  echo json_encode(['success' => false, 'message' => 'ID inválido']);
  exit;
}

$stmt = $pdo->prepare("SELECT id, title, link, display_time, active, image FROM ads WHERE id = ?");
$stmt->execute([$id]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ad) {
  echo json_encode(['success' => true, 'ad' => $ad]);
} else {
  echo json_encode(['success' => false, 'message' => 'Propaganda não encontrada']);
}
