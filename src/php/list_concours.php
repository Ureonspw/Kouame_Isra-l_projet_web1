<?php
require_once '../../config/database.php';

try {
    // Récupérer la liste des concours avec les informations du domaine
    $stmt = $conn->query("
        SELECT c.id, c.nom, c.description, c.niveau_requis, c.categorie, 
               c.ministere, c.created_at, d.nom as domaine_nom
        FROM CONCOURS c
        LEFT JOIN DOMAINE d ON c.domaine_id = d.id
        ORDER BY c.created_at DESC
    ");
    
    $concours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Retourner les concours au format JSON
    echo json_encode($concours);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des concours']);
}
?> 