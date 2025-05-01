<?php
// Inclusion du fichier de configuration de la base de données
require_once '../../config/database.php';

try {
    // Requête SQL pour récupérer les centres d'examen avec leurs informations associées
    // Jointure avec SESSION_CONCOURS et CONCOURS pour obtenir le nom du concours
    $query = "SELECT c.*, s.concours_id, co.nom as concours_nom 
              FROM CENTRE_EXAMEN c 
              JOIN SESSION_CONCOURS s ON c.session_id = s.id
              JOIN CONCOURS co ON s.concours_id = co.id
              ORDER BY c.ville ASC";
    
    // Préparation et exécution de la requête
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Récupération de tous les résultats sous forme de tableau associatif
    $centres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Définition du type de contenu de la réponse comme JSON
    header('Content-Type: application/json');
    // Encodage et envoi des données en JSON
    echo json_encode($centres);
    
} catch(PDOException $e) {
    // En cas d'erreur, renvoie un code HTTP 500 et le message d'erreur
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 