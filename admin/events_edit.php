<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';
require_once __DIR__ . '/../php/lib/image_utils.php';

requireAdmin();
header('Content-Type: application/json');

$input = $_POST;
$id = isset($input['id']) ? intval($input['id']) : null;
if (!$id) json_response(['success' => false, 'message' => 'ID inválido'], 400);

$pdo = getPDO();

// === Carregar dados (GET via POST) ===
if (!isset($input['update'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) json_response(['success' => false, 'message' => 'Evento não encontrado'], 404);
    json_response(['success' => true, 'event' => $event]);
}

// === Campos editáveis ===
$fields = [
    'name', 'summary', 'date_start', 'date_end',
    'address', 'city', 'latitude', 'longitude',
    'capacity', 'unlimited', 'cost', 'image'
];

$update = [];
$params = ['id' => $id];

// === Upload direto de imagem ===
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $dir = __DIR__ . '/../assets/uploads/';
    if (!file_exists($dir)) mkdir($dir, 0777, true);

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $newName = 'event_' . $id . '_' . time() . '.' . $ext;
    $target = $dir . $newName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $params['image'] = 'assets/uploads/' . $newName;
        $update[] = "image = :image";
    }
}

// === Atualização dos campos normais ===
foreach ($fields as $f) {
    if (array_key_exists($f, $input)) {
        if ($f === 'unlimited') {
            $params[$f] = ($input[$f] === true || $input[$f] === 'true' || $input[$f] === 1 || $input[$f] === '1') ? 't' : 'f';
        } elseif ($f === 'capacity') {
            $params[$f] = intval($input[$f]);
        } else {
            $params[$f] = $input[$f];
        }
        $update[] = "$f = :$f";
    }
}

// === Upload de imagem cortada (via cropper) ===
if (isset($_FILES['cropped_image']) && $_FILES['cropped_image']['error'] === UPLOAD_ERR_OK) {
    $imagePath = saveImageUpload($_FILES['cropped_image'], 'events', 'evt_');
    $params['image'] = $imagePath;
    if (!in_array('image = :image', $update)) $update[] = 'image = :image';
}

// === Se não há nada para atualizar ===
if (!$update) json_response(['success' => false, 'message' => 'Nenhum dado para atualizar'], 400);

// === Executa UPDATE ===
$sql = "UPDATE events SET " . implode(', ', $update) . " WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

json_response(['success' => true, 'message' => 'Evento atualizado com sucesso!']);
