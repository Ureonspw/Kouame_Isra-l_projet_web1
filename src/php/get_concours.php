<?php
require_once '../../config/database.php';

try {
    $query = "SELECT id, nom FROM CONCOURS ORDER BY nom ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $concours = $stmt->fetchAll();
    
    header('Content-Type: application/json');
    echo json_encode($concours);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 