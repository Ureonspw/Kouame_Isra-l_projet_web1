<?php
require_once '../../config/database.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Vérifier si les champs requis sont fournis
if (!isset($_POST['id']) || !isset($_POST['nom']) || !isset($_POST['domaine_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID, nom et domaine sont requis']);
    exit();
}

try {
    // Préparer la requête de mise à jour
    $stmt = $conn->prepare("
        UPDATE CONCOURS SET 
            nom = ?,
            description = ?,
            niveau_requis = ?,
            categorie = ?,
            ministere = ?,
            domaine_id = ?
        WHERE id = ?
    ");
    
    // Exécuter la requête
    $stmt->execute([
        $_POST['nom'],
        $_POST['description'] ?? null,
        $_POST['niveau_requis'] ?? null,
        $_POST['categorie'] ?? null,
        $_POST['ministere'] ?? null,
        $_POST['domaine_id'],
        $_POST['id']
    ]);
    
    // Retourner une réponse de succès
    echo json_encode(['success' => true]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour du concours']);
}
?> 