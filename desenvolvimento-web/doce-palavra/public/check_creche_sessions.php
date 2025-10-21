<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }
    
    try {
        // Verificar se há sessões vinculadas
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reading_sessions WHERE creche_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        
        echo json_encode([
            'hasSessions' => $count > 0,
            'count' => (int)$count
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método não permitido']);
}
?>
