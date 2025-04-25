<?php
require_once '../../config/database.php';

try {
    $query = "SELECT s.*, c.nom as concours_nom 
              FROM SESSION_CONCOURS s 
              JOIN CONCOURS c ON s.concours_id = c.id 
              ORDER BY s.date_ouverture DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les dates pour l'affichage
    foreach ($sessions as &$session) {
        $session['date_ouverture'] = date('Y-m-d', strtotime($session['date_ouverture']));
        $session['date_cloture'] = date('Y-m-d', strtotime($session['date_cloture']));
    }
    
    header('Content-Type: application/json');
    echo json_encode($sessions);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 