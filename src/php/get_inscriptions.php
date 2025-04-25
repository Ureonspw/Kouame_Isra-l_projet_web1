<?php
require_once __DIR__ . '/../../config/database.php';

try {
    // Requête SQL pour récupérer les inscriptions avec les informations des candidats
    $query = "SELECT 
                i.id,
                CONCAT(c.nom, ' ', c.prenoms) as nom_candidat,
                co.nom as concours_nom,
                ce.ville as centre_ville,
                ce.lieu as centre_lieu,
                sc.date_ouverture,
                sc.date_cloture
              FROM INSCRIPTION i
              JOIN CANDIDAT c ON i.candidat_id = c.id
              JOIN SESSION_CONCOURS sc ON i.session_id = sc.id
              JOIN CONCOURS co ON sc.concours_id = co.id
              LEFT JOIN CENTRE_EXAMEN ce ON i.centre_id = ce.id
              WHERE i.statut = 'valide'
              ORDER BY c.nom, c.prenoms";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ajouter des logs pour le débogage
    error_log("Nombre d'inscriptions trouvées: " . count($inscriptions));
    if (count($inscriptions) > 0) {
        error_log("Première inscription: " . json_encode($inscriptions[0]));
    }
    
    header('Content-Type: application/json');
    echo json_encode($inscriptions);
    
} catch(PDOException $e) {
    error_log("Erreur SQL: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 