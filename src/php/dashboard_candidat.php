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
$photo_url = $photo ? '/uploads/documents/' . basename($photo['fichier_url']) : '/assets/images/profile.png';

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

// Récupérer les sessions de concours disponibles
$stmt = $conn->prepare("
    SELECT sc.*, c.nom as concours_nom, c.description as concours_description
    FROM SESSION_CONCOURS sc
    JOIN CONCOURS c ON sc.concours_id = c.id
    WHERE sc.date_cloture >= CURDATE()
    ORDER BY sc.date_ouverture DESC
");
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les centres d'examen pour chaque session
$centres = [];
foreach ($sessions as $session) {
    $stmt = $conn->prepare("
        SELECT * FROM CENTRE_EXAMEN
        WHERE session_id = ?
        ORDER BY ville
    ");
    $stmt->execute([$session['id']]);
    $centres[$session['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les inscriptions du candidat avec leurs statuts
$stmt = $conn->prepare("
    SELECT 
        i.*,
        c.nom as concours_nom,
        c.description as concours_description,
        sc.date_ouverture,
        sc.date_cloture,
        sc.nb_places,
        ce.ville as centre_ville,
        ce.lieu as centre_lieu,
        ce.capacite as centre_capacite,
        r.decision as resultat,
        r.note as note_resultat
    FROM INSCRIPTION i
    JOIN SESSION_CONCOURS sc ON i.session_id = sc.id
    JOIN CONCOURS c ON sc.concours_id = c.id
    LEFT JOIN CENTRE_EXAMEN ce ON i.centre_id = ce.id
    LEFT JOIN RESULTAT r ON r.inscription_id = i.id
    WHERE i.candidat_id = (
        SELECT id FROM CANDIDAT 
        WHERE utilisateur_id = ?
    )
    ORDER BY i.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre de concours disponibles
$stmt = $conn->prepare("
    SELECT COUNT(*) as nb_concours
    FROM SESSION_CONCOURS sc
    WHERE sc.date_cloture >= CURDATE()
");
$stmt->execute();
$nb_concours = $stmt->fetch(PDO::FETCH_ASSOC)['nb_concours'];

// Récupérer les inscriptions sans paiement
$stmt = $conn->prepare("
    SELECT 
        i.id as inscription_id,
        sc.concours_id,
        c.nom as concours_nom,
        c.description as concours_description,
        sc.date_ouverture,
        sc.date_cloture
    FROM INSCRIPTION i
    JOIN SESSION_CONCOURS sc ON i.session_id = sc.id
    JOIN CONCOURS c ON sc.concours_id = c.id
    LEFT JOIN PAIEMENT p ON p.inscription_id = i.id
    WHERE i.candidat_id = (
        SELECT id FROM CANDIDAT 
        WHERE utilisateur_id = ?
    )
    AND p.id IS NULL
    ORDER BY i.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$inscriptions_sans_paiement = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les paiements des inscriptions
$stmt = $conn->prepare("
    SELECT 
        p.*,
        i.id as inscription_id,
        sc.concours_id,
        c.nom as concours_nom,
        sc.date_ouverture,
        sc.date_cloture
    FROM PAIEMENT p
    JOIN INSCRIPTION i ON p.inscription_id = i.id
    JOIN SESSION_CONCOURS sc ON i.session_id = sc.id
    JOIN CONCOURS c ON sc.concours_id = c.id
    WHERE i.candidat_id = (
        SELECT id FROM CANDIDAT 
        WHERE utilisateur_id = ?
    )
    ORDER BY p.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                        <li data-section="dossier"><a href="#" data-target="dossier"><i class="fas fa-folder"></i> Mon dossier en ligne</a></li>
                        <li data-section="login"><a href="#" data-target="login"><i class="fas fa-key"></i> Login et mot de passe</a></li>
                        <li data-section="formations"><a href="#" data-target="formations"><i class="fas fa-graduation-cap"></i> Formations / Diplômes</a></li>
                        <li data-section="paiement"><a href="#" data-target="paiement"><i class="fas fa-money-bill-wave"></i> Régulariser un paiement</a></li>
                        <li data-section="revision"><a href="#" data-target="revision"><i class="fas fa-edit"></i> Revision IA</a></li>

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
                            <h3>Mes inscriptions concours</h3>
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="widget-content">
                            <?php if (empty($inscriptions)): ?>
                                <p class="no-inscription" style="text-align: center; color: #666; font-style: italic; padding: 20px; background: #f8f9fa; border-radius: 8px;">Aucune inscription en cours</p>
                            <?php else: ?>
                                <div style="display: grid; gap: 15px;">
                                    <?php foreach ($inscriptions as $inscription): ?>
                                        <div style="background: #ffffff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow: hidden;">
                                            <div style="display: flex; align-items: center; padding: 15px; border-bottom: 1px solid #eee;">
                                                <div style="width: 40px; height: 40px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                    <i class="fas fa-graduation-cap" style="font-size: 20px; color: #2c3e50;"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <h4 style="margin: 0 0 5px 0; color: #2c3e50; font-size: 1.1em;"><?php echo htmlspecialchars($inscription['concours_nom']); ?></h4>
                                                    <p style="margin: 0; color: #666; font-size: 0.9em;">
                                                        <i class="fas fa-calendar-alt" style="margin-right: 5px; color: #666;"></i>
                                                        Date limite: <?php echo date('d/m/Y', strtotime($inscription['date_cloture'])); ?>
                                                    </p>
                                                </div>
                                                <span style="padding: 8px 15px; border-radius: 20px; font-size: 0.8em; font-weight: bold; text-transform: uppercase; background-color: <?php 
                                                    echo $inscription['statut'] === 'valide' ? '#27ae60' : 
                                                        ($inscription['statut'] === 'en_attente' ? '#f39c12' : '#e74c3c'); 
                                                ?>; color: white;">
                                                    <?php echo ucfirst($inscription['statut']); ?>
                                                </span>
                                            </div>
                                            <?php if ($inscription['statut'] === 'valide'): ?>
                                                <div style="background: #ecf0f1; padding: 12px 15px; display: flex; align-items: center;">
                                                    <i class="fas fa-file-alt" style="color: #3498db; margin-right: 10px;"></i>
                                                    <span style="color: #3498db; font-size: 0.9em;">
                                                        Votre convocation est disponible dans la section "Mon dossier en ligne"
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="widget">
                        <div class="widget-header">
                            <h3>Nombre de concours disponibles</h3>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="widget-content">
                            <div style="text-align: center; padding: 30px 20px; background: #f8f9fa; border-radius: 8px;">
                                <span style="font-size: 48px; font-weight: bold; color: #2c3e50; display: block; line-height: 1;"><?php echo $nb_concours; ?></span>
                                <p style="margin: 10px 0 0 0; color: #666; font-size: 1.1em;">concours ouverts</p>
                            </div>
                        </div>
                    </div>

                    <div class="widget">
                        <div class="widget-header">
                            <h3>Résultats des concours</h3>
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="widget-content">
                            <?php if (empty($inscriptions)): ?>
                                <p style="text-align: center; color: #666; font-style: italic; padding: 20px; background: #f8f9fa; border-radius: 8px;">Aucun résultat disponible</p>
                            <?php else: ?>
                                <div style="display: grid; gap: 15px;">
                                    <?php foreach ($inscriptions as $inscription): ?>
                                        <?php if ($inscription['resultat']): ?>
                                            <div style="background: #ffffff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); padding: 15px; display: flex; align-items: center;">
                                                <div style="width: 40px; height: 40px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                    <i class="fas fa-info-circle" style="font-size: 20px; color: #3498db;"></i>
                                                </div>
                                                <div>
                                                    <h4 style="margin: 0 0 5px 0; color: #2c3e50; font-size: 1.1em;"><?php echo htmlspecialchars($inscription['concours_nom']); ?></h4>
                                                    <p style="margin: 0; color: #666; font-size: 0.9em;">
                                                        <i class="fas fa-check-circle" style="margin-right: 5px; color: #666;"></i>
                                                        Résultat: <?php echo ucfirst($inscription['resultat']); ?>
                                                    </p>
                                                    <?php if ($inscription['note_resultat'] !== null): ?>
                                                        <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">
                                                            <i class="fas fa-star" style="margin-right: 5px; color: #f1c40f;"></i>
                                                            Note: <?php echo number_format($inscription['note_resultat'], 2); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
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
                    <div class="postuler-container">
                        <!-- Section des inscriptions existantes -->
                        <div class="inscriptions-existantes">
                            <h2>Mes inscriptions en cours</h2>
                            <div class="inscriptions-grid">
                                <?php foreach ($inscriptions as $inscription): ?>
                                    <div class="inscription-card">
                                        <div class="inscription-header">
                                            <h3><?php echo htmlspecialchars($inscription['concours_nom']); ?></h3>
                                            <div class="inscription-actions">
                                                <span class="statut-badge statut-<?php echo $inscription['statut']; ?>">
                                                    <?php echo ucfirst($inscription['statut']); ?>
                                                </span>
                                                <button class="btn-delete" 
                                                        onclick="confirmDelete(<?php echo $inscription['id']; ?>)"
                                                        title="Supprimer l'inscription">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="inscription-content">
                                            <p class="inscription-description">
                                                <?php echo htmlspecialchars($inscription['concours_description']); ?>
                                            </p>
                                            <div class="inscription-details">
                                                <div class="detail-group">
                                                    <h4>Dates importantes</h4>
                                                    <p><i class="fas fa-calendar-check"></i> Ouverture: <?php echo date('d/m/Y', strtotime($inscription['date_ouverture'])); ?></p>
                                                    <p><i class="fas fa-calendar-times"></i> Clôture: <?php echo date('d/m/Y', strtotime($inscription['date_cloture'])); ?></p>
                                                    <p><i class="fas fa-calendar-alt"></i> Date d'inscription: <?php echo date('d/m/Y', strtotime($inscription['date_inscription'])); ?></p>
                                                </div>
                                                <div class="detail-group">
                                                    <h4>Centre d'examen</h4>
                                                    <p><i class="fas fa-map-marker-alt"></i> Ville: <?php echo htmlspecialchars($inscription['centre_ville']); ?></p>
                                                    <p><i class="fas fa-building"></i> Lieu: <?php echo htmlspecialchars($inscription['centre_lieu']); ?></p>
                                                    <p><i class="fas fa-users"></i> Capacité: <?php echo $inscription['centre_capacite'] ?? 'Non spécifiée'; ?></p>
                                                </div>
                                                <div class="detail-group">
                                                    <h4>Informations</h4>
                                                    <p><i class="fas fa-ticket-alt"></i> Places disponibles: <?php echo $inscription['nb_places'] ?? 'Non limité'; ?></p>
                                                    <p><i class="fas fa-clock"></i> Dernière mise à jour: <?php echo date('d/m/Y H:i', strtotime($inscription['created_at'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Section des concours disponibles -->
                        <div class="concours-disponibles">
                            <h2>Concours disponibles</h2>
                            <div class="concours-grid">
                                <?php foreach ($sessions as $session): ?>
                                    <div class="concours-card">
                                        <div class="concours-header">
                                            <h3><?php echo htmlspecialchars($session['concours_nom']); ?></h3>
                                            <div class="concours-dates">
                                                <span class="date-ouverture">
                                                    <i class="fas fa-door-open"></i> 
                                                    Ouverture: <?php echo date('d/m/Y', strtotime($session['date_ouverture'])); ?>
                                                </span>
                                                <span class="date-cloture">
                                                    <i class="fas fa-door-closed"></i> 
                                                    Clôture: <?php echo date('d/m/Y', strtotime($session['date_cloture'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="concours-content">
                                            <p class="concours-description">
                                                <?php echo htmlspecialchars($session['concours_description']); ?>
                                            </p>
                                            <div class="concours-info">
                                                <span class="places-disponibles">
                                                    <i class="fas fa-users"></i>
                                                    Places disponibles: <?php echo $session['nb_places'] ?? 'Non limité'; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="concours-actions">
                                            <button class="btn-inscription" 
                                                    onclick="showInscriptionModal(<?php echo $session['id']; ?>)"
                                                    <?php echo (strtotime($session['date_cloture']) < time()) ? 'disabled' : ''; ?>>
                                                <i class="fas fa-pencil-alt"></i>
                                                S'inscrire
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal d'inscription -->
            <div id="inscriptionModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Inscription au concours</h2>
                        <span class="close-modal" onclick="hideInscriptionModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="inscriptionForm" class="inscription-form">
                            <input type="hidden" id="sessionId" name="session_id">
                            
                            <div class="form-group">
                                <label for="centreExamen">Centre d'examen</label>
                                <select id="centreExamen" name="centre_id" required>
                                    <option value="">Sélectionnez un centre d'examen</option>
                                </select>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="hideInscriptionModal()">Annuler</button>
                                <button type="submit" class="submit-btn">Valider l'inscription</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmation de suppression -->
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Confirmer la suppression</h2>
                        <span class="close-modal" onclick="hideDeleteModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cette inscription ? Cette action est irréversible.</p>
                        <div class="form-actions">
                            <button type="button" class="cancel-btn" onclick="hideDeleteModal()">Annuler</button>
                            <button type="button" class="delete-btn" onclick="deleteInscription()">Supprimer</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                let inscriptionToDelete = null;

                function confirmDelete(inscriptionId) {
                    inscriptionToDelete = inscriptionId;
                    document.getElementById('deleteModal').style.display = 'block';
                }

                function hideDeleteModal() {
                    document.getElementById('deleteModal').style.display = 'none';
                    inscriptionToDelete = null;
                }

                function deleteInscription() {
                    if (!inscriptionToDelete) return;

                    fetch('delete_inscription.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'inscription_id=' + inscriptionToDelete
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Inscription supprimée avec succès !');
                            location.reload();
                        } else {
                            alert('Erreur lors de la suppression: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de la suppression');
                    })
                    .finally(() => {
                        hideDeleteModal();
                    });
                }

                function showInscriptionModal(sessionId) {
                    const modal = document.getElementById('inscriptionModal');
                    const sessionIdInput = document.getElementById('sessionId');
                    const centreSelect = document.getElementById('centreExamen');
                    
                    // Remplir le select des centres d'examen
                    const centres = <?php echo json_encode($centres); ?>;
                    centreSelect.innerHTML = '<option value="">Sélectionnez un centre d\'examen</option>';
                    
                    if (centres[sessionId]) {
                        centres[sessionId].forEach(centre => {
                            const option = document.createElement('option');
                            option.value = centre.id;
                            option.textContent = `${centre.ville} - ${centre.lieu}`;
                            centreSelect.appendChild(option);
                        });
                    }
                    
                    sessionIdInput.value = sessionId;
                    modal.style.display = 'block';
                }

                function hideInscriptionModal() {
                    document.getElementById('inscriptionModal').style.display = 'none';
                }

                // Gérer la soumission du formulaire
                document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch('process_inscription.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Inscription effectuée avec succès !');
                            hideInscriptionModal();
                            location.reload();
                        } else {
                            alert('Erreur lors de l\'inscription: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de l\'inscription');
                    });
                });
            </script>


            <div class="content-section" id="dossier">
                <div class="content-header">
                    <h1>Mon dossier en ligne</h1>
                    <button class="add-document-btn" onclick="showAddDocumentModal()">
                        <i class="fas fa-plus"></i> Ajouter un document
                    </button>
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
                                        <a href="<?php echo '/uploads/documents/' . htmlspecialchars(basename($document['fichier_url'])); ?>" 
                                           target="_blank" 
                                           class="view-btn" 
                                           title="Voir le document">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo '/uploads/documents/' . htmlspecialchars(basename($document['fichier_url'])); ?>" 
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

            <!-- Modal d'ajout de document -->
            <div id="addDocumentModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Ajouter un nouveau document</h2>
                        <span class="close-modal" onclick="hideAddDocumentModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="addDocumentForm" class="document-form" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="typeDocument">Type de document</label>
                                <select id="typeDocument" name="type_document" required onchange="toggleAutreType()">
                                    <option value="">Sélectionnez un type de document</option>
                                    <option value="Photo d'identité">Photo d'identité</option>
                                    <option value="CNI">CNI</option>
                                    <option value="Diplôme">Diplôme</option>
                                    <option value="Attestation">Attestation</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="form-group" id="autreTypeGroup" style="display: none;">
                                <label for="autreType">Précisez le type de document</label>
                                <input type="text" id="autreType" name="autre_type" placeholder="Ex: Certificat de travail">
                            </div>
                            <div class="form-group">
                                <label for="documentFile">Fichier</label>
                                <input type="file" id="documentFile" name="document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small>Formats acceptés : PDF, JPG, JPEG, PNG</small>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="hideAddDocumentModal()">Annuler</button>
                                <button type="submit" class="submit-btn">Ajouter le document</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function showAddDocumentModal() {
                    document.getElementById('addDocumentModal').style.display = 'block';
                }

                function hideAddDocumentModal() {
                    document.getElementById('addDocumentModal').style.display = 'none';
                    // Réinitialiser le formulaire
                    document.getElementById('addDocumentForm').reset();
                    document.getElementById('autreTypeGroup').style.display = 'none';
                }

                function toggleAutreType() {
                    const typeSelect = document.getElementById('typeDocument');
                    const autreTypeGroup = document.getElementById('autreTypeGroup');
                    const autreTypeInput = document.getElementById('autreType');
                    
                    if (typeSelect.value === 'autre') {
                        autreTypeGroup.style.display = 'block';
                        autreTypeInput.required = true;
                    } else {
                        autreTypeGroup.style.display = 'none';
                        autreTypeInput.required = false;
                    }
                }

                // Gérer la soumission du formulaire d'ajout de document
                document.getElementById('addDocumentForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const typeSelect = document.getElementById('typeDocument');
                    const autreTypeInput = document.getElementById('autreType');
                    
                    // Si le type est "autre", utiliser la valeur du champ autre_type comme type_document
                    if (typeSelect.value === 'autre') {
                        formData.set('type_document', autreTypeInput.value);
                    }
                    
                    fetch('add_document.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Document ajouté avec succès !');
                            hideAddDocumentModal();
                            location.reload();
                        } else {
                            alert('Erreur lors de l\'ajout du document: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de l\'ajout du document');
                    });
                });
            </script>


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

            <script>
                function showPasswordModal() {
                    document.getElementById('passwordModal').style.display = 'block';
                }

                function hidePasswordModal() {
                    document.getElementById('passwordModal').style.display = 'none';
                    // Réinitialiser le formulaire
                    document.getElementById('changePasswordForm').reset();
                }

                // Gérer la soumission du formulaire de changement de mot de passe
                document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch('change_password.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Mot de passe modifié avec succès !');
                            hidePasswordModal();
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors du changement de mot de passe');
                    });
                });
            </script>

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
                                            <a href="<?php echo '/uploads/documents/' . htmlspecialchars(basename($diplome['scan_url'])); ?>" 
                                               target="_blank" 
                                               class="view-btn" 
                                               title="Voir le diplôme">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo '/uploads/documents/' . htmlspecialchars(basename($diplome['scan_url'])); ?>" 
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

            <!-- Modal d'ajout de diplôme -->
            <div id="addDiplomaModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Ajouter un nouveau diplôme</h2>
                        <span class="close-modal" onclick="hideAddDiplomaModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="addDiplomaForm" class="diploma-form" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="diplomaName">Nom du diplôme</label>
                                <input type="text" id="diplomaName" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="diplomaLevel">Niveau</label>
                                <select id="diplomaLevel" name="niveau" required>
                                    <option value="">Sélectionnez un niveau</option>
                                    <option value="CEPE">CEPE (Certificat d'Études Primaires Élémentaires)</option>
                                    <option value="BEPC">BEPC (Brevet d'Études du Premier Cycle)</option>
                                    <option value="CAP">CAP (Certificat d'Aptitude Professionnelle)</option>
                                    <option value="BEP">BEP (Brevet d'Études Professionnelles)</option>
                                    <option value="BAC">BAC (Baccalauréat)</option>
                                    <option value="BAC+1">BAC+1</option>
                                    <option value="BAC+2">BAC+2</option>
                                    <option value="BAC+3">BAC+3 (Licence)</option>
                                    <option value="BAC+4">BAC+4 (Master 1)</option>
                                    <option value="BAC+5">BAC+5 (Master 2)</option>
                                    <option value="BAC+6">BAC+6</option>
                                    <option value="BAC+7">BAC+7</option>
                                    <option value="BAC+8">BAC+8</option>
                                    <option value="Doctorat">Doctorat</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="diplomaYear">Année d'obtention</label>
                                <input type="number" id="diplomaYear" name="annee_obtention" min="1900" max="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="diplomaInstitution">Établissement</label>
                                <input type="text" id="diplomaInstitution" name="etablissement" required>
                            </div>
                            <div class="form-group">
                                <label for="diplomaScan">Scan du diplôme</label>
                                <input type="file" id="diplomaScan" name="scan" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small>Formats acceptés : PDF, JPG, JPEG, PNG</small>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="hideAddDiplomaModal()">Annuler</button>
                                <button type="submit" class="submit-btn">Ajouter le diplôme</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function showAddDiplomaModal() {
                    document.getElementById('addDiplomaModal').style.display = 'block';
                }

                function hideAddDiplomaModal() {
                    document.getElementById('addDiplomaModal').style.display = 'none';
                    document.getElementById('addDiplomaForm').reset();
                }

                // Gérer la soumission du formulaire d'ajout de diplôme
                document.getElementById('addDiplomaForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch('add_diploma.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Diplôme ajouté avec succès !');
                            hideAddDiplomaModal();
                            location.reload();
                        } else {
                            alert('Erreur lors de l\'ajout du diplôme: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de l\'ajout du diplôme');
                    });
                });
            </script>

            <div class="content-section" id="paiement">
                <div class="content-header">
                    <h1>Régulariser un paiement</h1>
                </div>
                <div class="section-content">
                    <div class="paiement-container">
                        <!-- Section pour créer un nouveau paiement -->
                        <div class="nouveau-paiement">
                            <h2>Créer un nouveau paiement</h2>
                            <div class="inscriptions-sans-paiement">
                                <?php if (empty($inscriptions_sans_paiement)): ?>
                                    <p class="no-payment">Aucune inscription sans paiement.</p>
                                <?php else: ?>
                                    <div class="inscriptions-grid">
                                        <?php foreach ($inscriptions_sans_paiement as $inscription): ?>
                                            <div class="inscription-card">
                                                <div class="inscription-header">
                                                    <h3><?php echo htmlspecialchars($inscription['concours_nom']); ?></h3>
                                                </div>
                                                <div class="inscription-content">
                                                    <p class="inscription-description">
                                                        <?php echo htmlspecialchars($inscription['concours_description']); ?>
                                                    </p>
                                                    <div class="inscription-details">
                                                        <div class="detail-group">
                                                            <h4>Dates importantes</h4>
                                                            <p><i class="fas fa-calendar-check"></i> Ouverture: <?php echo date('d/m/Y', strtotime($inscription['date_ouverture'])); ?></p>
                                                            <p><i class="fas fa-calendar-times"></i> Clôture: <?php echo date('d/m/Y', strtotime($inscription['date_cloture'])); ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="inscription-actions">
                                                        <button class="btn-payer" onclick="showNouveauPaiementModal(<?php echo $inscription['inscription_id']; ?>)">
                                                            <i class="fas fa-plus-circle"></i>
                                                            Créer un paiement
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Section des paiements en attente -->
                        <div class="paiements-en-attente">
                            <h2>Paiements en attente</h2>
                            <div class="paiements-grid">
                                <?php foreach ($paiements as $paiement): ?>
                                    <?php if ($paiement['statut'] === 'en_attente'): ?>
                                        <div class="paiement-card">
                                            <div class="paiement-header">
                                                <h3><?php echo htmlspecialchars($paiement['concours_nom']); ?></h3>
                                                <span class="statut-badge statut-<?php echo $paiement['statut']; ?>">
                                                    <?php echo ucfirst($paiement['statut']); ?>
                                                </span>
                                            </div>
                                            <div class="paiement-content">
                                                <div class="paiement-details">
                                                    <div class="detail-group">
                                                        <h4>Informations de paiement</h4>
                                                        <p><i class="fas fa-money-bill-wave"></i> Montant: <?php echo number_format($paiement['montant'], 2, ',', ' '); ?> FCFA</p>
                                                        <p><i class="fas fa-credit-card"></i> Mode de paiement: <?php echo htmlspecialchars($paiement['mode_paiement']); ?></p>
                                                        <p><i class="fas fa-calendar"></i> Date: <?php echo date('d/m/Y', strtotime($paiement['date_paiement'])); ?></p>
                                                    </div>
                                                    <div class="detail-group">
                                                        <h4>Informations du concours</h4>
                                                        <p><i class="fas fa-calendar-check"></i> Ouverture: <?php echo date('d/m/Y', strtotime($paiement['date_ouverture'])); ?></p>
                                                        <p><i class="fas fa-calendar-times"></i> Clôture: <?php echo date('d/m/Y', strtotime($paiement['date_cloture'])); ?></p>
                                                    </div>
                                                </div>
                                                <div class="paiement-actions">
                                                    <button class="btn-payer" onclick="showPaiementModal(<?php echo $paiement['id']; ?>)">
                                                        <i class="fas fa-credit-card"></i>
                                                        Modifier le paiement
                                                    </button>
                                                    <button class="btn-valider" onclick="validerPaiement(<?php echo $paiement['id']; ?>)">
                                                        <i class="fas fa-check"></i>
                                                        Valider le paiement
                                                    </button>
                                                    <button class="btn-annuler" onclick="annulerPaiement(<?php echo $paiement['id']; ?>)">
                                                        <i class="fas fa-times"></i>
                                                        Annuler le paiement
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Section des paiements effectués -->
                        <div class="paiements-effectues">
                            <h2>Historique des paiements</h2>
                            <div class="paiements-grid">
                                <?php foreach ($paiements as $paiement): ?>
                                    <?php if ($paiement['statut'] !== 'en_attente'): ?>
                                        <div class="paiement-card">
                                            <div class="paiement-header">
                                                <h3><?php echo htmlspecialchars($paiement['concours_nom']); ?></h3>
                                                <span class="statut-badge statut-<?php echo $paiement['statut']; ?>">
                                                    <?php echo ucfirst($paiement['statut']); ?>
                                                </span>
                                            </div>
                                            <div class="paiement-content">
                                                <div class="paiement-details">
                                                    <div class="detail-group">
                                                        <h4>Informations de paiement</h4>
                                                        <p><i class="fas fa-money-bill-wave"></i> Montant: <?php echo number_format($paiement['montant'], 2, ',', ' '); ?> FCFA</p>
                                                        <p><i class="fas fa-credit-card"></i> Mode de paiement: <?php echo htmlspecialchars($paiement['mode_paiement']); ?></p>
                                                        <p><i class="fas fa-calendar"></i> Date: <?php echo date('d/m/Y', strtotime($paiement['date_paiement'])); ?></p>
                                                    </div>
                                                    <div class="detail-group">
                                                        <h4>Informations du concours</h4>
                                                        <p><i class="fas fa-calendar-check"></i> Ouverture: <?php echo date('d/m/Y', strtotime($paiement['date_ouverture'])); ?></p>
                                                        <p><i class="fas fa-calendar-times"></i> Clôture: <?php echo date('d/m/Y', strtotime($paiement['date_cloture'])); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-section" id="revision">
                <div class="content-header">
                    <h1>Revision ia</h1>
                </div>
                <div class="section-content iarev">
                    <button onclick="window.location.href='../../src/html/iarev.html'">
                        <i class="fas fa-robot"></i>
                        Accéder à l'espace de révision IA
                    </button>
                </div>
            </div>

            <!-- Modal de paiement -->
            <div id="paiementModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Modifier le paiement</h2>
                        <span class="close-modal" onclick="hidePaiementModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="paiementForm" class="paiement-form">
                            <input type="hidden" id="paiementId" name="paiement_id">
                            
                            <div class="form-group">
                                <label for="montant">Montant (FCFA)</label>
                                <input type="number" id="montant" name="montant" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label for="modePaiement">Mode de paiement</label>
                                <select id="modePaiement" name="mode_paiement" required>
                                    <option value="">Sélectionnez un mode de paiement</option>
                                    <option value="Orange Money">Orange Money</option>
                                    <option value="MTN Mobile Money">MTN Mobile Money</option>
                                    <option value="Wave">Wave</option>
                                    <option value="Carte bancaire">Carte bancaire</option>
                                    <option value="Espèces">Espèces</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="datePaiement">Date de paiement</label>
                                <input type="date" id="datePaiement" name="date_paiement" required>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="hidePaiementModal()">Annuler</button>
                                <button type="submit" class="submit-btn">Valider le paiement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal de nouveau paiement -->
            <div id="nouveauPaiementModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Créer un nouveau paiement</h2>
                        <span class="close-modal" onclick="hideNouveauPaiementModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="nouveauPaiementForm" class="paiement-form">
                            <input type="hidden" id="inscriptionId" name="inscription_id">
                            
                            <div class="form-group">
                                <label for="nouveauMontant">Montant (FCFA)</label>
                                <input type="number" id="nouveauMontant" name="montant" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label for="nouveauModePaiement">Mode de paiement</label>
                                <select id="nouveauModePaiement" name="mode_paiement" required>
                                    <option value="">Sélectionnez un mode de paiement</option>
                                    <option value="Orange Money">Orange Money</option>
                                    <option value="MTN Mobile Money">MTN Mobile Money</option>
                                    <option value="Wave">Wave</option>
                                    <option value="Carte bancaire">Carte bancaire</option>
                                    <option value="Espèces">Espèces</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="nouvelleDatePaiement">Date de paiement</label>
                                <input type="date" id="nouvelleDatePaiement" name="date_paiement" required>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="hideNouveauPaiementModal()">Annuler</button>
                                <button type="submit" class="submit-btn">Créer le paiement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function showPaiementModal(paiementId) {
                    const modal = document.getElementById('paiementModal');
                    const paiementIdInput = document.getElementById('paiementId');
                    paiementIdInput.value = paiementId;
                    modal.style.display = 'block';
                }

                function hidePaiementModal() {
                    document.getElementById('paiementModal').style.display = 'none';
                }

                function showNouveauPaiementModal(inscriptionId) {
                    const modal = document.getElementById('nouveauPaiementModal');
                    const inscriptionIdInput = document.getElementById('inscriptionId');
                    inscriptionIdInput.value = inscriptionId;
                    modal.style.display = 'block';
                }

                function hideNouveauPaiementModal() {
                    document.getElementById('nouveauPaiementModal').style.display = 'none';
                }

                // Gérer la soumission du formulaire de modification
                document.getElementById('paiementForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch('process_paiement.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Paiement modifié avec succès !');
                            hidePaiementModal();
                            location.reload();
                        } else {
                            alert('Erreur lors de la modification du paiement: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de la modification du paiement');
                    });
                });

                // Gérer la soumission du formulaire de création
                document.getElementById('nouveauPaiementForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch('create_paiement.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Paiement créé avec succès !');
                            hideNouveauPaiementModal();
                            location.reload();
                        } else {
                            alert('Erreur lors de la création du paiement: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de la création du paiement');
                    });
                });

                // Initialiser les dates du jour comme date de paiement par défaut
                document.getElementById('datePaiement').valueAsDate = new Date();
                document.getElementById('nouvelleDatePaiement').valueAsDate = new Date();

                function validerPaiement(paiementId) {
                    if (confirm('Êtes-vous sûr de vouloir valider ce paiement ?')) {
                        fetch('valider_paiement.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'paiement_id=' + paiementId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Paiement validé avec succès !');
                                location.reload();
                            } else {
                                alert('Erreur lors de la validation du paiement: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Une erreur est survenue lors de la validation du paiement');
                        });
                    }
                }

                function annulerPaiement(paiementId) {
                    if (confirm('Êtes-vous sûr de vouloir annuler ce paiement ? Cette action est irréversible.')) {
                        fetch('annuler_paiement.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'paiement_id=' + paiementId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Paiement annulé avec succès !');
                                location.reload();
                            } else {
                                alert('Erreur lors de l\'annulation du paiement: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Une erreur est survenue lors de l\'annulation du paiement');
                        });
                    }
                }
            </script>


           
            
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


