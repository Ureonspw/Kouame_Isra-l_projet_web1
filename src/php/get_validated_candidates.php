<?php
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

try {
    // Modification de la requête pour plus de détails
    $query = "SELECT 
                c.id, 
                c.nom, 
                c.prenoms, 
                u.email,
                i.statut,
                i.date_inscription
              FROM CANDIDAT c
              INNER JOIN UTILISATEUR u ON c.utilisateur_id = u.id
              INNER JOIN INSCRIPTION i ON c.id = i.candidat_id
              WHERE i.statut = 'valide'
              ORDER BY c.nom, c.prenoms";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($candidates)) {
        // Log pour le débogage
        error_log("Aucun candidat trouvé avec le statut 'valide'");
        echo json_encode([]);
    } else {
        error_log("Nombre de candidats trouvés : " . count($candidates));
        echo json_encode($candidates);
    }
} catch(PDOException $e) {
    error_log("Erreur SQL : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la récupération des candidats',
        'details' => $e->getMessage()
    ]);
}
?> 