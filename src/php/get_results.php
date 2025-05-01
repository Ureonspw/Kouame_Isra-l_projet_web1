<?php
// Inclusion du fichier de configuration de la base de données
require_once __DIR__ . '/../../config/database.php';

try {
    // Requête SQL pour récupérer les résultats des candidats avec leurs informations associées
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
              ORDER BY r.created_at DESC";
    
    // Préparation et exécution de la requête
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Récupération de tous les résultats sous forme de tableau associatif
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Traitement des résultats : création du nom complet du candidat
    foreach ($results as &$result) {
        $result['nom_candidat'] = $result['nom'] . ' ' . $result['prenoms'];
        // Suppression des champs nom et prénoms individuels
        unset($result['nom'], $result['prenoms']);
    }
    
    // Envoi de la réponse en format JSON
    header('Content-Type: application/json');
    echo json_encode($results);
    
} catch(PDOException $e) {
    // Gestion des erreurs : journalisation et réponse d'erreur
    error_log("Erreur SQL: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 