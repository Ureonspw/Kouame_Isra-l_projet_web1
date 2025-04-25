<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use TCPDF;

try {
    // Récupérer les données POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['candidate_id'])) {
        throw new Exception('Données invalides');
    }
    
    // Récupérer les informations du candidat
    $query = "SELECT 
                c.*, 
                i.date_inscription,
                i.statut,
                s.date_ouverture,
                s.date_cloture,
                co.nom as concours_nom,
                co.description as concours_description,
                d.nom as domaine_nom,
                ce.ville as centre_ville,
                ce.lieu as centre_lieu
              FROM CANDIDAT c 
              JOIN INSCRIPTION i ON c.id = i.candidat_id
              JOIN SESSION_CONCOURS s ON i.session_id = s.id 
              JOIN CONCOURS co ON s.concours_id = co.id 
              JOIN DOMAINE d ON co.domaine_id = d.id
              JOIN CENTRE_EXAMEN ce ON i.centre_id = ce.id
              WHERE c.id = :id";
              
    $stmt = $conn->prepare($query);
    $stmt->execute(['id' => $data['candidate_id']]);
    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$candidate) {
        throw new Exception('Candidat non trouvé');
    }
    
    // Créer un nouveau document PDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Définir les informations du document
    $pdf->SetCreator('PUBLIGEST CI');
    $pdf->SetAuthor('PUBLIGEST CI');
    $pdf->SetTitle('Convocation - ' . $candidate['nom'] . ' ' . $candidate['prenoms']);
    
    // Supprimer l'en-tête et le pied de page par défaut
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Ajouter une page
    $pdf->AddPage();
    
    // Logo
    $logoPath = __DIR__ . '/../../assets/images/logo.png';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 15, 15, 30);
    }
    
    // Titre
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 20, 'CONVOCATION', 0, 1, 'C');
    
    // Informations du candidat
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Ln(10);
    
    // En-tête avec les informations du candidat
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Nom et Prénoms :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $candidate['nom'] . ' ' . $candidate['prenoms'], 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Date de naissance :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $candidate['date_naissance'], 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Lieu de naissance :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $candidate['lieu_naissance'], 0, 1);
    
    $pdf->Ln(10);
    
    // Informations du concours
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Domaine :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $candidate['domaine_nom'], 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Concours :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, $candidate['concours_nom'], 0, 'L');
    
    if (!empty($candidate['concours_description'])) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 8, $candidate['concours_description'], 0, 'L');
    }
    
    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, 'Nous avons le plaisir de vous informer que votre candidature a été retenue.', 0, 'L');
    $pdf->Ln(5);
    
    // Informations de l'examen
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Date et heure :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, date('d/m/Y H:i', strtotime($data['exam_date'])), 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Centre d\'examen :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $candidate['centre_ville'] . ' - ' . $candidate['centre_lieu'], 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Durée :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $data['exam_duration'] . ' minutes', 0, 1);
    
    // Instructions spécifiques
    if (!empty($data['exam_instructions'])) {
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Instructions spécifiques :', 0, 1);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(0, 10, $data['exam_instructions'], 0, 'L');
    }
    
    // Consignes générales
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Consignes importantes :', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, '- Présentez-vous 30 minutes avant le début des épreuves', 0, 'L');
    $pdf->MultiCell(0, 10, '- Munissez-vous de votre pièce d\'identité en cours de validité', 0, 'L');
    $pdf->MultiCell(0, 10, '- Apportez cette convocation le jour de l\'examen', 0, 'L');
    $pdf->MultiCell(0, 10, '- Les téléphones portables et autres appareils électroniques sont interdits', 0, 'L');
    
    // Signature
    $pdf->Ln(20);
    $pdf->Cell(0, 10, 'Le Directeur des Concours,', 0, 1, 'R');
    $pdf->Ln(20);
    $pdf->Cell(0, 10, 'Signature et Cachet', 0, 1, 'R');
    
    // Nettoyer le buffer de sortie
    ob_clean();
    
    // Définir les headers pour le téléchargement
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="convocation_' . $candidate['nom'] . '_' . $candidate['prenoms'] . '_' . $candidate['concours_nom'] . '.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // Générer le PDF
    $pdf->Output('convocation.pdf', 'D');
    
    // Enregistrer le document dans la base de données
    $query = "INSERT INTO DOCUMENT (candidat_id, type_document, fichier_url) 
              VALUES (:candidat_id, 'convocation', :fichier_url)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'candidat_id' => $candidate['id'],
        'fichier_url' => 'convocation_' . $candidate['nom'] . '_' . $candidate['prenoms'] . '_' . $candidate['concours_nom'] . '.pdf'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Erreur lors de la génération de la convocation',
        'details' => $e->getMessage()
    ]);
}
?> 