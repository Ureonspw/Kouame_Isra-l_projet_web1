<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// L'utilisateur est connecté, on peut afficher la page
$user_email = $_SESSION['user_email'];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>PUBLIGEST CI - Dashboard Admin</title>
    <style>
    /* Styles pour la section utilisateurs */
    .user-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 28px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }

    .stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
    }

    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        box-shadow: 0 4px 15px rgba(231, 74, 59, 0.3);
    }

    .stat-icon i {
        color: #fff;
    }

    .stat-info h3 {
        margin: 0;
        font-size: 16px;
        color: #6e707e;
        font-weight: 600;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
        margin: 5px 0 0;
        color: #5a5c69;
        font-family: 'Nunito', sans-serif;
    }

    .users-table-container {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .table-header h3 {
        margin: 0;
        color: #5a5c69;
        font-size: 20px;
        font-weight: 700;
    }

    .search-box {
        position: relative;
        width: 300px;
    }

    .search-box input {
        width: 100%;
        padding: 12px 20px;
        padding-left: 45px;
        border: 1px solid #d1d3e2;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
    }

    .search-box input:focus {
        outline: none;
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6e707e;
    }

    .table-responsive {
        overflow-x: auto;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }

    .users-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 800px;
    }

    .users-table th {
        background-color: #f8f9fc;
        color: #5a5c69;
        font-weight: 700;
        padding: 15px 20px;
        text-align: left;
        border-bottom: 2px solid #e3e6f0;
    }

    .users-table td {
        padding: 15px 20px;
        color: #5a5c69;
        border-bottom: 1px solid #e3e6f0;
        vertical-align: middle;
    }

    .users-table tr:hover {
        background-color: #f8f9fc;
    }

    .users-table tr:last-child td {
        border-bottom: none;
    }

    .action-btn {
        padding: 8px 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .delete-btn {
        background-color: #e74a3b;
        color: white;
    }

    .delete-btn:hover {
        background-color: #d52a1a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(231, 74, 59, 0.3);
    }

    /* Responsive design */
    @media (max-width: 1200px) {
        .user-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .user-stats {
            grid-template-columns: 1fr;
        }
        
        .table-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .search-box {
            width: 100%;
        }
        
        .stat-card {
            padding: 20px;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }
        
        .stat-number {
            font-size: 24px;
        }
    }

    @media (max-width: 576px) {
        .users-table-container {
            padding: 20px;
        }
        
        .users-table th,
        .users-table td {
            padding: 12px 15px;
        }
        
        .action-btn {
            padding: 6px 12px;
            font-size: 13px;
        }
    }

    /* Styles pour la section concours */
    .domaines-section,
    .concours-section {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .section-header h3 {
        margin: 0;
        color: #5a5c69;
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header h3 i {
        color: #4e73df;
        font-size: 24px;
    }

    .add-btn {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .add-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
    }

    .domaines-list,
    .concours-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .domaine-card,
    .concours-card {
        background: #f8f9fc;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e3e6f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .domaine-card::before,
    .concours-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #4e73df, #224abe);
    }

    .domaine-card:hover,
    .concours-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .domaine-card h4,
    .concours-card h4 {
        margin: 0 0 10px 0;
        color: #5a5c69;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .domaine-card h4 i,
    .concours-card h4 i {
        color: #4e73df;
        font-size: 18px;
    }

    .concours-card p {
        margin: 5px 0;
        color: #6e707e;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .concours-card p i {
        color: #858796;
        font-size: 14px;
        min-width: 16px;
    }

    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .edit-btn {
        background: #4e73df;
        color: white;
    }

    .delete-btn {
        background: #e74a3b;
        color: white;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        overflow-y: auto;
    }

    .modal-content {
        background: white;
        width: 90%;
        max-width: 600px;
        margin: 30px auto;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        position: relative;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: #5a5c69;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-header h3 i {
        color: #4e73df;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6e707e;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #5a5c69;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group label i {
        color: #4e73df;
        font-size: 16px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .submit-btn,
    .cancel-btn {
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .submit-btn {
        background: #4e73df;
        color: white;
        border: none;
    }

    .cancel-btn {
        background: #e74a3b;
        color: white;
        border: none;
    }

    .submit-btn:hover,
    .cancel-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .domaines-list,
        .concours-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .domaines-list,
        .concours-list {
            grid-template-columns: 1fr;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .add-btn {
            width: 100%;
            justify-content: center;
        }
        
        .modal-content {
            width: 95%;
            margin: 20px auto;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .submit-btn,
        .cancel-btn {
            width: 100%;
            justify-content: center;
        }
        
        .card-actions {
            flex-direction: column;
        }
        
        .action-btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .domaines-section,
        .concours-section {
            padding: 15px;
        }
        
        .modal-body {
            padding: 15px;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 8px 12px;
        }
    }

    /* Styles pour les cartes de sessions et centres */
    .session-card, .center-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid #4e73df;
    }

    .session-card:hover, .center-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .session-card h4, .center-card h4 {
        color: #4e73df;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .session-card p, .center-card p {
        color: #5a5c69;
        margin: 8px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .session-card i, .center-card i {
        color: #858796;
        width: 20px;
        text-align: center;
    }

    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }

    .edit-btn {
        background: #4e73df;
        color: white;
    }

    .delete-btn {
        background: #e74a3b;
        color: white;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
        text-align: center;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .session-card, .center-card {
            padding: 15px;
        }

        .card-actions {
            flex-direction: column;
        }

        .action-btn {
            width: 100%;
            justify-content: center;
        }
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
            <p>bienvenu sur le dashboard admin de PUBLIGEST CI. vous pouvez gérer les informations de l'entreprise et les candidatures.</p>
        </div>
        <div class="contenuvus">
            <div class="menu1">
                <img src="" alt="">
                <h3>Inscription</h3>
            </div>
            <div class="menu2">
                <img src="" alt="">
                <h3>Inscription</h3>
            </div>
            <div class="menu3">
                <img src="" alt="">
                <h3>Inscription</h3>
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
                        <li data-section="statistics">
                            <a href="#">
                                <i class="fas fa-chart-line"></i>
                                <span>Statistiques</span>
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
                        <li data-section="applications">
                            <a href="#">
                                <i class="fas fa-file-alt"></i>
                                <span>Candidatures</span>
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
                                <span>Dossiers candidats</span>
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
                        <li data-section="settings">
                            <a href="#">
                                <i class="fas fa-cog"></i>
                                <span>Paramètres</span>
                            </a>
                        </li>
                        <li data-section="reports">
                            <a href="#">
                                <i class="fas fa-file-pdf"></i>
                                <span>Rapports</span>
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
                    <div class="search-box">
                        <input type="text" placeholder="Rechercher...">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="admin-actions">
                        <button class="notification-btn" aria-label="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <button class="profile-btn" aria-label="Profile">
                            <img src="../assets/images/profile.png" alt="Profile">
                        </button>
                    </div>
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
                                <p class="stat-number">12</p>
                                <span class="stat-change positive">+2 ce mois</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon blue">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Candidats Inscrits</h3>
                                <p class="stat-number">1,234</p>
                                <span class="stat-change positive">+156 aujourd'hui</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Dossiers à Traiter</h3>
                                <p class="stat-number">45</p>
                                <span class="stat-change negative">Urgent</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon red">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Taux de Complétion</h3>
                                <p class="stat-number">78%</p>
                                <span class="stat-change positive">+5% ce mois</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-grid">
                    <div class="widget">
                        <div class="widget-header">
                            <h3>Concours Récents</h3>
                            <button class="view-all">Voir tout</button>
                        </div>
                        <div class="widget-content">
                            <div class="concours-list">
                                <div class="concours-item">
                                    <div class="concours-info">
                                        <h4>Concours ENA 2025</h4>
                                        <p>Date limite: 15/06/2025</p>
                                    </div>
                                    <div class="concours-stats">
                                        <span class="stat">156 inscrits</span>
                                        <span class="status active">Actif</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="widget">
                        <div class="widget-header">
                            <h3>Dossiers en Attente</h3>
                            <button class="view-all">Voir tout</button>
                        </div>
                        <div class="widget-content">
                            <div class="dossiers-list">
                                <div class="dossier-item">
                                    <div class="dossier-info">
                                        <h4>KOUAME ISRAEL</h4>
                                        <p>Concours ENA 2025</p>
                                    </div>
                                    <div class="dossier-actions">
                                        <button class="action-btn view">Voir</button>
                                        <button class="action-btn validate">Valider</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <!-- Le contenu sera ajouté ultérieurement -->
                </div>
            </section>

            <!-- Applications Section -->
            <section id="applications" class="content-section">
                <div class="section-content">
                    <h2>Candidatures</h2>
                    <!-- Add applications content here -->
                </div>
            </section>

            <!-- Candidate Files Section -->
            <section id="candidate-files" class="content-section">
                <div class="section-content">
                    <h2>Dossiers candidats</h2>
                    <!-- Add candidate files content here -->
                </div>
            </section>

            <!-- Validation Section -->
            <section id="validation" class="content-section">
                <div class="section-content">
                    <h2>Validation des dossiers</h2>
                    <!-- Add validation content here -->
                </div>
            </section>

            <!-- Results Section -->
            <section id="results" class="content-section">
                <div class="section-content">
                    <h2>Résultats des concours</h2>
                    <!-- Add results content here -->
                </div>
            </section>

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
                            <div class="search-box">
                                <input type="text" id="userSearch" placeholder="Rechercher un utilisateur...">
                                <i class="fas fa-search"></i>
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
            <section id="settings" class="content-section">
                <div class="section-content">
                    <h2>Paramètres du système</h2>
                    <!-- Add settings content here -->
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports" class="content-section">
                <div class="section-content">
                    <h2>Rapports et statistiques</h2>
                    <!-- Add reports content here -->
                </div>
            </section>
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
                .then(response => response.json())
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
                .catch(error => console.error('Erreur:', error));
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
    </script>
</body>
</html>