<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour vous inscrire']);
    exit();
}

// Vérifier si les données requises sont présentes
if (!isset($_POST['session_id']) || !isset($_POST['centre_id'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

try {
    // Récupérer l'ID du candidat
    $stmt = $conn->prepare("SELECT id FROM CANDIDAT WHERE utilisateur_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $candidat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$candidat) {
        echo json_encode(['success' => false, 'message' => 'Candidat non trouvé']);
        exit();
    }

    // Vérifier si la session est toujours ouverte
    $stmt = $conn->prepare("
        SELECT date_cloture, nb_places 
        FROM SESSION_CONCOURS 
        WHERE id = ?
    ");
    $stmt->execute([$_POST['session_id']]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session non trouvée']);
        exit();
    }

    if (strtotime($session['date_cloture']) < time()) {
        echo json_encode(['success' => false, 'message' => 'La session est clôturée']);
        exit();
    }

    // Vérifier le nombre de places disponibles
    if ($session['nb_places'] !== null) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM INSCRIPTION 
            WHERE session_id = ? AND statut = 'valide'
        ");
        $stmt->execute([$_POST['session_id']]);
        $inscriptions = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($inscriptions['count'] >= $session['nb_places']) {
            echo json_encode(['success' => false, 'message' => 'Plus de places disponibles']);
            exit();
        }
    }

    // Vérifier si le candidat n'est pas déjà inscrit à cette session
    $stmt = $conn->prepare("
        SELECT id 
        FROM INSCRIPTION 
        WHERE candidat_id = ? AND session_id = ?
    ");
    $stmt->execute([$candidat['id'], $_POST['session_id']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à cette session']);
        exit();
    }

    // Créer l'inscription
    $stmt = $conn->prepare("
        INSERT INTO INSCRIPTION (
            candidat_id, 
            session_id, 
            centre_id, 
            date_inscription, 
            statut
        ) VALUES (?, ?, ?, CURDATE(), 'en_attente')
    ");
    
    $stmt->execute([
        $candidat['id'],
        $_POST['session_id'],
        $_POST['centre_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Inscription effectuée avec succès']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
}
?> 