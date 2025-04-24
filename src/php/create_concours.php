<?php
require_once '../../config/database.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Vérifier les champs requis
if (!isset($_POST['nom']) || empty($_POST['nom']) || !isset($_POST['domaine_id']) || empty($_POST['domaine_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Le nom du concours et le domaine sont requis']);
    exit();
}

try {
    // Préparer la requête d'insertion
    $stmt = $conn->prepare("
        INSERT INTO CONCOURS (
            nom, description, niveau_requis, categorie, 
            ministere, domaine_id
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    // Exécuter la requête avec les valeurs
    $stmt->execute([
        $_POST['nom'],
        $_POST['description'] ?? null,
        $_POST['niveau_requis'] ?? null,
        $_POST['categorie'] ?? null,
        $_POST['ministere'] ?? null,
        $_POST['domaine_id']
    ]);
    
    // Retourner une réponse de succès
    echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la création du concours']);
}
?> 