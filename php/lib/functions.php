<?php
// php/lib/functions.php

function json_response($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function resize_image($src, $dest, $max_width, $max_height) {
    list($width, $height, $type) = getimagesize($src);

    $ratio = $width / $height;
    if ($max_width / $max_height > $ratio) {
        $new_height = $max_height;
        $new_width = $max_height * $ratio;
    } else {
        $new_width = $max_width;
        $new_height = $max_width / $ratio;
    }

    $dst = imagecreatetruecolor($new_width, $new_height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($src);
            break;
        case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($src);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            break;
        default:
            return false;
    }

    imagecopyresampled($dst, $src_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst, $dest, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($dst, $dest);
            break;
    }

    imagedestroy($dst);
    imagedestroy($src_img);
    return true;
}
