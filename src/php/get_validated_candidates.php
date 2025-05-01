<?php
// Inclusion du fichier de configuration de la base de données
require_once __DIR__ . '/../../config/database.php';

// Définition du type de contenu de la réponse en JSON
header('Content-Type: application/json');

try {
    // Requête SQL pour récupérer les candidats validés avec leurs informations détaillées
    // Jointure entre les tables CANDIDAT, UTILISATEUR et INSCRIPTION
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
    
    // Préparation et exécution de la requête
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Récupération de tous les résultats sous forme de tableau associatif
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($candidates)) {
        // Log pour le débogage si aucun candidat n'est trouvé
        error_log("Aucun candidat trouvé avec le statut 'valide'");
        echo json_encode([]);
    } else {
        // Log du nombre de candidats trouvés
        error_log("Nombre de candidats trouvés : " . count($candidates));
        echo json_encode($candidates);
    }
} catch(PDOException $e) {
    // Gestion des erreurs SQL
    error_log("Erreur SQL : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la récupération des candidats',
        'details' => $e->getMessage()
    ]);
}
?> 