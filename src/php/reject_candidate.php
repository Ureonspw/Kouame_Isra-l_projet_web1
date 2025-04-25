<?php
require_once '../../config/database.php';
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

try {
    // Récupérer les données du POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['candidate_id'])) {
        throw new Exception('ID du candidat non spécifié');
    }

    $candidateId = $data['candidate_id'];

    // Mettre à jour le statut de l'inscription
    $query = "UPDATE INSCRIPTION SET statut = 'rejete' WHERE candidat_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$candidateId]);

    // Vérifier si la mise à jour a réussi
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Candidat rejeté avec succès']);
    } else {
        throw new Exception('Aucun candidat trouvé avec cet ID');
    }

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 