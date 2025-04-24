<?php
require_once '../../config/database.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du domaine manquant']);
    exit();
}

try {
    // Commencer une transaction
    $conn->beginTransaction();

    // Supprimer le domaine (la suppression en cascade s'occupera des concours liés)
    $stmt = $conn->prepare("DELETE FROM DOMAINE WHERE id = ?");
    $stmt->execute([$data['id']]);

    // Valider la transaction
    $conn->commit();

    echo json_encode(['success' => true]);

} catch(PDOException $e) {
    // Annuler la transaction en cas d'erreur
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression du domaine']);
}
?> 