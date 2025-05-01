<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit();
}

$user_id = $data['user_id'];

try {
    // Vérifier si l'utilisateur existe
    $stmt = $conn->prepare("SELECT role FROM UTILISATEUR WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
        exit();
    }

    // Ne pas permettre la suppression d'un administrateur
    if ($user['role'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Impossible de supprimer un administrateur']);
        exit();
    }

    // Début de la transaction
    $conn->beginTransaction();

    // Supprimer l'utilisateur (les contraintes de clé étrangère avec CASCADE s'occuperont des données associées)
    $stmt = $conn->prepare("DELETE FROM UTILISATEUR WHERE id = ?");
    $stmt->execute([$user_id]);

    // Valider la transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);

} catch (PDOException $e) {
    // En cas d'erreur, annuler la transaction
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Erreur lors de la suppression de l'utilisateur: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de l\'utilisateur']);
} catch (Exception $e) {
    // En cas d'erreur, annuler la transaction
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Erreur: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue']);
}
?> 