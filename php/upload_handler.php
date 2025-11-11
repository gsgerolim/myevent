<?php
// php/upload_handler.php
require_once __DIR__ . '/lib/functions.php';
$cfg = include __DIR__ . '/config.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Método não permitido']);
    exit;
}
if(empty($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'No file']);
    exit;
}
$up = $_FILES['file'];
if($up['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Upload error']);
    exit;
}

$max_mb = $cfg['max_upload_mb'] ?? 2;
if($up['size'] > $max_mb * 1024 * 1024) {
    http_response_code(413);
    echo json_encode(['success'=>false,'message'=>'File too large']);
    exit;
}

$ext = pathinfo($up['name'], PATHINFO_EXTENSION);
$allowed = ['jpg','jpeg','png'];
if(!in_array(strtolower($ext), $allowed)) {
    http_response_code(415);
    echo json_encode(['success'=>false,'message'=>'Type not allowed']);
    exit;
}

$dest_dir = $cfg['upload_dir'] ?? (__DIR__ . '/../assets/uploads');
if(!is_dir($dest_dir)) {
    if (!mkdir($dest_dir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'Unable to create upload dir']);
        exit;
    }
}

$filename = bin2hex(random_bytes(12)) . '.' . $ext;
$tmp = $up['tmp_name'];
$dest = $dest_dir . '/' . $filename;

if(!move_uploaded_file($tmp, $dest)){
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Failed to move file']);
    exit;
}

// resize (in-place)
resize_image($dest, $dest, 1200, 800);

// return web-accessible path (relative)
$publicPath = 'assets/uploads/' . $filename;
echo json_encode(['success'=>true, 'path'=> $publicPath]);
