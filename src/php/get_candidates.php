<?php
require_once '../../config/database.php';

try {
    // Requête pour récupérer les candidats avec leurs inscriptions
    $query = "
        SELECT 
            c.id,
            c.nom,
            c.prenoms,
            u.email,
            c.telephone_principal,
            c.num_piece,
            i.statut,
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
            d.nom as domaine_nom
        FROM CANDIDAT c
        JOIN UTILISATEUR u ON c.utilisateur_id = u.id
        JOIN INSCRIPTION i ON c.id = i.candidat_id
        JOIN SESSION_CONCOURS sc ON i.session_id = sc.id
        JOIN CONCOURS co ON sc.concours_id = co.id
        JOIN DOMAINE d ON co.domaine_id = d.id
        ORDER BY i.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formater les données pour l'affichage
    $formattedCandidates = array_map(function($candidate) {
        return [
            'id' => $candidate['id'],
            'nom' => $candidate['nom'],
            'prenoms' => $candidate['prenoms'],
            'email' => $candidate['email'],
            'telephone_principal' => $candidate['telephone_principal'],
            'num_piece' => $candidate['num_piece'],
            'statut' => $candidate['statut'],
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
            ]
        ];
    }, $candidates);

    header('Content-Type: application/json');
    echo json_encode($formattedCandidates);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des candidats: ' . $e->getMessage()]);
}
?> 