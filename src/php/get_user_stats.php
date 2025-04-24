<?php
require_once '../../config/database.php';

try {
    // Récupérer le nombre total d'utilisateurs
    $stmt = $conn->query("SELECT COUNT(*) as total FROM UTILISATEUR");
    $total_users = $stmt->fetch()['total'];

    // Récupérer le nombre de candidats
    $stmt = $conn->query("SELECT COUNT(*) as total FROM UTILISATEUR WHERE role = 'candidat'");
    $total_candidates = $stmt->fetch()['total'];

    // Récupérer le nombre d'administrateurs
    $stmt = $conn->query("SELECT COUNT(*) as total FROM UTILISATEUR WHERE role = 'admin'");
    $total_admins = $stmt->fetch()['total'];

    // Retourner les statistiques au format JSON
    echo json_encode([
        'total_users' => $total_users,
        'total_candidates' => $total_candidates,
        'total_admins' => $total_admins
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des statistiques']);
}
?> 