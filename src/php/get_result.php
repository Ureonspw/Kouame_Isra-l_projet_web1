<?php
// Inclusion du fichier de configuration de la base de données
require_once __DIR__ . '/../../config/database.php';

// Vérification de la présence de l'ID dans les paramètres GET
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit();
}

try {
    // Requête SQL pour récupérer les détails d'un résultat
    // Jointures avec les tables INSCRIPTION, CANDIDAT, SESSION_CONCOURS, CONCOURS et CENTRE_EXAMEN
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
    
    // Préparation et exécution de la requête avec l'ID fourni
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    
    // Récupération du résultat
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Vérification si un résultat a été trouvé
    if (!$result) {
        http_response_code(404);
        echo json_encode(['error' => 'Résultat non trouvé']);
        exit();
    }
    
    // Envoi de la réponse en JSON
    header('Content-Type: application/json');
    echo json_encode($result);
    
} catch(PDOException $e) {
    // Gestion des erreurs SQL
    error_log("Erreur SQL: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 