<?php
require_once '../../config/database.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Vérifier si le nom du domaine est fourni
if (!isset($_POST['nom']) || empty($_POST['nom'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Le nom du domaine est requis']);
    exit();
}

try {
    // Préparer la requête d'insertion
    $stmt = $conn->prepare("INSERT INTO DOMAINE (nom, description) VALUES (?, ?)");
    
    // Exécuter la requête
    $stmt->execute([$_POST['nom'], $_POST['description'] ?? null]);
    
    // Retourner une réponse de succès
    echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la création du domaine']);
}
?> 