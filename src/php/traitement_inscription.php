<?php
session_start();
require_once '../../config/database.php';

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Log des données reçues
        error_log("Données POST reçues : " . print_r($_POST, true));
        error_log("Fichiers reçus : " . print_r($_FILES, true));

        // Vérification des champs requis
        $required_fields = [
            'email', 'password', 'civilite', 'genre', 'nom', 'prenoms',
            'date_naissance', 'lieu_naissance', 'nationalite', 'situation_matrimoniale',
            'telephone', 'region', 'departement', 'commune', 'lieu_residence',
            'type_piece', 'numero_piece', 'nom_pere', 'nom_mere'
        ];

        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Le champ $field est requis");
            }
        }

        // Vérification des fichiers
        if (!isset($_FILES['photo_identite']) || $_FILES['photo_identite']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("La photo d'identité est requise");
        }

        // Vérification des documents
        $required_documents = ['carte_identite', 'extrait_naissance', 'cmu'];
        foreach ($required_documents as $doc) {
            if (!isset($_FILES[$doc]) || $_FILES[$doc]['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Le document $doc est requis");
            }
        }

        // Vérification du mot de passe
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("Les mots de passe ne correspondent pas");
        }

        // Hashage du mot de passe
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Début de la transaction
        $conn->beginTransaction();

        // Insertion dans la table UTILISATEUR
        $stmt = $conn->prepare("INSERT INTO UTILISATEUR (email, mot_de_passe, role) VALUES (?, ?, 'candidat')");
        $stmt->execute([$_POST['email'], $hashed_password]);
        $utilisateur_id = $conn->lastInsertId();

        // Génération du numéro d'inscription
        $num_inscription = 'CI' . date('Y') . str_pad($utilisateur_id, 6, '0', STR_PAD_LEFT);

        // Création des dossiers s'ils n'existent pas
        $upload_dir = __DIR__ . '/../../uploads/';
        $photos_dir = $upload_dir . 'photos/';
        $documents_dir = $upload_dir . 'documents/';

        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        if (!file_exists($photos_dir)) mkdir($photos_dir, 0777, true);
        if (!file_exists($documents_dir)) mkdir($documents_dir, 0777, true);

        // Upload de la photo d'identité
        $photo_extension = pathinfo($_FILES['photo_identite']['name'], PATHINFO_EXTENSION);
        $photo_filename = 'photo_' . $utilisateur_id . '.' . $photo_extension;
        $photo_path = 'uploads/photos/' . $photo_filename;
        $photo_full_path = $photos_dir . $photo_filename;
        
        if (!move_uploaded_file($_FILES['photo_identite']['tmp_name'], $photo_full_path)) {
            throw new Exception("Erreur lors de l'upload de la photo d'identité");
        }

        // Insertion dans la table CANDIDAT
        $stmt = $conn->prepare("
            INSERT INTO CANDIDAT (
                utilisateur_id, nom, prenoms, sexe, date_naissance, lieu_naissance,
                nationalite, situation_matrimoniale, telephone_principal, telephone_secondaire,
                type_piece, num_piece, expiration_piece, adresse_postale, region,
                departement, commune, lieu_residence, num_inscription, permis,
                type_permis, handicap, nom_pere, nom_mere
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $utilisateur_id,
            $_POST['nom'],
            $_POST['prenoms'],
            $_POST['genre'],
            $_POST['date_naissance'],
            $_POST['lieu_naissance'],
            $_POST['nationalite'],
            $_POST['situation_matrimoniale'],
            $_POST['telephone'],
            $_POST['telephone_secondaire'] ?? null,
            $_POST['type_piece'],
            $_POST['numero_piece'],
            $_POST['date_expiration'] ?? null,
            $_POST['adresse_postale'] ?? null,
            $_POST['region'],
            $_POST['departement'],
            $_POST['commune'],
            $_POST['lieu_residence'],
            $num_inscription,
            $_POST['possede_permis'] ?? 0,
            $_POST['type_permis'] ?? null,
            $_POST['handicap'] ?? 0,
            $_POST['nom_pere'],
            $_POST['nom_mere']
        ]);

        $candidat_id = $conn->lastInsertId();

        // Upload et enregistrement des documents
        $documents = [
            'carte_identite' => 'Carte d\'identité',
            'extrait_naissance' => 'Extrait de naissance',
            'cmu' => 'CMU',
            'handicap' => 'Document handicap',
            'permis_conduire' => 'Permis de conduire'
        ];

        foreach ($documents as $field => $type) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $doc_extension = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                $doc_filename = $field . '_' . $candidat_id . '.' . $doc_extension;
                $doc_path = 'uploads/documents/' . $doc_filename;
                $doc_full_path = $documents_dir . $doc_filename;
                
                if (!move_uploaded_file($_FILES[$field]['tmp_name'], $doc_full_path)) {
                    throw new Exception("Erreur lors de l'upload du document $type");
                }

                $stmt = $conn->prepare("INSERT INTO DOCUMENT (candidat_id, type_document, fichier_url) VALUES (?, ?, ?)");
                $stmt->execute([$candidat_id, $type, $doc_path]);
            }
        }

        // Enregistrement des diplômes
        if (isset($_POST['nom_diplome']) && is_array($_POST['nom_diplome'])) {
            $stmt = $conn->prepare("INSERT INTO DIPLOME (candidat_id, nom, niveau, annee_obtention, etablissement, scan_url) VALUES (?, ?, ?, ?, ?, ?)");
            
            foreach ($_POST['nom_diplome'] as $key => $nom) {
                if (!empty($nom)) {
                    // Upload du scan du diplôme
                    $scan_field = 'fichier_scan_' . $key;
                    $scan_url = null;
                    
                    if (isset($_FILES[$scan_field]) && $_FILES[$scan_field]['error'] === UPLOAD_ERR_OK) {
                        $scan_extension = pathinfo($_FILES[$scan_field]['name'], PATHINFO_EXTENSION);
                        $scan_filename = 'diplome_' . $candidat_id . '_' . $key . '.' . $scan_extension;
                        $scan_path = 'uploads/documents/' . $scan_filename;
                        $scan_full_path = $documents_dir . $scan_filename;
                        
                        if (move_uploaded_file($_FILES[$scan_field]['tmp_name'], $scan_full_path)) {
                            $scan_url = $scan_path;
                        }
                    }

                    $stmt->execute([
                        $candidat_id,
                        $nom,
                        $_POST['niveau'][$key],
                        $_POST['annee_obtention'][$key],
                        $_POST['etablissement'][$key],
                        $scan_url
                    ]);
                }
            }
        }

        // Validation de la transaction
        $conn->commit();

        // Stockage des informations de succès dans la session
        $_SESSION['inscription_success'] = true;
        $_SESSION['num_inscription'] = $num_inscription;

        // Redirection vers la page de succès
        header('Location: inscription_succes.php');
        exit;

    } catch (Exception $e) {
        // En cas d'erreur, on annule la transaction
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        // Log de l'erreur
        error_log("Erreur lors de l'inscription : " . $e->getMessage());
        
        // Stockage de l'erreur dans la session
        $_SESSION['error'] = $e->getMessage();
        header('Location: inscription_candidat.php');
        exit;
    }
} else {
    header('Location: inscription_candidat.php');
    exit;
} 