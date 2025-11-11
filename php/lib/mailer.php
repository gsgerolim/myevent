<?php
// php/lib/mailer.php
function send_email($to, $subject, $body){
    // Ideal: integrar PHPMailer. Aqui um fallback básico.
    $from = 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $headers  = "From: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    return mail($to, $subject, $body, $headers);
}
