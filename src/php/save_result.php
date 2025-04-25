<?php
require_once __DIR__ . '/../../config/database.php';

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Données invalides']);
    exit();
}

// Validation des données requises
$requiredFields = ['inscription_id', 'note', 'decision'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        http_response_code(400);
        echo json_encode(['error' => "Le champ $field est requis"]);
        exit();
    }
}

// Validation de la note
if (!is_numeric($data['note']) || $data['note'] < 0 || $data['note'] > 1000) {
    http_response_code(400);
    echo json_encode(['error' => 'La note doit être un nombre entre 0 et 1000']);
    exit();
}

// Validation de la décision
$validDecisions = ['admis', 'rejete', 'en_attente'];
if (!in_array($data['decision'], $validDecisions)) {
    http_response_code(400);
    echo json_encode(['error' => 'Décision invalide']);
    exit();
}

try {
    // Vérifier si l'inscription existe et est valide
    $checkQuery = "SELECT i.id 
                   FROM INSCRIPTION i 
                   WHERE i.id = :inscription_id 
                   AND i.statut = 'valide'";
    
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':inscription_id', $data['inscription_id']);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Inscription invalide ou non trouvée']);
        exit();
    }

    if (isset($data['id']) && !empty($data['id'])) {
        // Mise à jour
        $query = "UPDATE RESULTAT 
                 SET inscription_id = :inscription_id,
                     note = :note,
                     decision = :decision
                 WHERE id = :id";
    } else {
        // Vérifier si un résultat existe déjà pour cette inscription
        $checkResultQuery = "SELECT id FROM RESULTAT WHERE inscription_id = :inscription_id";
        $checkResultStmt = $conn->prepare($checkResultQuery);
        $checkResultStmt->bindParam(':inscription_id', $data['inscription_id']);
        $checkResultStmt->execute();
        
        if ($checkResultStmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Un résultat existe déjà pour cette inscription']);
            exit();
        }

        // Insertion
        $query = "INSERT INTO RESULTAT (inscription_id, note, decision)
                 VALUES (:inscription_id, :note, :decision)";
    }
    
    $stmt = $conn->prepare($query);
    
    if (isset($data['id']) && !empty($data['id'])) {
        $stmt->bindParam(':id', $data['id']);
    }
    
    $stmt->bindParam(':inscription_id', $data['inscription_id']);
    $stmt->bindParam(':note', $data['note']);
    $stmt->bindParam(':decision', $data['decision']);
    
    $stmt->execute();
    
    // Récupérer l'ID du résultat créé ou mis à jour
    $resultId = isset($data['id']) ? $data['id'] : $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'id' => $resultId,
        'message' => isset($data['id']) ? 'Résultat mis à jour avec succès' : 'Résultat créé avec succès'
    ]);
    
} catch(PDOException $e) {
    error_log("Erreur SQL: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()]);
}
?> 