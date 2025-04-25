<?php
require_once '../../config/database.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID du candidat non spécifié');
    }

    $candidateId = $_GET['id'];

    // Récupérer les informations personnelles du candidat
    $query = "
        SELECT 
            c.*,
            u.email,
            i.statut as inscription_statut,
            sc.id as session_id,
            sc.date_ouverture,
            sc.date_cloture,
            co.id as concours_id,
            co.nom as concours_nom,
            co.description as concours_description,
            co.niveau_requis,
            co.categorie,
            co.ministere,
            d.id as domaine_id,
            d.nom as domaine_nom,
            ce.ville as centre_ville,
            ce.lieu as centre_lieu
        FROM CANDIDAT c
        JOIN UTILISATEUR u ON c.utilisateur_id = u.id
        JOIN INSCRIPTION i ON c.id = i.candidat_id
        JOIN SESSION_CONCOURS sc ON i.session_id = sc.id
        JOIN CONCOURS co ON sc.concours_id = co.id
        JOIN DOMAINE d ON co.domaine_id = d.id
        LEFT JOIN CENTRE_EXAMEN ce ON i.centre_id = ce.id
        WHERE c.id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$candidateId]);
    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$candidate) {
        throw new Exception('Candidat non trouvé');
    }

    // Récupérer les documents du candidat
    $query = "SELECT * FROM DOCUMENT WHERE candidat_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$candidateId]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les diplômes du candidat
    $query = "SELECT * FROM DIPLOME WHERE candidat_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$candidateId]);
    $diplomas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les informations de paiement
    $query = "
        SELECT 
            p.*,
            i.statut as inscription_statut
        FROM PAIEMENT p
        JOIN INSCRIPTION i ON p.inscription_id = i.id
        WHERE i.candidat_id = ?
        ORDER BY p.created_at DESC
        LIMIT 1
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute([$candidateId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    // Formater la réponse
    $response = [
        'nom' => $candidate['nom'],
        'prenoms' => $candidate['prenoms'],
        'date_naissance' => $candidate['date_naissance'],
        'nationalite' => $candidate['nationalite'],
        'num_piece' => $candidate['num_piece'],
        'telephone_principal' => $candidate['telephone_principal'],
        'adresse_postale' => $candidate['adresse_postale'],
        'session' => [
            'id' => $candidate['session_id'],
            'date_ouverture' => $candidate['date_ouverture'],
            'date_cloture' => $candidate['date_cloture']
        ],
        'concours' => [
            'id' => $candidate['concours_id'],
            'nom' => $candidate['concours_nom'],
            'description' => $candidate['concours_description'],
            'niveau_requis' => $candidate['niveau_requis'],
            'categorie' => $candidate['categorie'],
            'ministere' => $candidate['ministere'],
            'domaine' => [
                'id' => $candidate['domaine_id'],
                'nom' => $candidate['domaine_nom']
            ]
        ],
        'statut' => $candidate['inscription_statut'],
        'centre_examen' => [
            'ville' => $candidate['centre_ville'],
            'lieu' => $candidate['centre_lieu']
        ],
        'documents' => $documents,
        'diplomes' => $diplomas,
        'paiement' => $payment ? [
            'montant' => $payment['montant'],
            'mode_paiement' => $payment['mode_paiement'],
            'date_paiement' => $payment['date_paiement'],
            'statut' => $payment['statut']
        ] : null
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 