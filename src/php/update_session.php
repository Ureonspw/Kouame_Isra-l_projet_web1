<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

try {
    // Vérifier si les données requises sont présentes
    if (!isset($_POST['id']) || !isset($_POST['concours_id']) || !isset($_POST['date_ouverture']) || 
        !isset($_POST['date_cloture']) || !isset($_POST['nb_places'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes']);
        exit();
    }

    // Vérifier que la date de clôture est postérieure à la date d'ouverture
    $date_ouverture = new DateTime($_POST['date_ouverture']);
    $date_cloture = new DateTime($_POST['date_cloture']);
    
    if ($date_cloture <= $date_ouverture) {
        http_response_code(400);
        echo json_encode(['error' => 'La date de clôture doit être postérieure à la date d\'ouverture']);
        exit();
    }
    
    $query = "UPDATE SESSION_CONCOURS 
              SET concours_id = :concours_id,
                  date_ouverture = :date_ouverture,
                  date_cloture = :date_cloture,
                  nb_places = :nb_places
              WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->bindParam(':concours_id', $_POST['concours_id'], PDO::PARAM_INT);
    $stmt->bindParam(':date_ouverture', $_POST['date_ouverture']);
    $stmt->bindParam(':date_cloture', $_POST['date_cloture']);
    $stmt->bindParam(':nb_places', $_POST['nb_places'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la mise à jour de la session']);
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 