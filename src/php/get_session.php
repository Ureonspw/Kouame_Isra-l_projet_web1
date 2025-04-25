<?php
session_start();
require_once '../../config/database.php';

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de session manquant']);
    exit();
}

$session_id = $_GET['id'];

try {
    $query = "SELECT * FROM SESSION_CONCOURS WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        http_response_code(404);
        echo json_encode(['error' => 'Session non trouvée']);
        exit();
    }
    
    // Formater les dates pour l'affichage
    $session['date_ouverture'] = date('Y-m-d', strtotime($session['date_ouverture']));
    $session['date_cloture'] = date('Y-m-d', strtotime($session['date_cloture']));
    
    header('Content-Type: application/json');
    echo json_encode($session);
    
} catch(PDOException $e) {
    error_log("Erreur PDO dans get_session.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 