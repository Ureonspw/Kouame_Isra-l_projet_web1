<?php
require_once '../../config/database.php';

try {
    // Récupérer la liste des domaines
    $stmt = $conn->query("
        SELECT id, nom, description, created_at 
        FROM DOMAINE 
        ORDER BY nom ASC
    ");
    
    $domaines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Retourner les domaines au format JSON
    echo json_encode($domaines);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des domaines']);
}
?> 