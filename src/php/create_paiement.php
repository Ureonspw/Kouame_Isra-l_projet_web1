<?php
// Désactiver l'affichage des erreurs pour éviter qu'elles ne polluent la réponse JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Définir le type de contenu JSON dès le début
header('Content-Type: application/json');

try {
    session_start();
    require_once __DIR__ . '/../../config/database.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter']);
        exit;
    }

    // Vérifier si les données requises sont présentes
    if (!isset($_POST['inscription_id']) || !isset($_POST['montant']) || !isset($_POST['mode_paiement']) || !isset($_POST['date_paiement'])) {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        exit;
    }

    $inscription_id = $_POST['inscription_id'];
    $montant = floatval($_POST['montant']); // Convertir en nombre décimal
    $mode_paiement = $_POST['mode_paiement'];
    $date_paiement = $_POST['date_paiement'];
    $utilisateur_id = $_SESSION['user_id'];

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

    // Vérifier que l'inscription appartient bien au candidat
    $stmt = $conn->prepare("
        SELECT i.id 
        FROM INSCRIPTION i 
        WHERE i.id = ? AND i.candidat_id = ?
    ");
    $stmt->execute([$inscription_id, $candidat_id]);
    
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Inscription non trouvée ou non autorisée']);
        exit;
    }

    // Vérifier qu'il n'existe pas déjà un paiement pour cette inscription
    $stmt = $conn->prepare("
        SELECT id 
        FROM PAIEMENT 
        WHERE inscription_id = ?
    ");
    $stmt->execute([$inscription_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Un paiement existe déjà pour cette inscription']);
        exit;
    }

    // Créer le nouveau paiement
    $stmt = $conn->prepare("
        INSERT INTO PAIEMENT (
            inscription_id, 
            montant, 
            mode_paiement, 
            date_paiement, 
            statut
        ) VALUES (?, ?, ?, ?, 'en_attente')
    ");
    
    $stmt->execute([
        $inscription_id,
        $montant,
        $mode_paiement,
        $date_paiement
    ]);

    echo json_encode(['success' => true, 'message' => 'Paiement créé avec succès']);

} catch (PDOException $e) {
    error_log("Erreur PDO dans create_paiement.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de la création du paiement',
        'debug' => [
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage()
        ]
    ]);
} catch (Exception $e) {
    error_log("Erreur inattendue dans create_paiement.php: " . $e->getMessage());
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