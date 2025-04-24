<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Veuillez vous connecter pour accéder à cette page.";
    header("Location: ../index.php");
    exit();
}

// Récupérer la photo d'identité du candidat
require_once __DIR__ . '/../../config/database.php';
$stmt = $conn->prepare("
    SELECT d.fichier_url 
    FROM DOCUMENT d 
    JOIN CANDIDAT c ON d.candidat_id = c.id 
    WHERE c.utilisateur_id = ? AND d.type_document = 'Photo d\'identité'
    ORDER BY d.id DESC 
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$photo = $stmt->fetch(PDO::FETCH_ASSOC);
$photo_url = $photo ? '../../' . $photo['fichier_url'] : '../../assets/images/profile.png';

// Récupérer les informations du candidat
$stmt = $conn->prepare("
    SELECT * FROM CANDIDAT 
    WHERE utilisateur_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$candidat = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les documents du candidat
$stmt = $conn->prepare("
    SELECT * FROM DOCUMENT 
    WHERE candidat_id = (
        SELECT id FROM CANDIDAT 
        WHERE utilisateur_id = ?
    )
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations de connexion
$stmt = $conn->prepare("
    SELECT email, created_at, updated_at 
    FROM UTILISATEUR 
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les diplômes du candidat
$stmt = $conn->prepare("
    SELECT * FROM DIPLOME 
    WHERE candidat_id = (
        SELECT id FROM CANDIDAT 
        WHERE utilisateur_id = ?
    )
    ORDER BY annee_obtention DESC
");
$stmt->execute([$_SESSION['user_id']]);
$diplomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// L'utilisateur est connecté, on peut afficher la page
$user_name = $_SESSION['user_name'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>PUBLIGEST CI</title>
</head>

    <body>

        <header>
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="logo">
            </div>
            
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="menu">
            </ul>
            <div class="logout-btn">
                <a href="logout.php" class="btn-deconnexion">Déconnexion</a>
            </div>
        </header>
        <section class="slider-main">
            <div class="container">
                <div class="logo">
                    <h1 href="#">PUBLIGEST_CI</h1>
                </div>
                <div class="slider-content-wrap">                
                    <div class="slider-content">
                        <h2 class="heading-style-2">Bienvenu candidat <?php echo $user_name; ?></h2>
                        <p>Bienvenu sur la plateforme de gestion des concours de la fonction publique côte d'ivoire</p>
                        <div class="social-icons">
                            <a href="#"><img src="https://www.yudiz.com/codepen/headphone-slider/instagram-icon.svg" alt="instagram"></a>
                            <a href="#"><img src="https://www.yudiz.com/codepen/headphone-slider/facbook-icon.svg" alt="facebook"></a>
                            <a href="#"><img src="https://www.yudiz.com/codepen/headphone-slider/twiter-icon.svg" alt="twitter"></a>
                        </div>
                    </div>               
                </div>         
            </div>
            <div class="slider-images">       
                <img class="slider-image" src="../../assets/images/carousselimg/c1.png" alt="headphone image">        
                <img class="slider-image" src="../../assets/images/carousselimg/caroussel2.png" alt="headphone image">        
                <img class="slider-image" src="../../assets/images/carousselimg/carroussel1.png" alt="headphone image">        
                <img class="slider-image" src="../../assets/images/carousselimg/c2.png" alt="headphone image">        
                <img class="slider-image" src="../../assets/images/carousselimg/Male Student with Books Background Removed.png" alt="headphone image">        
            </div> 
            <div id="backgrounds">
                <div class="background" style="background: radial-gradient(50% 50% at 50% 50%, #f5dbc3 0%, #f7883e 92.19%);"></div>
                <div class="background" style="background: radial-gradient(50% 50% at 50% 50%, #D7D7D7 0%, #979797 100%);"></div>
                <div class="background" style="background: radial-gradient(50% 50% at 50% 50%,  #C7F6D0 0%, #7CB686 100%);"></div>
                <div class="background" style="background: radial-gradient(50% 50% at 50% 50%, #b2b8ff 0%, #3683ffe0 100%);"></div>
                <div class="background" style="background: radial-gradient(50% 50% at 50% 50%, #6B6B6B 0%, #292929 100%);"></div>
            </div>
        </section>   







    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="profile-picture">
                    <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="Photo d'identité">
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo $user_name; ?></div>
                    <div class="user-id">ID: <?php echo $_SESSION['user_id']; ?></div>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <div class="menu-section">
                    <h3>Menu</h3>
                    <ul>
                        <li class="active" data-section="dashboard">
                            <a href="#" data-target="dashboard"><i class="fas fa-chart-line"></i> Suivi des étapes par concours</a>
                        </li>
                        <li data-section="types">
                            <a href="#" data-target="types"><i class="fas fa-list"></i> profil candidat</a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3>Profil candidat</h3>
                    <ul>
                        <li data-section="postuler"><a href="#" data-target="postuler"><i class="fas fa-file-alt"></i> Postuler à un concours</a></li>
                        <li data-section="suivi"><a href="#" data-target="suivi"><i class="fas fa-tasks"></i> Suivi des étapes</a></li>
                        <li data-section="dossier"><a href="#" data-target="dossier"><i class="fas fa-folder"></i> Mon dossier en ligne</a></li>
                        <li data-section="fiches"><a href="#" data-target="fiches"><i class="fas fa-receipt"></i> Fiches & reçus</a></li>
                        <li data-section="login"><a href="#" data-target="login"><i class="fas fa-key"></i> Login et mot de passe</a></li>
                        <li data-section="formations"><a href="#" data-target="formations"><i class="fas fa-graduation-cap"></i> Formations / Diplômes</a></li>
                        <li data-section="paiement"><a href="#" data-target="paiement"><i class="fas fa-money-bill-wave"></i> Régulariser un paiement</a></li>
                        <li data-section="cours"><a href="#" data-target="cours"><i class="fas fa-book"></i> Cours de préparation</a></li>
                        <li data-section="reclamations"><a href="#" data-target="reclamations"><i class="fas fa-exclamation-circle"></i> Réclamations</a></li>
                    </ul>
                </div>
            </nav>

       
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Section -->
            <div class="content-section active" id="dashboard">
                <div class="content-header">
                    <h1>Tableau de bord</h1>
                    <div class="user-actions">
                        <button class="notification-btn" data-tooltip="Notifications"><i class="fas fa-bell"></i></button>
                        <button class="help-btn" data-tooltip="Aide"><i class="fas fa-question-circle"></i></button>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="widget">
                        <div class="widget-header">
                            <h3>Mes concours en cours</h3>
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="widget-content">
                            <div class="concours-item">
                                <i class="fas fa-graduation-cap"></i>
                                <div class="concours-info">
                                    <h4>Concours ENA 2025</h4>
                                    <p>Date limite: 15/06/2025</p>
                                </div>
                                <span class="status-badge status-en-cours">En cours</span>
                            </div>
                        </div>
                    </div>

                    <div class="widget">
                        <div class="widget-header">
                            <h3>Prochaines étapes</h3>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="widget-content">
                            <div class="calendar">
                                <!-- Calendar content will be added dynamically -->
                            </div>
                        </div>
                    </div>

                    <div class="widget">
                        <div class="widget-header">
                            <h3>Notifications récentes</h3>
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="widget-content">
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="notification-content">
                                    <h4>Nouvelle étape disponible</h4>
                                    <p>Votre dossier a été validé pour le concours ENA 2025</p>
                                    <span class="notification-time">Il y a 2 heures</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Sections -->

            <div class="content-section" id="types">
                <div class="content-header">
                    <h1>Profil Candidat</h1>
                </div>
                <div class="section-content">
                    <div class="profile-container">
                        <div class="profile-section">
                            <h2>Informations Personnelles</h2>
                            <div class="profile-grid">
                                <div class="profile-item">
                                    <span class="label">Nom</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['nom']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Prénoms</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['prenoms']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Sexe</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['sexe']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Date de naissance</span>
                                    <span class="value"><?php echo date('d/m/Y', strtotime($candidat['date_naissance'])); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Lieu de naissance</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['lieu_naissance']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Nationalité</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['nationalite']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Situation matrimoniale</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['situation_matrimoniale']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="profile-section">
                            <h2>Coordonnées</h2>
                            <div class="profile-grid">
                                <div class="profile-item">
                                    <span class="label">Téléphone principal</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['telephone_principal']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Téléphone secondaire</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['telephone_secondaire']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Adresse postale</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['adresse_postale']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Région</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['region']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Département</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['departement']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Commune</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['commune']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Lieu de résidence</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['lieu_residence']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="profile-section">
                            <h2>Pièce d'identité</h2>
                            <div class="profile-grid">
                                <div class="profile-item">
                                    <span class="label">Type de pièce</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['type_piece']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Numéro de pièce</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['num_piece']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Date d'expiration</span>
                                    <span class="value"><?php echo date('d/m/Y', strtotime($candidat['expiration_piece'])); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="profile-section">
                            <h2>Informations complémentaires</h2>
                            <div class="profile-grid">
                                <div class="profile-item">
                                    <span class="label">Type de candidat</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['type_candidat']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Numéro d'inscription</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['num_inscription']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Permis de conduire</span>
                                    <span class="value"><?php echo $candidat['permis'] ? 'Oui' : 'Non'; ?></span>
                                </div>
                                <?php if ($candidat['permis']): ?>
                                <div class="profile-item">
                                    <span class="label">Type de permis</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['type_permis']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="profile-item">
                                    <span class="label">Handicap</span>
                                    <span class="value"><?php echo $candidat['handicap'] ? 'Oui' : 'Non'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="profile-section">
                            <h2>Informations familiales</h2>
                            <div class="profile-grid">
                                <div class="profile-item">
                                    <span class="label">Nom du père</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['nom_pere']); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="label">Nom de la mère</span>
                                    <span class="value"><?php echo htmlspecialchars($candidat['nom_mere']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-section" id="postuler">
                <div class="content-header">
                    <h1>Postuler à un concours</h1>
                </div>
                <div class="section-content">
                    <!-- Postuler content will be loaded here -->
                </div>
            </div>
            <div class="content-section" id="suivi">
                <div class="content-header">
                    <h1>Suivi des étapes</h1>
                </div>
                <div class="section-content">
                    <!-- Suivi content will be loaded here -->
                </div>
            </div>
            <div class="content-section" id="dossier">
                <div class="content-header">
                    <h1>Mon dossier en ligne</h1>
                </div>
                <div class="section-content">
                    <div class="documents-container">
                        <div class="documents-header">
                            <h2>Mes documents</h2>
                            <p class="documents-description">Retrouvez ici tous les documents que vous avez déposés pour vos concours.</p>
                        </div>
                        <div class="documents-grid">
                            <?php foreach ($documents as $document): ?>
                                <div class="document-card">
                                    <div class="document-icon">
                                        <?php
                                        $extension = pathinfo($document['fichier_url'], PATHINFO_EXTENSION);
                                        $icon = 'fa-file';
                                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            $icon = 'fa-image';
                                        } elseif ($extension === 'pdf') {
                                            $icon = 'fa-file-pdf';
                                        }
                                        ?>
                                        <i class="fas <?php echo $icon; ?>"></i>
                                    </div>
                                    <div class="document-info">
                                        <h3><?php echo htmlspecialchars($document['type_document']); ?></h3>
                                        <p class="document-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($document['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div class="document-actions">
                                        <a href="<?php echo '../../' . htmlspecialchars($document['fichier_url']); ?>" 
                                           target="_blank" 
                                           class="view-btn" 
                                           title="Voir le document">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo '../../' . htmlspecialchars($document['fichier_url']); ?>" 
                                           download 
                                           class="download-btn" 
                                           title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-section" id="fiches">
                <div class="content-header">
                    <h1>Fiches & reçus</h1>
                </div>
                <div class="section-content">
                    <!-- Fiches content will be loaded here -->
                </div>
            </div>
            <div class="content-section" id="login">
                <div class="content-header">
                    <h1>Login et mot de passe</h1>
                </div>
                <div class="section-content">
                    <div class="login-container">
                        <div class="login-info">
                            <div class="info-card">
                                <div class="info-header">
                                    <i class="fas fa-user-circle"></i>
                                    <h2>Informations de connexion</h2>
                                </div>
                                <div class="info-content">
                                    <div class="info-item">
                                        <span class="label">Email</span>
                                        <span class="value"><?php echo htmlspecialchars($utilisateur['email']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Date de création du compte</span>
                                        <span class="value"><?php echo date('d/m/Y H:i', strtotime($utilisateur['created_at'])); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Dernière mise à jour</span>
                                        <span class="value"><?php echo date('d/m/Y H:i', strtotime($utilisateur['updated_at'])); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="password-actions">
                                <button class="change-password-btn" onclick="showPasswordModal()">
                                    <i class="fas fa-key"></i>
                                    Changer le mot de passe
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal pour changer le mot de passe -->
            <div id="passwordModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Changer le mot de passe</h2>
                        <span class="close-modal" onclick="hidePasswordModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="changePasswordForm" class="password-form">
                            <div class="form-group">
                                <label for="currentPassword">Mot de passe actuel</label>
                                <input type="password" id="currentPassword" name="currentPassword" required>
                            </div>
                            <div class="form-group">
                                <label for="newPassword">Nouveau mot de passe</label>
                                <input type="password" id="newPassword" name="newPassword" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirmer le nouveau mot de passe</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" required>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="hidePasswordModal()">Annuler</button>
                                <button type="submit" class="submit-btn">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="content-section" id="formations">
                <div class="content-header">
                    <h1>Formations / Diplômes</h1>
                </div>
                <div class="section-content">
                    <div class="diplomas-container">
                        <div class="diplomas-header">
                            <h2>Mes diplômes</h2>
                            <p class="diplomas-description">Retrouvez ici tous vos diplômes et formations.</p>
                        </div>
                        
                        <div class="diplomas-grid">
                            <?php foreach ($diplomes as $diplome): ?>
                                <div class="diploma-card">
                                    <div class="diploma-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="diploma-info">
                                        <h3><?php echo htmlspecialchars($diplome['nom']); ?></h3>
                                        <div class="diploma-details">
                                            <div class="detail-item">
                                                <i class="fas fa-layer-group"></i>
                                                <span><?php echo htmlspecialchars($diplome['niveau'] ?? 'Non spécifié'); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>Année d'obtention: <?php echo htmlspecialchars($diplome['annee_obtention']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-university"></i>
                                                <span><?php echo htmlspecialchars($diplome['etablissement']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="diploma-actions">
                                        <?php if ($diplome['scan_url']): ?>
                                            <a href="<?php echo '../../' . htmlspecialchars($diplome['scan_url']); ?>" 
                                               target="_blank" 
                                               class="view-btn" 
                                               title="Voir le diplôme">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo '../../' . htmlspecialchars($diplome['scan_url']); ?>" 
                                               download 
                                               class="download-btn" 
                                               title="Télécharger">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>


            <div class="content-section" id="paiement">
                <div class="content-header">
                    <h1>Régulariser un paiement</h1>
                </div>
                <div class="section-content">
                    <!-- Paiement content will be loaded here -->
                </div>
            </div>
            <div class="content-section" id="cours">
                <div class="content-header">
                    <h1>Cours de préparation</h1>
                </div>
                <div class="section-content">
                    <!-- Cours content will be loaded here -->
                </div>
            </div>
            <div class="content-section" id="reclamations">
                <div class="content-header">
                    <h1>Réclamations</h1>
                </div>
                <div class="section-content">
                    <!-- Réclamations content will be loaded here -->
                </div>
            </div>
           
            
        </main>
    </div>

    



<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>Contactez-nous</h3>
            <p>Email: info@publigest.ci</p>
            <p>Téléphone: +225 XX XX XX XX</p>
            <p>Adresse: Abidjan, Côte d'Ivoire</p>
        </div>
        <div class="footer-section">
            <h3>Liens Rapides</h3>
            <ul>
                <li><a href="#">Accueil</a></li>
                <li><a href="#">A propos</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">Concours</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Suivez-nous</h3>
            <div class="social-links">
                <a href="#"><img src="../../assets/images/facebook.png" alt="Facebook"></a>
                <a href="#"><img src="../../assets/images/Instagram.png" alt="Twitter"></a>
                <a href="#"><img src="../../assets/images/LinkedIn.webp" alt="LinkedIn"></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 PUBLIGEST CI. Tous droits réservés.</p>
    </div>
</footer>
        <script src="../../assets/js/dashboard.js"></script> 
    </body>

</html>


