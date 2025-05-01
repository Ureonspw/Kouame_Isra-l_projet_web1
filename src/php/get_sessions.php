<?php
// Inclusion du fichier de configuration de la base de données
require_once '../../config/database.php';

try {
    // Requête SQL pour récupérer les sessions de concours avec le nom du concours associé
    // La requête joint la table SESSION_CONCOURS avec la table CONCOURS
    // Les résultats sont triés par date d'ouverture décroissante
    $query = "SELECT s.*, c.nom as concours_nom 
              FROM SESSION_CONCOURS s 
              JOIN CONCOURS c ON s.concours_id = c.id 
              ORDER BY s.date_ouverture DESC";
    
    // Préparation et exécution de la requête
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Récupération de tous les résultats sous forme de tableau associatif
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatage des dates pour l'affichage
    // Conversion des dates au format Y-m-d (année-mois-jour)
    foreach ($sessions as &$session) {
        $session['date_ouverture'] = date('Y-m-d', strtotime($session['date_ouverture']));
        $session['date_cloture'] = date('Y-m-d', strtotime($session['date_cloture']));
    }
    
    // Envoi de la réponse en JSON
    header('Content-Type: application/json');
    echo json_encode($sessions);
    
} catch(PDOException $e) {
    // En cas d'erreur, renvoi d'une réponse d'erreur 500 avec le message d'erreur
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 