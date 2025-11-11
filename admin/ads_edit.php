<?php
// php/admin/ads_edit.php
header('Content-Type: application/json');
// php/admin/ads_list.php
require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();

$pdo = getPDO();

$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$link = trim($_POST['link'] ?? '');
$display_time = intval($_POST['display_time'] ?? 5);
$active = isset($_POST['active']) && $_POST['active'] === 'on' ? 1 : 0;
$cropped_image = $_POST['cropped_image'] ?? null;
$image_path = null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Propaganda inválida.']);
    exit;
}

// 🔹 Busca imagem atual
$stmt = $pdo->prepare("SELECT image FROM ads WHERE id = :id");
$stmt->execute(['id' => $id]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current) {
    echo json_encode(['success' => false, 'message' => 'Propaganda não encontrada.']);
    exit;
}
$image_path = $current['image'];

// 🔹 Atualiza imagem se veio nova
if ($cropped_image) {
    try {
        if (preg_match('/^data:image\/(\w+);base64,/', $cropped_image, $type)) {
            $data = substr($cropped_image, strpos($cropped_image, ',') + 1);
            $ext = strtolower($type[1]);
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                throw new Exception('Formato de imagem inválido.');
            }

            $data = base64_decode($data);
            if ($data === false) {
                throw new Exception('Falha ao decodificar imagem.');
            }

            $fileName = 'ad_' . time() . '.' . $ext;
            $filePath = __DIR__ . '/../assets/uploads/' . $fileName;
            file_put_contents($filePath, $data);
            $image_path = 'assets/uploads/' . $fileName;
        } else {
            throw new Exception('Formato de imagem inválido.');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao processar imagem: ' . $e->getMessage()]);
        exit;
    }
}
elseif (!empty($_FILES['image']['tmp_name'])) {
    $upload_dir = __DIR__ . '/../assets/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = 'ad_' . time() . '.' . strtolower($ext);
    $filePath = $upload_dir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        $image_path = 'assets/uploads/' . $fileName;
    }
}

// 🔹 Atualiza registro
$stmt = $pdo->prepare("
    UPDATE ads
    SET title = :title, link = :link, image = :image, active = :active, display_time = :display_time
    WHERE id = :id
");
$ok = $stmt->execute([
    'title' => $title,
    'link' => $link,
    'image' => $image_path,
    'active' => $active,
    'display_time' => $display_time,
    'id' => $id
]);

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Propaganda atualizada com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar propaganda.']);
}
