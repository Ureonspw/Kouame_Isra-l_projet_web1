<?php
require_once '../../config/database.php';

// Vérifier si l'ID est fourni
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du concours requis']);
    exit();
}

try {
    // Récupérer les informations du concours
    $stmt = $conn->prepare("
        SELECT c.id, c.nom, c.description, c.niveau_requis, c.categorie, 
               c.ministere, c.domaine_id, c.created_at, d.nom as domaine_nom
        FROM CONCOURS c
        LEFT JOIN DOMAINE d ON c.domaine_id = d.id
        WHERE c.id = ?
    ");
    
    $stmt->execute([$_GET['id']]);
    $concours = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$concours) {
        http_response_code(404);
        echo json_encode(['error' => 'Concours non trouvé']);
        exit();
    }
    
    // Retourner le concours au format JSON
    echo json_encode($concours);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération du concours']);
}
?> 