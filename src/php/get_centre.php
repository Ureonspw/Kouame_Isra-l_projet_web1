<?php
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de centre manquant']);
    exit();
}

try {
    $query = "SELECT c.*, s.concours_id, co.nom as concours_nom 
              FROM CENTRE_EXAMEN c 
              JOIN SESSION_CONCOURS s ON c.session_id = s.id
              JOIN CONCOURS co ON s.concours_id = co.id
              WHERE c.id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $centre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$centre) {
        http_response_code(404);
        echo json_encode(['error' => 'Centre non trouvé']);
        exit();
    }
    
    header('Content-Type: application/json');
    echo json_encode($centre);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 