<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// L'utilisateur est connecté, on peut afficher la page
$user_email = $_SESSION['user_email'];

// Récupération des statistiques
try {
    // Connexion à la base de données
    require_once '../../config/database.php';
    
    // 1. Nombre de concours actifs (sessions en cours)
    $stmt = $conn->query("SELECT COUNT(*) FROM SESSION_CONCOURS 
                         WHERE date_ouverture <= CURDATE() 
                         AND date_cloture >= CURDATE()");
    $concours_actifs = $stmt->fetchColumn();
    
    // 2. Nombre total de candidats inscrits
    $stmt = $conn->query("SELECT COUNT(*) FROM CANDIDAT");
    $total_candidats = $stmt->fetchColumn();
    
    // 3. Nombre de dossiers en attente
    $stmt = $conn->query("SELECT COUNT(*) FROM INSCRIPTION 
                         WHERE statut = 'en_attente'");
    $dossiers_attente = $stmt->fetchColumn();
    
    // 4. Taux de paiement des inscriptions
    $stmt = $conn->query("SELECT 
                         (SELECT COUNT(*) FROM PAIEMENT WHERE statut = 'valide') * 100.0 / 
                         (SELECT COUNT(*) FROM INSCRIPTION) as taux_paiement");
    $taux_paiement = round($stmt->fetchColumn(), 1);
    
} catch(PDOException $e) {
    // En cas d'erreur, on utilise des valeurs par défaut
    $concours_actifs = 0;
    $total_candidats = 0;
    $dossiers_attente = 0;
    $taux_paiement = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS pour la partie originale -->
    <link rel="stylesheet" href="../../assets/css/dashboardadmin.css">
    <!-- CSS pour le nouveau dashboard -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="shortcut icon" href="./assets/images/logo.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <title>PUBLIGEST CI - Dashboard Admin</title>
    <style>
        /* Styles existants */
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .search-box {
            position: relative;
            flex: 1;
        }

        .search-box input {
            width: 100%;
            padding: 0.5rem 1rem;
            padding-left: 2.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .search-box i {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .btn-primary {
            background-color: #F47721;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e06a1a;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn-primary i {
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <!-- Header original -->
    <header>
        <div class="logo">
            <img src="../../assets/images/logo.png" alt="logo">
        </div>
        
   
        <div class="login" id="logoutButton">Déconnexion</div>
    </header>

    <!-- Contenu original -->
    <div class="container">
        <div class="contenttitlegeneral">
            <h1>ADMIN PUBLIGEST CI</h1>
        </div>
        <div class="content">
            <p>bienvenu sur le dashboard admin de PUBLIGEST CI. vous pouvez gérer les informations concours et les candidatures.</p>
        </div>
        <div class="contenuvus">
            <div class="menu1">
                <img src="" alt="">
                <h3>Inscription</h3>
            </div>
            <div class="menu2">
                <img src="" alt="">
                <h3>Validation</h3>
            </div>
            <div class="menu3">
                <img src="" alt="">
                <h3>Resultat</h3>
            </div>
        </div>
        <div class="imganimationcirlcle">
            <img src="../../assets/images/Material Icons Settings (1).png" alt="" class="caroussel1">
        </div>
    </div>

    <!-- Nouveau Dashboard -->
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <button class="close-sidebar" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="../../assets/images/logo.png" alt="logo">
                </div>
                <div class="admin-info">
                    <h3>Administrateur</h3>
                    <p>PUBLIGEST CI</p>
                </div>
            </div>

            <nav class="admin-menu">
                <div class="menu-section">
                    <h3>
                        <span>Tableau de bord</span>
                        <i class="fas fa-chevron-down"></i>
                    </h3>
                    <ul class="submenu active">
                        <li class="active" data-section="overview">
                            <a href="#">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Vue d'ensemble</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3>
                        <span>Gestion des Concours</span>
                        <i class="fas fa-chevron-down"></i>
                    </h3>
                    <ul class="submenu">
                        <li data-section="create-contest">
                            <a href="#">
                                <i class="fas fa-plus-circle"></i>
                                <span>Créer un concours</span>
                            </a>
                        </li>
                        <li data-section="manage-contests">
                            <a href="#">
                                <i class="fas fa-tasks"></i>
                                <span>Gérer les concours</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3>
                        <span>Gestion des Candidats</span>
                        <i class="fas fa-chevron-down"></i>
                    </h3>
                    <ul class="submenu">
                        <li data-section="candidate-files">
                            <a href="#">
                                <i class="fas fa-folder"></i>
                                <span>Convocations</span>
                            </a>
                        </li>
                        <li data-section="validation">
                            <a href="#">
                                <i class="fas fa-check-circle"></i>
                                <span>Validation</span>
                            </a>
                        </li>
                        <li data-section="results">
                            <a href="#">
                                <i class="fas fa-chart-bar"></i>
                                <span>Résultats</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3>
                        <span>Administration</span>
                        <i class="fas fa-chevron-down"></i>
                    </h3>
                    <ul class="submenu">
                        <li data-section="users">
                            <a href="#">
                                <i class="fas fa-users"></i>
                                <span>Utilisateurs</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-info">
                        <h4><?php echo htmlspecialchars($user_email); ?></h4>
                        <p>Administrateur</p>
                    </div>
                </div>
                <button class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Tableau de bord Administrateur</h1>
                </div>
                <div class="header-right">
                    
                </div>
            </header>

            <!-- Overview Section -->
            <section id="overview" class="content-section active">
                <!-- Stats Overview -->
                <div class="stats-overview">
                    <h2>Vue d'ensemble</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon orange">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Concours Actifs</h3>
                                <p class="stat-number"><?php echo $concours_actifs; ?></p>
                                <span class="stat-change positive">En cours</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon blue">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Candidats Inscrits</h3>
                                <p class="stat-number"><?php echo number_format($total_candidats); ?></p>
                                <span class="stat-change positive">Total</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Dossiers à Traiter</h3>
                                <p class="stat-number"><?php echo $dossiers_attente; ?></p>
                                <span class="stat-change negative">En attente</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon red">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Taux de Paiement</h3>
                                <p class="stat-number"><?php echo $taux_paiement; ?>%</p>
                                <span class="stat-change positive">Des inscriptions</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
               
            </section>

            <!-- Statistics Section -->
            <section id="statistics" class="content-section">
                <div class="charts-section">
                    <div class="charts-grid">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>Inscriptions par Mois</h3>
                                <select class="chart-period">
                                    <option>Cette année</option>
                                    <option>6 derniers mois</option>
                                    <option>3 derniers mois</option>
                                </select>
                            </div>
                            <div class="chart-container">
                                <canvas id="inscriptionsChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>Répartition des Candidats</h3>
                                <select class="chart-period">
                                    <option>Tous les concours</option>
                                    <option>Concours actifs</option>
                                </select>
                            </div>
                            <div class="chart-container">
                                <canvas id="repartitionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Create Contest Section -->
            <section id="create-contest" class="content-section">
                <div class="section-content">
                    <h2>Gestion des Concours</h2>
                    
                    <!-- Domaines Section -->
                    <div class="domaines-section">
                        <div class="section-header">
                            <h3>Domaines de Concours</h3>
                            <button class="add-btn" id="addDomaineBtn">
                                <i class="fas fa-plus"></i> Nouveau Domaine
                            </button>
                        </div>
                        
                        <div class="domaines-list" id="domainesList">
                            <!-- Les domaines seront chargés dynamiquement -->
                        </div>
                    </div>

                    <!-- Concours Section -->
                    <div class="concours-section">
                        <div class="section-header">
                            <h3>Concours</h3>
                            <button class="add-btn" id="addConcoursBtn">
                                <i class="fas fa-plus"></i> Nouveau Concours
                            </button>
                        </div>
                        
                        <div class="concours-list" id="concoursList">
                            <!-- Les concours seront chargés dynamiquement -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Modals -->
            <div class="modal" id="domaineModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-folder-plus"></i> Nouveau Domaine</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="domaineForm">
                            <div class="form-group">
                                <label for="domaineNom"><i class="fas fa-tag"></i> Nom du Domaine</label>
                                <input type="text" id="domaineNom" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="domaineDescription"><i class="fas fa-info-circle"></i> Description</label>
                                <textarea id="domaineDescription" name="description" rows="4" placeholder="Entrez une description du domaine..."></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-save"></i> Enregistrer
                                </button>
                                <button type="button" class="cancel-btn">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal" id="concoursModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Nouveau Concours</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="concoursForm">
                            <div class="form-group">
                                <label for="concoursNom">Nom du Concours</label>
                                <input type="text" id="concoursNom" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="concoursDescription">Description</label>
                                <textarea id="concoursDescription" name="description" rows="4"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="concoursNiveau">Niveau Requis</label>
                                <input type="text" id="concoursNiveau" name="niveau_requis">
                            </div>
                            <div class="form-group">
                                <label for="concoursCategorie">Catégorie</label>
                                <input type="text" id="concoursCategorie" name="categorie">
                            </div>
                            <div class="form-group">
                                <label for="concoursMinistere">Ministère</label>
                                <input type="text" id="concoursMinistere" name="ministere">
                            </div>
                            <div class="form-group">
                                <label for="concoursDomaine">Domaine</label>
                                <select id="concoursDomaine" name="domaine_id" required>
                                    <!-- Les domaines seront chargés dynamiquement -->
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="submit-btn">Enregistrer</button>
                                <button type="button" class="cancel-btn">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Manage Contests Section -->
            <section id="manage-contests" class="content-section">
                <div class="section-content">
                    <h2>Gérer les concours</h2>
                    
                    <!-- Sessions Section -->
                    <div class="sessions-section">
                        <div class="section-header">
                            <h3>Sessions de Concours</h3>
                            <button class="add-btn" id="addSessionBtn">
                                <i class="fas fa-plus"></i> Nouvelle Session
                            </button>
                        </div>
                        
                        <div class="sessions-list" id="sessionsList">
                            <!-- Les sessions seront chargées dynamiquement -->
                        </div>
                    </div>

                    <!-- Centres Section -->
                    <div class="centres-section">
                        <div class="section-header">
                            <h3>Centres d'Examen</h3>
                            <button class="add-btn" id="addCentreBtn">
                                <i class="fas fa-plus"></i> Nouveau Centre
                            </button>
                        </div>
                        
                        <div class="centres-list" id="centresList">
                            <!-- Les centres seront chargés dynamiquement -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Session Modal -->
            <div class="modal" id="sessionModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-calendar-plus"></i> Nouvelle Session</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="sessionForm">
                            <div class="form-group">
                                <label for="sessionConcours"><i class="fas fa-trophy"></i> Concours</label>
                                <select id="sessionConcours" name="concours_id" required>
                                    <option value="">Sélectionnez un concours</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sessionDateOuverture"><i class="fas fa-calendar-check"></i> Date d'ouverture</label>
                                <input type="date" id="sessionDateOuverture" name="date_ouverture" required>
                            </div>
                            <div class="form-group">
                                <label for="sessionDateCloture"><i class="fas fa-calendar-times"></i> Date de clôture</label>
                                <input type="date" id="sessionDateCloture" name="date_cloture" required>
                            </div>
                            <div class="form-group">
                                <label for="sessionPlaces"><i class="fas fa-users"></i> Nombre de places</label>
                                <input type="number" id="sessionPlaces" name="nb_places" min="1" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-save"></i> Enregistrer
                                </button>
                                <button type="button" class="cancel-btn">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Centre Modal -->
            <div class="modal" id="centreModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-building"></i> Nouveau Centre</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="centreForm">
                            <div class="form-group">
                                <label for="centreSession"><i class="fas fa-calendar-alt"></i> Session</label>
                                <select id="centreSession" name="session_id" required>
                                    <option value="">Sélectionnez une session</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="centreVille"><i class="fas fa-map-marker-alt"></i> Ville</label>
                                <input type="text" id="centreVille" name="ville" required>
                            </div>
                            <div class="form-group">
                                <label for="centreLieu"><i class="fas fa-map"></i> Lieu</label>
                                <input type="text" id="centreLieu" name="lieu" required>
                            </div>
                            <div class="form-group">
                                <label for="centreCapacite"><i class="fas fa-users"></i> Capacité</label>
                                <input type="number" id="centreCapacite" name="capacite" min="1" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-save"></i> Enregistrer
                                </button>
                                <button type="button" class="cancel-btn">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Applications Section -->

            <!-- Candidate Files Section -->
            <section id="candidate-files" class="content-section">
                <div class="section-content">
                    <h2>Convocations</h2>
                    
                    <!-- Interface de génération de convocation -->
                    <div class="convocation-form">
                        <div class="form-group">
                            <label for="candidateSelect">Sélectionner un candidat validé</label>
                            <select id="candidateSelect" class="form-control">
                                <option value="">Chargement des candidats...</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="examDate">Date de l'examen</label>
                            <input type="datetime-local" id="examDate" class="form-control" required>
                        </div>
                        
                        <!-- <div class="form-group">
                            <label for="examLocation">Lieu de l'examen</label>
                            <input type="text" id="examLocation" class="form-control" required>
                        </div> -->
                        
                        <div class="form-group">
                            <label for="examDuration">Durée de l'examen (en minutes)</label>
                            <input type="number" id="examDuration" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="examInstructions">Instructions spéciales</label>
                            <textarea id="examInstructions" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button id="generateConvocation" class="btn btn-primary">
                                <i class="fas fa-file-pdf"></i> Générer la convocation
                            </button>
                            <button id="saveConvocation" class="btn btn-success" disabled>
                                <i class="fas fa-save"></i> Enregistrer la convocation
                            </button>
                        </div>
                    </div>
                    
                    <!-- Aperçu du PDF -->
                    <div id="pdfPreview" class="pdf-preview" style="display: none;">
                        <h3>Aperçu de la convocation</h3>
                        <iframe id="pdfViewer" width="100%" height="600px"></iframe>
                    </div>
                </div>
            </section>

            <!-- Validation Section -->
            <section id="validation" class="content-section">
                <div class="section-content">
                    <h2>Validation des dossiers</h2>
                    
                    <!-- Filtres de recherche -->
                    <div class="validation-filters">
                        <div class="search-box">
                            <input type="text" id="candidateSearch" placeholder="Rechercher un candidat...">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="filter-group">
                            <select id="sessionFilter">
                                <option value="">Toutes les sessions</option>
                            </select>
                            <select id="statusFilter">
                                <option value="en_attente">En attente</option>
                                <option value="valide">Validés</option>
                                <option value="rejete">Rejetés</option>
                            </select>
                        </div>
                    </div>

                    <!-- Liste des candidats -->
                    <div class="candidates-list" id="candidatesList">
                        <!-- Les candidats seront chargés dynamiquement ici -->
                    </div>

                    <!-- Modal pour voir les détails -->
                    <div class="modal" id="candidateModal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3><i class="fas fa-user-graduate"></i> Détails du candidat</h3>
                                <button class="close-modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="candidate-details">
                                    <div class="personal-info">
                                        <h4>Informations personnelles</h4>
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <label>Nom complet:</label>
                                                <span id="candidateName"></span>
                                            </div>
                                            <div class="info-item">
                                                <label>Date de naissance:</label>
                                                <span id="candidateBirthDate"></span>
                                            </div>
                                            <div class="info-item">
                                                <label>Nationalité:</label>
                                                <span id="candidateNationality"></span>
                                            </div>
                                            <div class="info-item">
                                                <label>Numéro de pièce:</label>
                                                <span id="candidateIdNumber"></span>
                                            </div>
                                            <div class="info-item">
                                                <label>Téléphone:</label>
                                                <span id="candidatePhone"></span>
                                            </div>
                                            <div class="info-item">
                                                <label>Adresse:</label>
                                                <span id="candidateAddress"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="documents-section">
                                        <h4>Documents fournis</h4>
                                        <div class="documents-list" id="documentsList">
                                            <!-- Les documents seront chargés dynamiquement ici -->
                                        </div>
                                    </div>

                                    <div class="diplomas-section">
                                        <h4>Diplômes</h4>
                                        <div class="diplomas-list" id="diplomasList">
                                            <!-- Les diplômes seront chargés dynamiquement ici -->
                                        </div>
                                    </div>

                                    <div class="payment-section">
                                        <h4>État du paiement</h4>
                                        <div class="payment-info" id="paymentInfo">
                                            <!-- Les informations de paiement seront chargées dynamiquement ici -->
                                        </div>
                                    </div>

                                    <div class="validation-actions">
                                        <button class="action-btn validate-btn" id="validateBtn">
                                            <i class="fas fa-check"></i> Valider
                                        </button>
                                        <button class="action-btn reject-btn" id="rejectBtn">
                                            <i class="fas fa-times"></i> Rejeter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Results Section -->
            <section id="results" class="content-section">
                <div class="section-content">
                    <h2>Résultats des concours</h2>
                    
                    <!-- Statistiques des résultats -->
                    <div class="results-stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Admis</h3>
                                <p class="stat-number" id="admis-count">0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Rejetés</h3>
                                <p class="stat-number" id="rejetes-count">0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h3>En attente</h3>
                                <p class="stat-number" id="en-attente-count">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contrôles des résultats -->
                    <div class="results-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-results" placeholder="Rechercher un candidat, un concours ou une session...">
                        </div>
                        <button class="action-btn add-btn" onclick="openAddResultModal()">
                            <i class="fas fa-plus"></i> Ajouter un résultat
                        </button>
                    </div>

                    <!-- Tableau des résultats -->
                    <div class="table-responsive">
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>ID Inscription</th>
                                    <th>Nom du candidat</th>
                                    <th>Concours</th>
                                    <th>Session</th>
                                    <th>Centre</th>
                                    <th>Note</th>
                                    <th>Décision</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="results-table-body">
                                <!-- Les résultats seront chargés dynamiquement ici -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Modal pour ajouter/modifier un résultat -->
            <div id="result-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h3 id="modal-title">Ajouter un résultat</h3>
                    <form id="result-form">
                        <input type="hidden" id="result-id" name="id">
                        <div class="form-group">
                            <label for="inscription-id">Candidat et Concours</label>
                            <select id="inscription-id" name="inscription_id" required class="select2">
                                <option value="">Sélectionnez un candidat...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="note">Note (sur 1000)</label>
                            <input type="number" id="note" name="note" step="0.01" min="0" max="1000" required>
                        </div>
                        <div class="form-group">
                            <label for="decision">Décision</label>
                            <select id="decision" name="decision" required>
                                <option value="">Sélectionnez une décision...</option>
                                <option value="admis">Admis</option>
                                <option value="rejete">Rejeté</option>
                                <option value="en_attente">En attente</option>
                            </select>
                        </div>
                        <button type="submit" class="action-btn save-btn">Enregistrer</button>
                    </form>
                </div>
            </div>

            <!-- Users Section -->
            <section id="users" class="content-section">
                <div class="section-content">
                    <h2>Gestion des utilisateurs</h2>
                    
                    <!-- User Statistics -->
                    <div class="user-stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Utilisateurs</h3>
                                <p class="stat-number" id="totalUsers">0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Candidats</h3>
                                <p class="stat-number" id="totalCandidates">0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Administrateurs</h3>
                                <p class="stat-number" id="totalAdmins">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="users-table-container">
                        <div class="table-header">
                            <h3>Liste des Utilisateurs</h3>
                            <div class="header-actions">
                                <div class="search-box">
                                    <input type="text" id="userSearch" placeholder="Rechercher un utilisateur...">
                                    <i class="fas fa-search"></i>
                                </div>
                                <a href="admin_register.php" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Nouvel Admin
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Date d'inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Les données seront chargées dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Settings Section -->

            <!-- Reports Section -->
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

    <script src="../../assets/js/dashboardadmin.js"></script>
    <script>
        // Menu Toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
        });

        // Section Navigation
        document.querySelectorAll('.admin-menu li').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all menu items
                document.querySelectorAll('.admin-menu li').forEach(li => {
                    li.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Hide all sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                // Show selected section
                const sectionId = this.getAttribute('data-section');
                document.getElementById(sectionId).classList.add('active');
            });
        });

        // Charts
        const inscriptionsCtx = document.getElementById('inscriptionsChart').getContext('2d');
        const repartitionCtx = document.getElementById('repartitionChart').getContext('2d');

        // Inscriptions Chart
        new Chart(inscriptionsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Inscriptions',
                    data: [120, 190, 150, 220, 180, 250],
                    borderColor: '#3498db',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(52, 152, 219, 0.1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Répartition Chart
        new Chart(repartitionCtx, {
            type: 'doughnut',
            data: {
                labels: ['ENA', 'INP-HB', 'ENS', 'Autres'],
                datasets: [{
                    data: [40, 25, 20, 15],
                    backgroundColor: [
                        '#ff9f43',
                        '#3498db',
                        '#28c76f',
                        '#ea5455'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        document.getElementById('logoutButton').addEventListener('click', function() {
            // Envoyer une requête de déconnexion
            fetch('process_logout.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '../../index.php';
                } else {
                    alert('Erreur lors de la déconnexion');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        });

        // Fonction pour charger les statistiques des utilisateurs
        function loadUserStats() {
            fetch('get_user_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalUsers').textContent = data.total_users;
                    document.getElementById('totalCandidates').textContent = data.total_candidates;
                    document.getElementById('totalAdmins').textContent = data.total_admins;
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour charger la liste des utilisateurs
        function loadUsers() {
            fetch('get_users.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('usersTableBody');
                    tbody.innerHTML = '';
                    
                    data.forEach(user => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${user.id}</td>
                            <td>${user.email}</td>
                            <td>${user.role}</td>
                            <td>${user.created_at}</td>
                            <td>
                                <button class="action-btn delete-btn" onclick="deleteUser(${user.id})">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour supprimer un utilisateur
        function deleteUser(userId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                fetch('delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: userId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUsers();
                        loadUserStats();
                    } else {
                        alert('Erreur lors de la suppression de l\'utilisateur');
                    }
                })
                .catch(error => console.error('Erreur:', error));
            }
        }

        // Fonction de recherche
        document.getElementById('userSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTableBody tr');
            
            rows.forEach(row => {
                const email = row.cells[1].textContent.toLowerCase();
                if (email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadUserStats();
            loadUsers();
            loadDomaines();
            loadConcours();
            loadSessions();
            loadCentres(); // Ajout du chargement des centres
        });

        // Fonction pour charger les domaines
        function loadDomaines() {
            fetch('get_domaines.php')
                .then(response => response.json())
                .then(data => {
                    const domainesList = document.getElementById('domainesList');
                    domainesList.innerHTML = '';
                    
                    data.forEach(domaine => {
                        const card = document.createElement('div');
                        card.className = 'domaine-card';
                        card.innerHTML = `
                            <h4><i class="fas fa-folder"></i>${domaine.nom}</h4>
                            <p><i class="fas fa-info-circle"></i>${domaine.description || 'Aucune description'}</p>
                            <div class="card-actions">
                                <button class="action-btn edit-btn" onclick="editDomaine(${domaine.id})">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteDomaine(${domaine.id})">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        `;
                        domainesList.appendChild(card);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour charger les concours
        function loadConcours() {
            fetch('list_concours.php')
                .then(response => response.json())
                .then(data => {
                    const concoursList = document.getElementById('concoursList');
                    concoursList.innerHTML = '';
                    
                    data.forEach(concours => {
                        const card = document.createElement('div');
                        card.className = 'concours-card';
                        card.innerHTML = `
                            <h4><i class="fas fa-trophy"></i>${concours.nom}</h4>
                            <p><i class="fas fa-info-circle"></i>${concours.description || 'Aucune description'}</p>
                            <p><i class="fas fa-graduation-cap"></i><strong>Niveau requis:</strong> ${concours.niveau_requis || 'Non spécifié'}</p>
                            <p><i class="fas fa-tag"></i><strong>Catégorie:</strong> ${concours.categorie || 'Non spécifiée'}</p>
                            <p><i class="fas fa-building"></i><strong>Ministère:</strong> ${concours.ministere || 'Non spécifié'}</p>
                            <p><i class="fas fa-folder"></i><strong>Domaine:</strong> ${concours.domaine_nom || 'Non spécifié'}</p>
                            <div class="card-actions">
                                <button class="action-btn edit-btn" onclick="editConcours(${concours.id})">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteConcours(${concours.id})">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        `;
                        concoursList.appendChild(card);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour charger les domaines dans le select
        function loadDomainesSelect() {
            fetch('get_domaines.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des domaines');
                    }
                    return response.json();
                })
                .then(data => {
                    const select = document.getElementById('concoursDomaine');
                    select.innerHTML = '<option value="">Sélectionnez un domaine</option>';
                    
                    data.forEach(domaine => {
                        const option = document.createElement('option');
                        option.value = domaine.id;
                        option.textContent = domaine.nom;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des domaines: ' + error.message);
                });
        }

        // Fonction pour charger les concours dans le select
        function loadConcoursSelect() {
            fetch('list_concours.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des concours');
                    }
                    return response.json();
                })
                .then(data => {
                    const select = document.getElementById('sessionConcours');
                    select.innerHTML = '<option value="">Sélectionnez un concours</option>';
                    
                    data.forEach(concours => {
                        const option = document.createElement('option');
                        option.value = concours.id;
                        option.textContent = concours.nom;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des concours: ' + error.message);
                });
        }

        // Gestion des modals
        document.getElementById('addDomaineBtn').addEventListener('click', () => {
            document.getElementById('domaineModal').style.display = 'block';
        });

        document.getElementById('addConcoursBtn').addEventListener('click', () => {
            loadDomainesSelect();
            document.getElementById('concoursModal').style.display = 'block';
        });

        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.style.display = 'none';
                });
            });
        });

        // Fonction pour éditer un domaine
        function editDomaine(id) {
            // Récupérer les informations du domaine
            fetch('get_domaine.php?id=' + id)
                .then(response => response.json())
                .then(domaine => {
                    // Remplir le formulaire
                    document.getElementById('domaineNom').value = domaine.nom;
                    document.getElementById('domaineDescription').value = domaine.description || '';
                    
                    // Changer le titre du modal
                    document.querySelector('#domaineModal h3').innerHTML = '<i class="fas fa-edit"></i> Modifier le Domaine';
                    
                    // Ajouter un champ caché pour l'ID
                    let idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'id';
                    idInput.value = id;
                    document.getElementById('domaineForm').appendChild(idInput);
                    
                    // Afficher le modal
                    document.getElementById('domaineModal').style.display = 'block';
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour éditer un concours
        function editConcours(id) {
            // Récupérer les informations du concours
            fetch('get_concours.php?id=' + id)
                .then(response => response.json())
                .then(concours => {
                    // Remplir le formulaire
                    document.getElementById('concoursNom').value = concours.nom;
                    document.getElementById('concoursDescription').value = concours.description || '';
                    document.getElementById('concoursNiveau').value = concours.niveau_requis || '';
                    document.getElementById('concoursCategorie').value = concours.categorie || '';
                    document.getElementById('concoursMinistere').value = concours.ministere || '';
                    document.getElementById('concoursDomaine').value = concours.domaine_id;
                    
                    // Charger la liste des domaines si nécessaire
                    loadDomainesSelect();
                    
                    // Changer le titre du modal
                    document.querySelector('#concoursModal h3').innerHTML = '<i class="fas fa-edit"></i> Modifier le Concours';
                    
                    // Ajouter un champ caché pour l'ID
                    let idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'id';
                    idInput.value = id;
                    document.getElementById('concoursForm').appendChild(idInput);
                    
                    // Afficher le modal
                    document.getElementById('concoursModal').style.display = 'block';
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Mettre à jour la gestion des formulaires
        document.getElementById('domaineForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const id = formData.get('id');
            
            fetch(id ? 'update_domaine.php' : 'create_domaine.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadDomaines();
                    document.getElementById('domaineModal').style.display = 'none';
                    this.reset();
                    // Supprimer le champ ID caché s'il existe
                    const idInput = this.querySelector('input[name="id"]');
                    if (idInput) idInput.remove();
                } else {
                    alert('Erreur lors de ' + (id ? 'la modification' : 'la création') + ' du domaine');
                }
            })
            .catch(error => console.error('Erreur:', error));
        });

        document.getElementById('concoursForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const id = formData.get('id');
            
            fetch(id ? 'update_concours.php' : 'create_concours.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadConcours();
                    document.getElementById('concoursModal').style.display = 'none';
                    this.reset();
                    // Supprimer le champ ID caché s'il existe
                    const idInput = this.querySelector('input[name="id"]');
                    if (idInput) idInput.remove();
                } else {
                    alert('Erreur lors de ' + (id ? 'la modification' : 'la création') + ' du concours');
                }
            })
            .catch(error => console.error('Erreur:', error));
        });

        // Fonctions de suppression
        function deleteDomaine(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce domaine ?')) {
                fetch('delete_domaine.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadDomaines();
                        loadConcours();
                    } else {
                        alert('Erreur lors de la suppression du domaine');
                    }
                })
                .catch(error => console.error('Erreur:', error));
            }
        }

        function deleteConcours(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce concours ?')) {
                fetch('delete_concours.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadConcours();
                    } else {
                        alert('Erreur lors de la suppression du concours');
                    }
                })
                .catch(error => console.error('Erreur:', error));
            }
        }

        // Fonction pour charger les sessions
        function loadSessions() {
            fetch('get_sessions.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des sessions');
                    }
                    return response.json();
                })
                .then(data => {
                    const sessionsList = document.getElementById('sessionsList');
                    sessionsList.innerHTML = '';
                    
                    data.forEach(session => {
                        const status = getSessionStatus(session.date_ouverture, session.date_cloture);
                        const card = document.createElement('div');
                        card.className = 'session-card';
                        card.innerHTML = `
                            <span class="session-status ${status.class}">${status.text}</span>
                            <h4><i class="fas fa-calendar-alt"></i>${session.concours_nom}</h4>
                            <p><i class="fas fa-calendar-check"></i><strong>Ouverture:</strong> ${formatDate(session.date_ouverture)}</p>
                            <p><i class="fas fa-calendar-times"></i><strong>Clôture:</strong> ${formatDate(session.date_cloture)}</p>
                            <p><i class="fas fa-users"></i><strong>Places:</strong> ${session.nb_places}</p>
                            <div class="card-actions">
                                <button class="action-btn edit-btn" onclick="editSession(${session.id})">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteSession(${session.id})">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        `;
                        sessionsList.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des sessions: ' + error.message);
                });
        }

        // Fonction pour déterminer le statut d'une session
        function getSessionStatus(dateOuverture, dateCloture) {
            const today = new Date();
            const ouverture = new Date(dateOuverture);
            const cloture = new Date(dateCloture);
            
            if (today < ouverture) {
                return { class: 'status-upcoming', text: 'À venir' };
            } else if (today > cloture) {
                return { class: 'status-closed', text: 'Clôturé' };
            } else {
                return { class: 'status-active', text: 'Actif' };
            }
        }

        // Fonction pour formater la date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
        }

        // Fonction pour charger les sessions dans le select
        function loadSessionsSelect() {
            fetch('get_sessions.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des sessions');
                    }
                    return response.json();
                })
                .then(data => {
                    const select = document.getElementById('centreSession');
                    select.innerHTML = '<option value="">Sélectionnez une session</option>';
                    
                    data.forEach(session => {
                        const option = document.createElement('option');
                        option.value = session.id;
                        option.textContent = `${session.concours_nom} (${formatDate(session.date_ouverture)} - ${formatDate(session.date_cloture)})`;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des sessions: ' + error.message);
                });
        }

        // Gestion du modal
        document.getElementById('addSessionBtn').addEventListener('click', () => {
            loadConcoursSelect();
            document.getElementById('sessionModal').style.display = 'block';
            document.querySelector('#sessionModal h3').innerHTML = '<i class="fas fa-calendar-plus"></i> Nouvelle Session';
            document.getElementById('sessionForm').reset();
        });

        // Fonction pour éditer une session
        function editSession(id) {
            fetch('get_session.php?id=' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la récupération des données');
                    }
                    return response.json();
                })
                .then(session => {
                    const form = document.getElementById('sessionForm');
                    form.querySelector('#sessionConcours').value = session.concours_id;
                    form.querySelector('#sessionDateOuverture').value = session.date_ouverture;
                    form.querySelector('#sessionDateCloture').value = session.date_cloture;
                    form.querySelector('#sessionPlaces').value = session.nb_places;
                    
                    // Ajouter ou mettre à jour le champ ID caché
                    let idInput = form.querySelector('input[name="id"]');
                    if (!idInput) {
                        idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'id';
                        form.appendChild(idInput);
                    }
                    idInput.value = id;
                    
                    document.querySelector('#sessionModal h3').innerHTML = '<i class="fas fa-edit"></i> Modifier la Session';
                    document.getElementById('sessionModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert(error.message);
                });
        }

        // Gestion du formulaire
        document.getElementById('sessionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const sessionId = formData.get('id');
            
            // Vérifier que les dates sont valides
            const dateOuverture = new Date(formData.get('date_ouverture'));
            const dateCloture = new Date(formData.get('date_cloture'));
            
            if (dateCloture <= dateOuverture) {
                alert('La date de clôture doit être postérieure à la date d\'ouverture');
                return;
            }
            
            // Déterminer l'URL en fonction de l'existence d'un ID
            const url = sessionId ? 'update_session.php' : 'create_session.php';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Erreur lors de l\'enregistrement');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    loadSessions();
                    document.getElementById('sessionModal').style.display = 'none';
                    this.reset();
                    const idInput = this.querySelector('input[name="id"]');
                    if (idInput) idInput.remove();
                } else {
                    throw new Error(data.error || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert(error.message);
            });
        });

        // Fonction pour supprimer une session
        function deleteSession(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette session ?')) {
                const formData = new FormData();
                formData.append('id', id);
                
                fetch('delete_session.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || 'Erreur lors de la suppression');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        loadSessions();
                    } else {
                        throw new Error(data.error || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert(error.message);
                });
            }
        }

        // Fonction pour charger les centres
        function loadCentres() {
            fetch('get_centres.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des centres');
                    }
                    return response.json();
                })
                .then(data => {
                    const centresList = document.getElementById('centresList');
                    centresList.innerHTML = '';
                    
                    if (data.length === 0) {
                        centresList.innerHTML = '<div class="no-data">Aucun centre d\'examen trouvé</div>';
                        return;
                    }
                    
                    data.forEach(centre => {
                        const card = document.createElement('div');
                        card.className = 'centre-card';
                        card.innerHTML = `
                            <h4><i class="fas fa-building"></i>${centre.ville}</h4>
                            <p><i class="fas fa-map"></i><strong>Lieu:</strong> ${centre.lieu}</p>
                            <p><i class="fas fa-users"></i><strong>Capacité:</strong> ${centre.capacite}</p>
                            <p><i class="fas fa-calendar-alt"></i><strong>Session:</strong> ${centre.concours_nom}</p>
                            <div class="card-actions">
                                <button class="action-btn edit-btn" onclick="editCentre(${centre.id})">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteCentre(${centre.id})">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        `;
                        centresList.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    const centresList = document.getElementById('centresList');
                    centresList.innerHTML = '<div class="error-message">Erreur lors du chargement des centres: ' + error.message + '</div>';
                });
        }

        // Gestion du modal
        document.getElementById('addCentreBtn').addEventListener('click', () => {
            loadSessionsSelect();
            document.getElementById('centreModal').style.display = 'block';
            document.querySelector('#centreModal h3').innerHTML = '<i class="fas fa-building"></i> Nouveau Centre';
            document.getElementById('centreForm').reset();
        });

        // Fonction pour éditer un centre
        function editCentre(id) {
            fetch('get_centre.php?id=' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la récupération des données');
                    }
                    return response.json();
                })
                .then(centre => {
                    const form = document.getElementById('centreForm');
                    form.querySelector('#centreSession').value = centre.session_id;
                    form.querySelector('#centreVille').value = centre.ville;
                    form.querySelector('#centreLieu').value = centre.lieu;
                    form.querySelector('#centreCapacite').value = centre.capacite;
                    
                    // Ajouter ou mettre à jour le champ ID caché
                    let idInput = form.querySelector('input[name="id"]');
                    if (!idInput) {
                        idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'id';
                        form.appendChild(idInput);
                    }
                    idInput.value = id;
                    
                    document.querySelector('#centreModal h3').innerHTML = '<i class="fas fa-edit"></i> Modifier le Centre';
                    document.getElementById('centreModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert(error.message);
                });
        }

        // Gestion du formulaire
        document.getElementById('centreForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const centreId = formData.get('id');
            
            fetch(centreId ? 'update_centre.php' : 'create_centre.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Erreur lors de l\'enregistrement');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    loadCentres();
                    document.getElementById('centreModal').style.display = 'none';
                    this.reset();
                    const idInput = this.querySelector('input[name="id"]');
                    if (idInput) idInput.remove();
                } else {
                    throw new Error(data.error || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert(error.message);
            });
        });

        // Fonction pour supprimer un centre
        function deleteCentre(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce centre ?')) {
                const formData = new FormData();
                formData.append('id', id);
                
                fetch('delete_centre.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || 'Erreur lors de la suppression');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        loadCentres();
                    } else {
                        throw new Error(data.error || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert(error.message);
                });
            }
        }

        // Fonction pour charger les candidats
        function loadCandidates() {
            fetch('get_candidates.php')
                .then(response => response.json())
                .then(data => {
                    const candidatesList = document.getElementById('candidatesList');
                    candidatesList.innerHTML = '';
                    
                    data.forEach(candidate => {
                        const card = document.createElement('div');
                        card.className = 'candidate-card';
                        card.innerHTML = `
                            <h4><i class="fas fa-user"></i>${candidate.nom} ${candidate.prenoms}</h4>
                            <p><i class="fas fa-envelope"></i>${candidate.email}</p>
                            <p><i class="fas fa-phone"></i>${candidate.telephone_principal}</p>
                            <p><i class="fas fa-id-card"></i>${candidate.num_piece}</p>
                            
                            <div class="candidate-info">
                                <h5><i class="fas fa-calendar-alt"></i> Session</h5>
                                <p>Du ${formatDate(candidate.session.date_ouverture)} au ${formatDate(candidate.session.date_cloture)}</p>
                                
                                <h5><i class="fas fa-trophy"></i> Concours</h5>
                                <p>${candidate.concours.nom}</p>
                                <p><strong>Niveau requis:</strong> ${candidate.concours.niveau_requis}</p>
                                <p><strong>Catégorie:</strong> ${candidate.concours.categorie}</p>
                                <p><strong>Ministère:</strong> ${candidate.concours.ministere}</p>
                                
                                <h5><i class="fas fa-folder"></i> Domaine</h5>
                                <p>${candidate.concours.domaine.nom}</p>
                            </div>
                            
                            <span class="candidate-status status-${candidate.statut}">
                                ${candidate.statut === 'en_attente' ? 'En attente' : 
                                  candidate.statut === 'valide' ? 'Validé' : 'Rejeté'}
                            </span>
                            <div class="candidate-actions">
                                <button class="view-btn" onclick="viewCandidateDetails(${candidate.id})">
                                    <i class="fas fa-eye"></i> Voir détails
                                </button>
                            </div>
                        `;
                        candidatesList.appendChild(card);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour valider un candidat
        function validateCandidate(candidateId) {
            if (confirm('Êtes-vous sûr de vouloir valider ce candidat ?')) {
                fetch('validate_candidate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ candidate_id: candidateId })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification('Candidat validé avec succès', 'success');
                        loadCandidates();
                        document.getElementById('candidateModal').style.display = 'none';
                    } else {
                        throw new Error(data.error || 'Erreur lors de la validation');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification(error.message, 'error');
                });
            }
        }

        // Fonction pour rejeter un candidat
        function rejectCandidate(candidateId) {
            if (confirm('Êtes-vous sûr de vouloir rejeter ce candidat ?')) {
                fetch('reject_candidate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ candidate_id: candidateId })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification('Candidat rejeté avec succès', 'success');
                        loadCandidates();
                        document.getElementById('candidateModal').style.display = 'none';
                    } else {
                        throw new Error(data.error || 'Erreur lors du rejet');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification(error.message, 'error');
                });
            }
        }

        // Fonction pour voir les détails d'un candidat
        function viewCandidateDetails(candidateId) {
            fetch(`get_candidate_details.php?id=${candidateId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la récupération des données');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    // Remplir les informations personnelles
                    document.getElementById('candidateName').textContent = `${data.nom} ${data.prenoms}`;
                    document.getElementById('candidateBirthDate').textContent = data.date_naissance || 'Non spécifié';
                    document.getElementById('candidateNationality').textContent = data.nationalite || 'Non spécifié';
                    document.getElementById('candidateIdNumber').textContent = data.num_piece || 'Non spécifié';
                    document.getElementById('candidatePhone').textContent = data.telephone_principal || 'Non spécifié';
                    document.getElementById('candidateAddress').textContent = data.adresse_postale || 'Non spécifié';

                    // Remplir les documents
                    const documentsList = document.getElementById('documentsList');
                    documentsList.innerHTML = '';
                    if (data.documents && data.documents.length > 0) {
                        data.documents.forEach(doc => {
                            const docItem = document.createElement('div');
                            docItem.className = 'document-item';
                            // Extraire uniquement le nom du fichier du chemin complet
                            const fileName = doc.fichier_url.split('/').pop();
                            docItem.innerHTML = `
                                <h5>${doc.type_document || 'Document'}</h5>
                                <p><strong>Type:</strong> ${doc.type_document || 'Non spécifié'}</p>
                                <a href="/uploads/documents/${fileName}" target="_blank" class="view-doc">
                                    <i class="fas fa-file-pdf"></i> Voir le document
                                </a>
                            `;
                            documentsList.appendChild(docItem);
                        });
                    } else {
                        documentsList.innerHTML = '<p>Aucun document fourni</p>';
                    }

                    // Remplir les diplômes
                    const diplomasList = document.getElementById('diplomasList');
                    diplomasList.innerHTML = '';
                    if (data.diplomes && data.diplomes.length > 0) {
                        data.diplomes.forEach(diploma => {
                            const diplomaItem = document.createElement('div');
                            diplomaItem.className = 'diploma-item';
                            // Extraire uniquement le nom du fichier du chemin complet
                            const fileName = diploma.scan_url.split('/').pop();
                            diplomaItem.innerHTML = `
                                <h5>${diploma.nom || 'Diplôme'}</h5>
                                <p><strong>Niveau:</strong> ${diploma.niveau || 'Non spécifié'}</p>
                                <p><strong>Année:</strong> ${diploma.annee_obtention || 'Non spécifiée'}</p>
                                <p><strong>Établissement:</strong> ${diploma.etablissement || 'Non spécifié'}</p>
                                <a href="/uploads/documents/${fileName}" target="_blank" class="view-doc">
                                    <i class="fas fa-file-pdf"></i> Voir le diplôme
                                </a>
                            `;
                            diplomasList.appendChild(diplomaItem);
                        });
                    } else {
                        diplomasList.innerHTML = '<p>Aucun diplôme fourni</p>';
                    }

                    // Remplir les informations de paiement
                    const paymentInfo = document.getElementById('paymentInfo');
                    if (data.paiement) {
                        paymentInfo.innerHTML = `
                            <p><strong>Montant:</strong> ${data.paiement.montant || 0} FCFA</p>
                            <p><strong>Mode de paiement:</strong> ${data.paiement.mode_paiement || 'Non spécifié'}</p>
                            <p><strong>Date:</strong> ${data.paiement.date_paiement || 'Non spécifiée'}</p>
                            <span class="payment-status status-${data.paiement.statut || 'en_attente'}">
                                ${data.paiement.statut === 'valide' ? 'Validé' : 
                                  data.paiement.statut === 'en_attente' ? 'En attente' : 'Échoué'}
                            </span>
                        `;
                    } else {
                        paymentInfo.innerHTML = '<p>Aucune information de paiement disponible</p>';
                    }

                    // Mettre à jour les boutons d'action
                    const validationActions = document.querySelector('.validation-actions');
                    validationActions.innerHTML = '';
                    
                    if (data.statut === 'en_attente') {
                        validationActions.innerHTML = `
                            <button class="action-btn validate-btn" onclick="validateCandidate(${candidateId})">
                                <i class="fas fa-check"></i> Valider
                            </button>
                            <button class="action-btn reject-btn" onclick="rejectCandidate(${candidateId})">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                        `;
                    } else {
                        validationActions.innerHTML = `
                            <button class="action-btn validate-btn" onclick="validateCandidate(${candidateId})">
                                <i class="fas fa-check"></i> Valider
                            </button>
                            <button class="action-btn reject-btn" onclick="rejectCandidate(${candidateId})">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                        `;
                    }

                    // Afficher le modal
                    document.getElementById('candidateModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification(error.message || 'Erreur lors du chargement des détails du candidat', 'error');
                });
        }

        // Fonction pour afficher les notifications
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            // Supprimer la notification après 3 secondes
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Ajouter les styles pour les notifications
        const style = document.createElement('style');
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 5px;
                color: white;
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 1000;
                animation: slideIn 0.3s ease-out;
            }
            
            .notification.success {
                background-color: #28a745;
            }
            
            .notification.error {
                background-color: #dc3545;
            }
            
            .notification.info {
                background-color: #17a2b8;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);

        // Gestion des filtres
        document.getElementById('candidateSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.candidate-card');
            
            cards.forEach(card => {
                const name = card.querySelector('h4').textContent.toLowerCase();
                const email = card.querySelector('p').textContent.toLowerCase();
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        document.getElementById('statusFilter').addEventListener('change', function(e) {
            const status = e.target.value;
            const cards = document.querySelectorAll('.candidate-card');
            
            cards.forEach(card => {
                const cardStatus = card.querySelector('.candidate-status').className.includes(status);
                if (status === '' || cardStatus) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadCandidates();
        });

        // Fonction pour charger les candidats validés
        function loadValidatedCandidates() {
            fetch('get_validated_candidates.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('candidateSelect');
                    select.innerHTML = '<option value="">Sélectionnez un candidat</option>';
                    
                    data.forEach(candidate => {
                        const option = document.createElement('option');
                        option.value = candidate.id;
                        option.textContent = `${candidate.nom} ${candidate.prenoms} - ${candidate.email}`;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Erreur lors du chargement des candidats', 'error');
                });
        }

        // Gestion de la génération de convocation
        document.getElementById('generateConvocation').addEventListener('click', function() {
            const candidateId = document.getElementById('candidateSelect').value;
            const examDate = document.getElementById('examDate').value;
            const examDuration = document.getElementById('examDuration').value;
            const examInstructions = document.getElementById('examInstructions').value;

            if (!candidateId || !examDate || !examDuration) {
                showNotification('Veuillez remplir tous les champs obligatoires', 'error');
                return;
            }

            // Préparer les données pour la génération du PDF
            const data = {
                candidate_id: candidateId,
                exam_date: examDate,
                exam_duration: examDuration,
                exam_instructions: examInstructions
            };

            // Générer le PDF
            fetch('generate_convocation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.blob())
            .then(blob => {
                const url = URL.createObjectURL(blob);
                document.getElementById('pdfViewer').src = url;
                document.getElementById('pdfPreview').style.display = 'block';
                document.getElementById('saveConvocation').disabled = false;
                
                // Stocker les données pour l'enregistrement
                window.currentConvocationData = {
                    candidate_id: candidateId,
                    pdf_blob: blob
                };
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur lors de la génération du PDF', 'error');
            });
        });

        // Gestion de l'enregistrement de la convocation
        document.getElementById('saveConvocation').addEventListener('click', function() {
            if (!window.currentConvocationData) {
                showNotification('Aucune convocation à enregistrer', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('candidate_id', window.currentConvocationData.candidate_id);
            formData.append('type_document', 'convocation');
            formData.append('pdf_file', window.currentConvocationData.pdf_blob, 'convocation.pdf');

            fetch('save_convocation.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Convocation enregistrée avec succès', 'success');
                    document.getElementById('saveConvocation').disabled = true;
                    // Réinitialiser le formulaire
                    document.getElementById('convocationForm').reset();
                    document.getElementById('pdfPreview').style.display = 'none';
                } else {
                    throw new Error(data.error || 'Erreur lors de l\'enregistrement');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                console.log(error.message, 'error');
            });
        });

        // Charger les candidats validés au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadValidatedCandidates();
        });

        // Fonction pour charger les résultats
        function loadResults() {
            fetch('get_results.php')
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Erreur lors du chargement des résultats');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    const tbody = document.getElementById('results-table-body');
                    tbody.innerHTML = '';
                    
                    let admisCount = 0;
                    let rejetesCount = 0;
                    let enAttenteCount = 0;

                    data.forEach(result => {
                        const tr = document.createElement('tr');
                        const dateOuverture = new Date(result.date_ouverture).getFullYear();
                        const dateCloture = new Date(result.date_cloture).getFullYear();
                        
                        // Vérification des champs avant de les utiliser
                        const nomCandidat = result.nom_candidat || 'Nom inconnu';
                        const concoursNom = result.concours_nom || 'Concours inconnu';
                        const centreVille = result.centre_ville || 'Ville inconnue';
                        const centreLieu = result.centre_lieu || 'Lieu inconnu';
                        
                        tr.innerHTML = `
                            <td>${result.inscription_id}</td>
                            <td>${nomCandidat}</td>
                            <td>${concoursNom}</td>
                            <td>${dateOuverture}-${dateCloture}</td>
                            <td>${centreVille} - ${centreLieu}</td>
                            <td>${result.note}</td>
                            <td><span class="decision-badge decision-${result.decision}">${result.decision}</span></td>
                            <td>${new Date(result.created_at).toLocaleDateString()}</td>
                            <td>
                                <button class="action-btn edit-btn" onclick="editResult(${result.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteResult(${result.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);

                        if (result.decision === 'admis') admisCount++;
                        else if (result.decision === 'rejete') rejetesCount++;
                        else enAttenteCount++;
                    });

                    document.getElementById('admis-count').textContent = admisCount;
                    document.getElementById('rejetes-count').textContent = rejetesCount;
                    document.getElementById('en-attente-count').textContent = enAttenteCount;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert(error.message || 'Erreur lors du chargement des résultats');
                });
        }

        // Fonction pour filtrer les résultats
        function filterResults(searchTerm) {
            const rows = document.querySelectorAll('#results-table-body tr');
            searchTerm = searchTerm.toLowerCase();
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }

        // Écouter la barre de recherche
        document.getElementById('search-results').addEventListener('input', function(e) {
            filterResults(e.target.value);
        });

        // Fonction pour supprimer un résultat
        function deleteResult(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce résultat ?')) {
                fetch(`delete_result.php?id=${id}`, { 
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Erreur lors de la suppression');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        loadResults();
                        alert('Résultat supprimé avec succès');
                    } else {
                        throw new Error(data.error || 'Erreur lors de la suppression');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert(error.message || 'Erreur lors de la suppression');
                });
            }
        }

        // Fonction pour éditer un résultat
        function editResult(id) {
            fetch(`get_result.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Erreur lors de la récupération des données');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('modal-title').textContent = 'Modifier un résultat';
                    document.getElementById('result-id').value = data.id;
                    loadInscriptions().then(() => {
                        const select = document.getElementById('inscription-id');
                        select.value = data.inscription_id;
                        $(select).trigger('change');
                    });
                    document.getElementById('note').value = data.note;
                    document.getElementById('decision').value = data.decision;
                    document.getElementById('result-modal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert(error.message || 'Erreur lors de la récupération des données');
                });
        }

        // Gestion du formulaire
        document.getElementById('result-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer les données du formulaire
            const formData = new FormData(this);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }

            // Validation de la note
            const note = parseFloat(data.note);
            if (isNaN(note) || note < 0 || note > 1000) {
                alert('La note doit être un nombre entre 0 et 1000');
                return;
            }

            // Validation de l'inscription
            if (!data.inscription_id) {
                alert('Veuillez sélectionner un candidat');
                return;
            }

            // Validation de la décision
            if (!data.decision) {
                alert('Veuillez sélectionner une décision');
                return;
            }

            fetch('save_result.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || 'Erreur lors de la sauvegarde');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('result-modal').style.display = 'none';
                    loadResults();
                    alert(data.message || 'Opération réussie');
                } else {
                    throw new Error(data.error || 'Erreur lors de la sauvegarde');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert(error.message || 'Erreur lors de la sauvegarde');
            });
        });

        // Fermer le modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('result-modal').style.display = 'none';
        });

        // Charger les résultats au chargement de la page
        document.addEventListener('DOMContentLoaded', loadResults);

        // Fonction pour charger les inscriptions
        function loadInscriptions() {
            return fetch('get_inscriptions.php')
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Erreur lors du chargement des inscriptions');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues:', data); // Pour le débogage
                    
                    const select = document.getElementById('inscription-id');
                    select.innerHTML = '<option value="">Sélectionnez un candidat</option>';
                    
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(inscription => {
                            const option = document.createElement('option');
                            option.value = inscription.id;
                            option.textContent = `${inscription.nom_candidat} - ${inscription.concours_nom} (${inscription.centre_ville})`;
                            select.appendChild(option);
                        });
                    } else {
                        console.error('Aucune donnée reçue ou format incorrect');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert(error.message || 'Erreur lors du chargement des inscriptions');
                });
        }

        // Fonction pour formater l'affichage des options
        function formatInscription(inscription) {
            if (!inscription.id) return inscription.text;
            
            const text = inscription.text;
            const parts = text.split(' - ');
            const candidat = parts[0];
            const details = parts[1];
            
            return $(`
                <div style="padding: 5px;">
                    <div style="font-weight: bold;">${candidat}</div>
                    <div style="color: #666; font-size: 0.9em;">${details}</div>
                </div>
            `);
        }

        // Charger les inscriptions au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadResults();
            loadInscriptions();
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation des fonctionnalités
            loadResults();
            
            // Écouter la barre de recherche
            const searchInput = document.getElementById('search-results');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    filterResults(e.target.value);
                });
            }

            // Gestion du formulaire
            const resultForm = document.getElementById('result-form');
            if (resultForm) {
                resultForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const data = {
                        id: formData.get('result-id'),
                        inscription_id: formData.get('inscription-id'),
                        note: formData.get('note'),
                        decision: formData.get('decision')
                    };

                    // Validation
                    if (!data.inscription_id) {
                        console.log('Veuillez sélectionner un candidat');
                        return;
                    }

                    if (!data.note || isNaN(data.note) || data.note < 0 || data.note > 20) {
                        alert('Veuillez entrer une note valide entre 0 et 20');
                        return;
                    }

                    if (!data.decision) {
                        alert('Veuillez sélectionner une décision');
                        return;
                    }

                    // Envoi des données
                    fetch('save_result.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.error || 'Erreur lors de la sauvegarde');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            loadResults();
                            document.getElementById('result-modal').style.display = 'none';
                            alert('Résultat enregistré avec succès');
                        } else {
                            throw new Error(data.error || 'Erreur lors de la sauvegarde');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert(error.message || 'Erreur lors de la sauvegarde');
                    });
                });
            }
        });

        // Fonction pour ouvrir le modal d'ajout
        function openAddResultModal() {
            document.getElementById('modal-title').textContent = 'Ajouter un résultat';
            document.getElementById('result-form').reset();
            document.getElementById('result-id').value = '';
            document.getElementById('result-modal').style.display = 'block';
            loadInscriptions();
        }
    </script>
</body>
</html>