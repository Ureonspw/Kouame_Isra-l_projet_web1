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
    // Vérifier d'abord si la session existe
    $stmt = $conn->prepare("SELECT id FROM SESSION_CONCOURS WHERE id = ?");
    $stmt->execute([$session_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Session non trouvée']);
        exit();
    }

    // Récupérer les détails de la session
    $stmt = $conn->prepare("
        SELECT s.*, c.nom as concours_nom 
        FROM SESSION_CONCOURS s 
        LEFT JOIN CONCOURS c ON s.concours_id = c.id 
        WHERE s.id = ?
    ");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        http_response_code(404);
        echo json_encode(['error' => 'Session non trouvée']);
        exit();
    }

    echo json_encode($session);
} catch (PDOException $e) {
    error_log("Erreur PDO dans get_session.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?> 