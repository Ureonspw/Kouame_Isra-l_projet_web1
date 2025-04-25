<?php
require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit();
}

try {
    $query = "SELECT 
                r.*,
                c.nom,
                c.prenoms,
                co.nom as concours_nom,
                sc.date_ouverture,
                sc.date_cloture,
                ce.ville as centre_ville,
                ce.lieu as centre_lieu
              FROM RESULTAT r
              JOIN INSCRIPTION i ON r.inscription_id = i.id
              JOIN CANDIDAT c ON i.candidat_id = c.id
              JOIN SESSION_CONCOURS sc ON i.session_id = sc.id
              JOIN CONCOURS co ON sc.concours_id = co.id
              LEFT JOIN CENTRE_EXAMEN ce ON i.centre_id = ce.id
              WHERE r.id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        http_response_code(404);
        echo json_encode(['error' => 'Résultat non trouvé']);
        exit();
    }
    
    header('Content-Type: application/json');
    echo json_encode($result);
    
} catch(PDOException $e) {
    error_log("Erreur SQL: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 