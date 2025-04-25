<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

try {
    // Vérifier si les données requises sont présentes
    if (!isset($_POST['session_id']) || !isset($_POST['ville']) || 
        !isset($_POST['lieu']) || !isset($_POST['capacite'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes']);
        exit();
    }
    
    $query = "INSERT INTO CENTRE_EXAMEN (session_id, ville, lieu, capacite) 
              VALUES (:session_id, :ville, :lieu, :capacite)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':session_id', $_POST['session_id'], PDO::PARAM_INT);
    $stmt->bindParam(':ville', $_POST['ville']);
    $stmt->bindParam(':lieu', $_POST['lieu']);
    $stmt->bindParam(':capacite', $_POST['capacite'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la création du centre']);
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 