<?php
require_once '../../config/database.php';

// Vérifier si l'ID est fourni
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du domaine requis']);
    exit();
}

try {
    // Récupérer les informations du domaine
    $stmt = $conn->prepare("
        SELECT id, nom, description, created_at 
        FROM DOMAINE 
        WHERE id = ?
    ");
    
    $stmt->execute([$_GET['id']]);
    $domaine = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$domaine) {
        http_response_code(404);
        echo json_encode(['error' => 'Domaine non trouvé']);
        exit();
    }
    
    // Retourner le domaine au format JSON
    echo json_encode($domaine);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération du domaine']);
}
?> 