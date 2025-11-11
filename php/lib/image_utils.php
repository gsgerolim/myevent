<?php
// php/lib/image_utils.php

// php/lib/image_utils.php

function saveImageUpload($file, $folder = 'uploads', $prefix = 'img_', $maxWidth = 1200, $maxHeight = 800) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro no upload da imagem.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) {
        throw new Exception('Formato de imagem inválido.');
    }

    $uploadDir = __DIR__ . '/../../assets/' . $folder . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newName = uniqid($prefix) . '.' . $ext;
    $destPath = $uploadDir . $newName;

    list($width, $height) = getimagesize($file['tmp_name']);
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newW = $width * $ratio;
    $newH = $height * $ratio;

    $src = match ($ext) {
        'jpg', 'jpeg' => imagecreatefromjpeg($file['tmp_name']),
        'png' => imagecreatefrompng($file['tmp_name']),
        'gif' => imagecreatefromgif($file['tmp_name']),
    };

    $dst = imagecreatetruecolor($newW, $newH);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

    match ($ext) {
        'jpg', 'jpeg' => imagejpeg($dst, $destPath, 90),
        'png' => imagepng($dst, $destPath),
        'gif' => imagegif($dst, $destPath),
    };

    imagedestroy($src);
    imagedestroy($dst);

    return 'assets/' . $folder . '/' . $newName;
}
