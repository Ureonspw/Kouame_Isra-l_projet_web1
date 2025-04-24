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
    echo json_encode(['error' => 'ID de centre manquant']);
    exit();
}

$center_id = $_GET['id'];

try {
    // Vérifier d'abord si le centre existe
    $stmt = $conn->prepare("SELECT id FROM CENTRE_EXAMEN WHERE id = ?");
    $stmt->execute([$center_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Centre non trouvé']);
        exit();
    }

    // Récupérer les détails du centre
    $stmt = $conn->prepare("
        SELECT c.*, s.concours_id, co.nom as concours_nom 
        FROM CENTRE_EXAMEN c 
        LEFT JOIN SESSION s ON c.session_id = s.id 
        LEFT JOIN CONCOURS co ON s.concours_id = co.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$center_id]);
    $center = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$center) {
        http_response_code(404);
        echo json_encode(['error' => 'Centre non trouvé']);
        exit();
    }

    echo json_encode($center);
} catch (PDOException $e) {
    error_log("Erreur PDO dans get_center.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?> 