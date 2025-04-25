<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter']);
    exit;
}

// Vérifier si les données requises sont présentes
if (!isset($_POST['paiement_id']) || !isset($_POST['montant']) || !isset($_POST['mode_paiement']) || !isset($_POST['date_paiement'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$paiement_id = $_POST['paiement_id'];
$montant = floatval($_POST['montant']);
$mode_paiement = $_POST['mode_paiement'];
$date_paiement = $_POST['date_paiement'];
$utilisateur_id = $_SESSION['user_id'];

try {
    // Récupérer l'ID du candidat à partir de l'ID utilisateur
    $stmt = $conn->prepare("
        SELECT id 
        FROM CANDIDAT 
        WHERE utilisateur_id = ?
    ");
    $stmt->execute([$utilisateur_id]);
    $candidat = $stmt->fetch();

    if (!$candidat) {
        echo json_encode(['success' => false, 'message' => 'Candidat non trouvé']);
        exit;
    }

    $candidat_id = $candidat['id'];

    // Vérifier que le paiement appartient bien au candidat
    $stmt = $conn->prepare("
        SELECT p.id 
        FROM PAIEMENT p
        JOIN INSCRIPTION i ON p.inscription_id = i.id
        WHERE p.id = ? AND i.candidat_id = ?
    ");
    $stmt->execute([$paiement_id, $candidat_id]);
    
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Paiement non trouvé ou non autorisé']);
        exit;
    }

    // Mettre à jour le paiement
    $stmt = $conn->prepare("
        UPDATE PAIEMENT 
        SET montant = ?,
            mode_paiement = ?,
            date_paiement = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $montant,
        $mode_paiement,
        $date_paiement,
        $paiement_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Paiement modifié avec succès']);

} catch (PDOException $e) {
    error_log("Erreur PDO dans process_paiement.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de la modification du paiement',
        'debug' => [
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage()
        ]
    ]);
} catch (Exception $e) {
    error_log("Erreur inattendue dans process_paiement.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Une erreur inattendue est survenue',
        'debug' => [
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage()
        ]
    ]);
}
?> 