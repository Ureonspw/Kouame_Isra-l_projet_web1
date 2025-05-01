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
    <title>Inscription Candidato</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/inscription.css">
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

        <form id="registrationForm" action="process_inscription_candidat.php" method="POST" enctype="multipart/form-data">
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