<?php
require_once '../../config/database.php';

try {
    // Récupérer la liste des utilisateurs
    $stmt = $conn->query("
        SELECT id, email, role, created_at 
        FROM UTILISATEUR 
        ORDER BY created_at DESC
    ");
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Retourner les utilisateurs au format JSON
    echo json_encode($users);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des utilisateurs']);
}
?> 