<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    // Vérifier si un fichier a été uploadé
    if (!isset($_FILES['pdf_file'])) {
        throw new Exception('Aucun fichier PDF reçu');
    }
    
    $pdf_file = $_FILES['pdf_file'];
    $candidate_id = $_POST['candidate_id'];
    
    // Vérifier le type de fichier
    if ($pdf_file['type'] !== 'application/pdf') {
        throw new Exception('Le fichier doit être au format PDF');
    }
    
    // Créer le dossier de stockage s'il n'existe pas
    $upload_dir = '../../uploads/documents/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Générer un nom de fichier unique
    $filename = 'convocation_' . $candidate_id . '_' . time() . '.pdf';
    $filepath = $upload_dir . $filename;
    
    // Déplacer le fichier
    if (!move_uploaded_file($pdf_file['tmp_name'], $filepath)) {
        throw new Exception('Erreur lors de l\'enregistrement du fichier');
    }
    
    // Enregistrer dans la base de données
    $query = "INSERT INTO DOCUMENT (candidat_id, type_document, fichier_url) 
              VALUES (:candidate_id, :type_document, :file_url)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'candidate_id' => $candidate_id,
        'type_document' => 'convocation',
        'file_url' => 'uploads/documents/' . $filename
    ]);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 