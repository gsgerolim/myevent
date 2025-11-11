<?php
// php/api/logout.php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success'=>false,'message'=>'Método não permitido'], 405);
}

logout();
json_response(['success'=>true,'message'=>'Deslogado com sucesso']);
