<?php
require_once '../../config/database.php';

try {
    // Récupérer la liste des candidatures avec les informations du concours et du candidat
    $stmt = $conn->query("
        SELECT 
            c.id,
            c.session_concours_id,
            sc.concours_id,
            co.nom as concours_nom,
            c.candidat_id,
            ca.nom as candidat_nom,
            ca.prenom as candidat_prenom,
            ca.email as candidat_email,
            c.statut,
            c.date_soumission,
            c.created_at,
            c.updated_at
        FROM CANDIDATURE c
        JOIN SESSION_CONCOURS sc ON c.session_concours_id = sc.id
        JOIN CONCOURS co ON sc.concours_id = co.id
        JOIN CANDIDAT ca ON c.candidat_id = ca.id
        ORDER BY c.date_soumission DESC
    ");
    
    $candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Retourner les candidatures au format JSON
    echo json_encode($candidatures);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des candidatures']);
}
?> 