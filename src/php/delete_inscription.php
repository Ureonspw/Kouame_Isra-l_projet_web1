<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour supprimer une inscription']);
    exit();
}

// Vérifier si l'ID de l'inscription est fourni
if (!isset($_POST['inscription_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID d\'inscription manquant']);
    exit();
}

try {
    // Vérifier si l'inscription appartient bien au candidat
    $stmt = $conn->prepare("
        SELECT i.id 
        FROM INSCRIPTION i
        JOIN CANDIDAT c ON i.candidat_id = c.id
        WHERE i.id = ? AND c.utilisateur_id = ?
    ");
    $stmt->execute([$_POST['inscription_id'], $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Inscription non trouvée ou vous n\'avez pas les droits pour la supprimer']);
        exit();
    }

    // Supprimer l'inscription
    $stmt = $conn->prepare("DELETE FROM INSCRIPTION WHERE id = ?");
    $stmt->execute([$_POST['inscription_id']]);

    echo json_encode(['success' => true, 'message' => 'Inscription supprimée avec succès']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
}
?> 