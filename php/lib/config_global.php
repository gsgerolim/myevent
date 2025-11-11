<?php
// php/lib/config_global.php

// Configurações globais do sistema
$config = [
    'site_title'    => 'EventHub',
    'page_title'    => 'EventHub - Gerenciamento de Eventos',
    'favicon_path'  => 'assets/uploads/assets/favicon.png',
    'logo_path'     => 'assets/uploads/logo.png',

    // Temas (JSON string para facilitar edição no DB)
    'theme_light'   => json_encode([
        'primary'   => '#0d6efd',
        'secondary' => '#6c757d',
        'background'=> '#ffffff',
        'text'      => '#212529',
    ]),
    'theme_dark'    => json_encode([
        'primary'   => '#0d6efd',
        'secondary' => '#6c757d',
        'background'=> '#121212',
        'text'      => '#f8f9fa',
    ]),
];
