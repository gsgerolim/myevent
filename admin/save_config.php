<?php
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();

require_once __DIR__ . '/../php/lib/db.php';

header('Content-Type: application/json');

$pdo = getPDO();
$response = ['success' => false, 'message' => 'Erro desconhecido'];

try {
    // Recebe dados
    $siteTitle = trim($_POST['site_title'] ?? '');
    $pageTitle = trim($_POST['page_title'] ?? '');
    $themeLight = $_POST['theme_light'] ?? '{}';
    $themeDark  = $_POST['theme_dark'] ?? '{}';

    
    

    // Diretório de uploads
    $uploadDir = __DIR__ . '/../../assets/uploads/';


    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $logoPath = null;
    $faviconPath = null;

    // Função auxiliar para upload
    function handleUpload($fileKey, $prefix, $uploadDir) {
        if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['tmp_name'])) return null;
        $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['png','jpg','jpeg','gif','ico'])) return null;
        $fileName = $prefix . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $destPath)) {
            return 'assets/uploads/' . $fileName;
        }
        return null;
    }

    $logoPath    = handleUpload('logo', 'logo', $uploadDir);
    $faviconPath = handleUpload('favicon', 'favicon', $uploadDir);

    // Atualiza configuração global
    $sql = "UPDATE global_config SET 
                site_title = :site_title,
                page_title = :page_title,
                theme_light = :theme_light::jsonb,
                theme_dark = :theme_dark::jsonb";

    if ($logoPath) $sql .= ", logo_path = :logo_path";
    if ($faviconPath) $sql .= ", favicon_path = :favicon_path";

    $sql .= " WHERE id = 1";

    $stmt = $pdo->prepare($sql);
    $params = [
        ':site_title' => $siteTitle,
        ':page_title' => $pageTitle,
        ':theme_light' => $themeLight,
        ':theme_dark' => $themeDark,
    ];
    if ($logoPath) $params[':logo_path'] = $logoPath;
    if ($faviconPath) $params[':favicon_path'] = $faviconPath;

    $stmt->execute($params);

    $response = ['success' => true, 'message' => 'Configurações salvas com sucesso!'];

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
