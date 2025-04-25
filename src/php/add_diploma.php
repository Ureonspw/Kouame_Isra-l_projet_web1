<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter pour effectuer cette action']);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

try {
    // Récupérer l'ID du candidat
    $stmt = $conn->prepare("SELECT id FROM CANDIDAT WHERE utilisateur_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $candidat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$candidat) {
        throw new Exception("Candidat non trouvé");
    }

    // Vérifier si un fichier a été uploadé
    if (!isset($_FILES['scan']) || $_FILES['scan']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Veuillez sélectionner un fichier valide");
    }

    $file = $_FILES['scan'];
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Vérifier le type de fichier
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Format de fichier non supporté. Formats acceptés : PDF, JPG, JPEG, PNG");
    }

    // Vérifier la taille du fichier
    if ($file['size'] > $maxSize) {
        throw new Exception("Le fichier est trop volumineux. Taille maximale : 5MB");
    }

    // Créer le dossier de destination s'il n'existe pas
    $uploadDir = '../../uploads/diplomes/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Générer un nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('diplome_') . '.' . $extension;
    $filePath = $uploadDir . $fileName;

    // Déplacer le fichier
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception("Erreur lors de l'upload du fichier");
    }

    // Insérer le diplôme dans la base de données
    $stmt = $conn->prepare("
        INSERT INTO DIPLOME (
            candidat_id,
            nom,
            niveau,
            annee_obtention,
            etablissement,
            scan_url
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    $scanUrl = 'uploads/diplomes/' . $fileName;
    $stmt->execute([
        $candidat['id'],
        $_POST['nom'],
        $_POST['niveau'],
        $_POST['annee_obtention'],
        $_POST['etablissement'],
        $scanUrl
    ]);

    echo json_encode(['success' => true, 'message' => 'Diplôme ajouté avec succès']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 