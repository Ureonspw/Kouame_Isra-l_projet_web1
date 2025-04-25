<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Définition du répertoire d'upload
        $upload_dir = __DIR__ . '/../../uploads/documents/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Validation des données
        $errors = [];
        
        // Debug des données reçues
        error_log("Email reçu: " . $_POST['email']);
        error_log("Longueur du mot de passe: " . strlen($_POST['mot_de_passe']));
        
        // Validation des informations de connexion
        if (empty($_POST['email'])) {
            $errors[] = "L'email est requis";
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide";
        }
        
        if (empty($_POST['mot_de_passe'])) {
            $errors[] = "Le mot de passe est requis";
        } elseif (strlen($_POST['mot_de_passe']) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        }
        
        if ($_POST['mot_de_passe'] !== $_POST['confirm_password']) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }
        
        // Debug des erreurs
        if (!empty($errors)) {
            error_log("Erreurs de validation: " . implode(", ", $errors));
        }
        
        // Vérification si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM UTILISATEUR WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Cet email est déjà utilisé";
        }
        
        // Si des erreurs, on les affiche
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: inscription.php');
            exit;
        }
        
        // Hashage du mot de passe
        $hashed_password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        
        // Début de la transaction
        $conn->beginTransaction();
        
        try {
            // Insertion dans la table UTILISATEUR
            $stmt = $conn->prepare("
                INSERT INTO UTILISATEUR (email, mot_de_passe, role)
                VALUES (?, ?, 'candidat')
            ");
            $stmt->execute([$_POST['email'], $hashed_password]);
            $utilisateur_id = $conn->lastInsertId();
            
            // Génération du numéro d'inscription
            $num_inscription = 'CAND-' . date('Ymd') . '-' . strtoupper(uniqid());
            
            // Insertion dans la table CANDIDAT
            $stmt = $conn->prepare("
                INSERT INTO CANDIDAT (
                    utilisateur_id, nom, prenoms, sexe, date_naissance, lieu_naissance,
                    nationalite, situation_matrimoniale, telephone_principal, telephone_secondaire,
                    type_piece, num_piece, expiration_piece, adresse_postale, region,
                    departement, commune, lieu_residence, type_candidat, num_inscription,
                    permis, type_permis, handicap, nom_pere, nom_mere
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $utilisateur_id,
                $_POST['nom'],
                $_POST['prenoms'],
                $_POST['sexe'],
                $_POST['date_naissance'],
                $_POST['lieu_naissance'],
                $_POST['nationalite'],
                $_POST['situation_matrimoniale'] ?? null,
                $_POST['telephone_principal'],
                $_POST['telephone_secondaire'] ?? null,
                $_POST['type_piece'],
                $_POST['num_piece'],
                $_POST['expiration_piece'],
                $_POST['adresse_postale'],
                $_POST['region'],
                $_POST['departement'],
                $_POST['commune'],
                $_POST['lieu_residence'] ?? null,
                $_POST['type_candidat'],
                $num_inscription,
                $_POST['permis'] ?? 0,
                $_POST['type_permis'] ?? null,
                $_POST['handicap'] ?? 0,
                $_POST['nom_pere'],
                $_POST['nom_mere']
            ]);
            
            $candidat_id = $conn->lastInsertId();
            
            // Insertion des diplômes
            if (isset($_POST['diplome_nom']) && is_array($_POST['diplome_nom'])) {
                $stmt = $conn->prepare("
                    INSERT INTO DIPLOME (
                        candidat_id, nom, annee_obtention, etablissement, scan_url
                    ) VALUES (?, ?, ?, ?, ?)
                ");
                
                foreach ($_POST['diplome_nom'] as $index => $nom) {
                    if (isset($_FILES['diplome_scan']['name'][$index]) && $_FILES['diplome_scan']['error'][$index] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $_FILES['diplome_scan']['name'][$index],
                            'type' => $_FILES['diplome_scan']['type'][$index],
                            'tmp_name' => $_FILES['diplome_scan']['tmp_name'][$index],
                            'error' => $_FILES['diplome_scan']['error'][$index],
                            'size' => $_FILES['diplome_scan']['size'][$index]
                        ];
                        
                        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        $new_filename = $candidat_id . '_diplome_' . $index . '_' . time() . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                            $stmt->execute([
                                $candidat_id,
                                $nom,
                                $_POST['diplome_annee'][$index],
                                $_POST['diplome_etablissement'][$index],
                                $upload_path
                            ]);
                        }
                    }
                }
            }
            
            // Traitement des documents
            $document_types = [
                'piece_identite' => 'Carte d\'identité',
                'cmu' => 'CMU',
                'extrait_naissance' => 'Extrait de naissance',
                'photo' => 'Photo d\'identité'
            ];
            
            foreach ($document_types as $field => $type) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES[$field];
                    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $new_filename = $candidat_id . '_' . $field . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $stmt = $conn->prepare("
                            INSERT INTO DOCUMENT (candidat_id, type_document, fichier_url)
                            VALUES (?, ?, ?)
                        ");
                        $stmt->execute([$candidat_id, $type, $upload_path]);
                    }
                }
            }
            
            // Validation de la transaction
            $conn->commit();
            
            // Redirection vers la page de succès
            $_SESSION['success'] = "Inscription réussie ! Votre numéro d'inscription est : " . $num_inscription;
            header('Location: inscription_success.php');
            exit;
            
        } catch (Exception $e) {
            // En cas d'erreur, on annule la transaction
            $conn->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        $_SESSION['errors'] = ["Une erreur est survenue lors de l'inscription. Veuillez réessayer."];
        header('Location: inscription.php');
        exit;
    }
} else {
    header('Location: inscription.php');
    exit;
} 