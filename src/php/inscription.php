<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Affichage des erreurs s'il y en a
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
unset($_SESSION['errors']);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation et traitement des données
    // ... (à implémenter)
}

// Fonction pour générer un numéro d'inscription unique
function generateNumInscription() {
    return 'CAND-' . date('Ymd') . '-' . strtoupper(uniqid());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Candidat</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/inscription.css">
    <!-- <style>
        :root {
            --primary-color: #F47721; /* Orange */
            --secondary-color: #0E9F60; /* Vert */
            --accent-color: #FFFFFF; /* Blanc */
            --text-color: #2C3E50;
            --light-bg: #F8FAFC;
            --gradient-primary: linear-gradient(135deg, #F47721 0%, #FF9F4A 100%);
            --gradient-secondary: linear-gradient(135deg, #0E9F60 0%, #2ECC71 100%);
        }
        
        body {
            background: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
        }
        
        .inscription-container {
            max-width: 1200px;
            margin: 2rem auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(244, 119, 33, 0.1);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding: 2rem;
            background: var(--gradient-primary);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .form-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="rgba(255,255,255,0.1)" d="M0 0h100v100H0z"/></svg>');
            opacity: 0.1;
        }
        
        .form-header h2 {
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .warning-box {
            background: linear-gradient(135deg, #FF4444 0%, #CC0000 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 0 2rem 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .warning-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0 2rem;
            position: relative;
        }
        
        .step {
            position: relative;
            flex: 1;
            text-align: center;
            padding: 1rem;
            z-index: 1;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 0;
            width: 100%;
            height: 2px;
            background: #E2E8F0;
            z-index: -1;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .step.active .step-circle {
            background: var(--gradient-primary);
            color: white;
            transform: scale(1.1);
        }
        
        .step.completed .step-circle {
            background: var(--gradient-secondary);
            color: white;
        }
        
        .form-section {
            padding: 2rem;
            display: none;
            background: white;
            border-radius: 15px;
            margin: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid rgba(244, 119, 33, 0.1);
        }
        
        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-bg);
        }
        
        .form-control, .form-select {
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(244, 119, 33, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .required-field::after {
            content: "*";
            color: #E53E3E;
            margin-left: 4px;
        }
        
        .step-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #64748B;
            transition: all 0.3s ease;
        }
        
        .step.active .step-title {
            color: var(--primary-color);
            transform: scale(1.05);
        }
        
        .step.completed .step-title {
            color: var(--secondary-color);
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--light-bg);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--gradient-secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .btn-secondary {
            background: #E2E8F0;
            border: none;
            color: var(--text-color);
        }
        
        .btn-secondary:hover {
            background: #CBD5E0;
            transform: translateY(-2px);
        }
        
        .document-upload {
            border: 2px dashed var(--primary-color);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(244, 119, 33, 0.05);
        }
        
        .document-upload:hover {
            background: rgba(244, 119, 33, 0.1);
            transform: translateY(-2px);
        }
        
        .document-upload i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .document-info {
            font-size: 0.8rem;
            color: #64748B;
            margin-top: 0.5rem;
        }
        
        .document-requirements {
            background: #F8FAFC;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #64748B;
        }
        
        .document-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0.5rem 0;
        }
        
        .document-requirements li {
            margin: 0.3rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .document-requirements li i {
            color: var(--secondary-color);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .inscription-container {
                margin: 1rem;
                border-radius: 15px;
            }
            
            .step-indicator {
                display: none;
            }
            
            .form-section {
                padding: 1rem;
                margin: 0.5rem;
            }
            
            .warning-box {
                margin: 0 1rem 20px;
            }
            
            .form-header h2 {
                font-size: 1.8rem;
            }
        }
    </style> -->
</head>
<body>
    <div class="inscription-container">
        <div class="form-header">
            <h2>Inscription Candidat</h2>
            <p class="motto">Rejoignez-nous pour une carrière prometteuse</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="warning-box">
                <div class="warning-content">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Attention !</strong>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Indicateur d'étapes -->
        <div class="step-indicator">
            <div class="step active" id="step1-indicator">
                <div class="step-circle">1</div>
                <div class="step-title">Informations de connexion</div>
            </div>
            <div class="step" id="step2-indicator">
                <div class="step-circle">2</div>
                <div class="step-title">Informations personnelles</div>
            </div>
            <div class="step" id="step3-indicator">
                <div class="step-circle">3</div>
                <div class="step-title">Informations de résidence</div>
            </div>
            <div class="step" id="step4-indicator">
                <div class="step-circle">4</div>
                <div class="step-title">Documents</div>
            </div>
        </div>

        <form id="registrationForm" action="process_inscription.php" method="POST" enctype="multipart/form-data">
            <!-- Étape 1: Informations de connexion -->
            <div class="form-section active" id="step1">
                <h3><i class="fas fa-user-lock"></i> Informations de connexion</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label required-field">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="error-message" id="email-error"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mot_de_passe" class="form-label required-field">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        <div class="error-message" id="password-error"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label required-field">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <div class="error-message" id="confirm-password-error"></div>
                    </div>
                </div>
            </div>

            <!-- Étape 2: Informations personnelles -->
            <div class="form-section" id="step2">
                <h3><i class="fas fa-user"></i> Informations personnelles</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label required-field">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prenoms" class="form-label required-field">Prénoms</label>
                        <input type="text" class="form-control" id="prenoms" name="prenoms" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sexe" class="form-label required-field">Sexe</label>
                        <select class="form-select" id="sexe" name="sexe" required>
                            <option value="">Sélectionner</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date_naissance" class="form-label required-field">Date de naissance</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="lieu_naissance" class="form-label required-field">Lieu de naissance</label>
                        <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nationalite" class="form-label required-field">Nationalité</label>
                        <input type="text" class="form-control" id="nationalite" name="nationalite" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="situation_matrimoniale" class="form-label">Situation matrimoniale</label>
                        <select class="form-select" id="situation_matrimoniale" name="situation_matrimoniale">
                            <option value="">Sélectionner</option>
                            <option value="Célibataire">Célibataire</option>
                            <option value="Marié(e)">Marié(e)</option>
                            <option value="Divorcé(e)">Divorcé(e)</option>
                            <option value="Veuf(ve)">Veuf(ve)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nom_pere" class="form-label required-field">Nom du père</label>
                        <input type="text" class="form-control" id="nom_pere" name="nom_pere" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nom_mere" class="form-label required-field">Nom de la mère</label>
                        <input type="text" class="form-control" id="nom_mere" name="nom_mere" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type_candidat" class="form-label required-field">Type de candidat</label>
                        <select class="form-select" id="type_candidat" name="type_candidat" required>
                            <option value="">Sélectionner</option>
                            <option value="Interne">Interne</option>
                            <option value="Externe">Externe</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="handicap" class="form-label">Handicap</label>
                        <select class="form-select" id="handicap" name="handicap">
                            <option value="0">Non</option>
                            <option value="1">Oui</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="permis" class="form-label">Permis de conduire</label>
                        <select class="form-select" id="permis" name="permis">
                            <option value="0">Non</option>
                            <option value="1">Oui</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3" id="type_permis_container" style="display: none;">
                        <label for="type_permis" class="form-label">Type de permis</label>
                        <select class="form-select" id="type_permis" name="type_permis">
                            <option value="">Sélectionner</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Étape 3: Informations de résidence -->
            <div class="form-section" id="step3">
                <h3><i class="fas fa-home"></i> Informations de résidence</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telephone_principal" class="form-label required-field">Téléphone principal</label>
                        <input type="tel" class="form-control" id="telephone_principal" name="telephone_principal" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telephone_secondaire" class="form-label">Téléphone secondaire</label>
                        <input type="tel" class="form-control" id="telephone_secondaire" name="telephone_secondaire">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type_piece" class="form-label required-field">Type de pièce d'identité</label>
                        <select class="form-select" id="type_piece" name="type_piece" required>
                            <option value="">Sélectionner</option>
                            <option value="CNI">CNI</option>
                            <option value="Passeport">Passeport</option>
                            <option value="Permis de conduire">Permis de conduire</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="num_piece" class="form-label required-field">Numéro de pièce</label>
                        <input type="text" class="form-control" id="num_piece" name="num_piece" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="expiration_piece" class="form-label required-field">Date d'expiration</label>
                        <input type="date" class="form-control" id="expiration_piece" name="expiration_piece" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="adresse_postale" class="form-label required-field">Adresse postale</label>
                        <input type="text" class="form-control" id="adresse_postale" name="adresse_postale" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="region" class="form-label required-field">Région</label>
                        <input type="text" class="form-control" id="region" name="region" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="departement" class="form-label required-field">Département</label>
                        <input type="text" class="form-control" id="departement" name="departement" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="commune" class="form-label required-field">Commune</label>
                        <input type="text" class="form-control" id="commune" name="commune" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="lieu_residence" class="form-label">Lieu de résidence</label>
                        <input type="text" class="form-control" id="lieu_residence" name="lieu_residence">
                    </div>
                </div>
            </div>

            <!-- Étape 4: Diplômes et Documents -->
            <div class="form-section" id="step4">
                <h3><i class="fas fa-graduation-cap"></i> Diplômes</h3>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div id="diplomes-container">
                            <!-- Premier diplôme -->
                            <div class="diplome-section mb-4">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="diplome_nom[]" class="form-label required-field">Nom du diplôme</label>
                                        <select class="form-select" name="diplome_nom[]" required>
                                            <option value="">Sélectionner</option>
                                            <option value="CEPE">CEPE</option>
                                            <option value="BEPC">BEPC</option>
                                            <option value="BAC">BAC</option>
                                            <option value="BAC+1">BAC+1</option>
                                            <option value="BAC+2">BAC+2</option>
                                            <option value="BAC+3">BAC+3</option>
                                            <option value="BAC+4">BAC+4</option>
                                            <option value="BAC+5">BAC+5</option>
                                            <option value="Autre">Autre</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="diplome_annee[]" class="form-label required-field">Année d'obtention</label>
                                        <input type="number" class="form-control" name="diplome_annee[]" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="diplome_etablissement[]" class="form-label required-field">Établissement</label>
                                        <input type="text" class="form-control" name="diplome_etablissement[]" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="document-upload">
                                            <i class="fas fa-file-pdf"></i>
                                            <label for="diplome_scan[]" class="form-label required-field">Scan du diplôme</label>
                                            <input type="file" class="form-control" name="diplome_scan[]" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <div class="document-info">Format accepté : PDF, JPG, PNG</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" id="add-diplome">
                            <i class="fas fa-plus"></i> Ajouter un autre diplôme
                        </button>
                    </div>
                </div>

                <h3 class="mt-4"><i class="fas fa-file-upload"></i> Documents à fournir</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="document-upload">
                            <i class="fas fa-id-card"></i>
                            <label for="piece_identite" class="form-label required-field">Carte d'identité</label>
                            <input type="file" class="form-control" id="piece_identite" name="piece_identite" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="document-info">Format accepté : PDF, JPG, PNG</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="document-upload">
                            <i class="fas fa-file-medical"></i>
                            <label for="cmu" class="form-label required-field">CMU (Couverture Maladie Universelle)</label>
                            <input type="file" class="form-control" id="cmu" name="cmu" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="document-info">Format accepté : PDF, JPG, PNG</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="document-upload">
                            <i class="fas fa-birthday-cake"></i>
                            <label for="extrait_naissance" class="form-label required-field">Extrait de naissance</label>
                            <input type="file" class="form-control" id="extrait_naissance" name="extrait_naissance" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="document-info">Format accepté : PDF, JPG, PNG</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="document-upload">
                            <i class="fas fa-camera"></i>
                            <label for="photo" class="form-label required-field">Photo d'identité</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.jpeg,.png" required>
                            <div class="document-info">Format accepté : JPG, PNG</div>
                        </div>
                    </div>
                </div>
                
                <div class="document-requirements">
                    <h5>Exigences des documents :</h5>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Taille maximale : 5 Mo par fichier</li>
                        <li><i class="fas fa-check-circle"></i> Formats acceptés : PDF, JPG, PNG</li>
                        <li><i class="fas fa-check-circle"></i> Documents clairs et lisibles</li>
                        <li><i class="fas fa-check-circle"></i> Photo d'identité récente (moins de 6 mois)</li>
                    </ul>
                </div>
            </div>

            <!-- Boutons de navigation -->
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Précédent
                </button>
                <button type="button" class="btn btn-primary" id="nextBtn">
                    Suivant <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                    <i class="fas fa-check"></i> S'inscrire
                </button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../../assets/js/inscription.js"></script>
</body>
</html> 