<?php
// Inclusion du fichier de configuration de la base de données
require_once '../../config/database.php';

try {
    // Requête SQL pour récupérer tous les concours, triés par nom
    $query = "SELECT id, nom FROM CONCOURS ORDER BY nom ASC";
    
    // Préparation et exécution de la requête
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Récupération de tous les résultats
    $concours = $stmt->fetchAll();
    
    // Définition du type de contenu de la réponse comme JSON
    header('Content-Type: application/json');
    // Encodage et envoi des données en JSON
    echo json_encode($concours);
    
} catch(PDOException $e) {
    // En cas d'erreur, renvoie un code d'erreur 500 et le message d'erreur
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 