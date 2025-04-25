<?php
require_once '../../config/database.php';

try {
    $query = "SELECT c.*, s.concours_id, co.nom as concours_nom 
              FROM CENTRE_EXAMEN c 
              JOIN SESSION_CONCOURS s ON c.session_id = s.id
              JOIN CONCOURS co ON s.concours_id = co.id
              ORDER BY c.ville ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $centres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($centres);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 