<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

try {
    // Vérifier si l'ID est présent
    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de centre manquant']);
        exit();
    }
    
    $query = "DELETE FROM CENTRE_EXAMEN WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la suppression du centre']);
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 