<?php
require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();

$pdo = getPDO();

$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$link = trim($_POST['link'] ?? '');
$display_time = intval($_POST['display_time'] ?? 5);
$active = isset($_POST['active']) ? 1 : 0;

if ($title === '') {
  echo json_encode(['success' => false, 'message' => 'Título é obrigatório']);
  exit;
}

// Upload da imagem apenas se enviada
$imagePath = null;
if (!empty($_FILES['image']['name'])) {
  $uploadDir = __DIR__ . '/../assets/uploads/';
  
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

  $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
  $fileName = 'ad_' . time() . '.' . $ext;
  $targetFile = $uploadDir . $fileName;

  if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
    echo json_encode(['success' => false, 'message' => 'Falha ao enviar imagem']);
    exit;
  }

  $imagePath = 'assets/uploads/' . $fileName;
}

if ($id > 0) {
  // Atualiza
  if ($imagePath) {
    $stmt = $pdo->prepare("UPDATE ads SET title=?, link=?, display_time=?, active=?, image=? WHERE id=?");
    $ok = $stmt->execute([$title, $link, $display_time, $active, $imagePath, $id]);
  } else {
    $stmt = $pdo->prepare("UPDATE ads SET title=?, link=?, display_time=?, active=? WHERE id=?");
    $ok = $stmt->execute([$title, $link, $display_time, $active, $id]);
  }
  echo json_encode(['success' => $ok, 'message' => $ok ? 'Propaganda atualizada com sucesso' : 'Erro ao atualizar propaganda']);
} else {
  // Insere
  if (!$imagePath) {
    echo json_encode(['success' => false, 'message' => 'Imagem é obrigatória para nova propaganda']);
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO ads (title, link, display_time, active, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
  $ok = $stmt->execute([$title, $link, $display_time, $active, $imagePath]);
  echo json_encode(['success' => $ok, 'message' => $ok ? 'Propaganda criada com sucesso' : 'Erro ao criar propaganda']);
}
