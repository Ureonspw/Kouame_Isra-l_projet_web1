<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Vérifier si les champs requis sont présents
if (!isset($_POST['type_document']) || !isset($_FILES['document_file'])) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
    exit();
}

// Récupérer les données du formulaire
$type_document = $_POST['type_document'];
$file = $_FILES['document_file'];

// Vérifier les erreurs de téléchargement
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement du fichier']);
    exit();
}

// Vérifier le type de fichier
$allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
    exit();
}

// Vérifier la taille du fichier (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Le fichier est trop volumineux (max 5MB)']);
    exit();
}

// Créer le dossier de stockage s'il n'existe pas
$upload_dir = '../../uploads/documents/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Générer un nom de fichier unique
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;
$filepath = $upload_dir . $filename;

// Déplacer le fichier
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du déplacement du fichier']);
    exit();
}

// Enregistrer dans la base de données
require_once __DIR__ . '/../../config/database.php';

try {
    // Récupérer l'ID du candidat
    $stmt = $conn->prepare("SELECT id FROM CANDIDAT WHERE utilisateur_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $candidat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$candidat) {
        unlink($filepath); // Supprimer le fichier téléchargé
        echo json_encode(['success' => false, 'message' => 'Candidat non trouvé']);
        exit();
    }

    // Insérer le document dans la base de données
    $stmt = $conn->prepare("
        INSERT INTO DOCUMENT (candidat_id, type_document, fichier_url) 
        VALUES (?, ?, ?)
    ");
    
    $relative_path = 'uploads/documents/' . $filename;
    $stmt->execute([$candidat['id'], $type_document, $relative_path]);

    echo json_encode(['success' => true, 'message' => 'Document ajouté avec succès']);
} catch (PDOException $e) {
    unlink($filepath); // Supprimer le fichier téléchargé en cas d'erreur
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?> 