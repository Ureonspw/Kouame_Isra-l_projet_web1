<?php
// Inclusion du fichier de configuration de la base de données
require_once '../../config/database.php';

// Vérification de la présence de l'ID du centre dans les paramètres GET
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de centre manquant']);
    exit();
}

try {
    // Requête SQL pour récupérer les informations du centre d'examen
    // Jointure avec la table SESSION_CONCOURS et CONCOURS pour obtenir le nom du concours
    $query = "SELECT c.*, s.concours_id, co.nom as concours_nom 
              FROM CENTRE_EXAMEN c 
              JOIN SESSION_CONCOURS s ON c.session_id = s.id
              JOIN CONCOURS co ON s.concours_id = co.id
              WHERE c.id = :id";
    
    // Préparation et exécution de la requête avec l'ID du centre
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    
    // Récupération du résultat
    $centre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Vérification si le centre existe
    if (!$centre) {
        http_response_code(404);
        echo json_encode(['error' => 'Centre non trouvé']);
        exit();
    }
    
    // Envoi de la réponse en JSON
    header('Content-Type: application/json');
    echo json_encode($centre);
    
} catch(PDOException $e) {
    // Gestion des erreurs de base de données
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 