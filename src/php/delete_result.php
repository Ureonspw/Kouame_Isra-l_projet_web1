<?php
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit();
}

try {
    $query = "DELETE FROM RESULTAT WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Résultat non trouvé']);
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 