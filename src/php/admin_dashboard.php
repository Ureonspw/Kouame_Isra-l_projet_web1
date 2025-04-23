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
</head>

<body>
    <!-- Header original -->
    <header>
        <div class="logo">
            <img src="../../assets/images/logo.png" alt="logo">
        </div>
        
        <ul class="menu">
            <li><a href="#">Accueil</a></li>
            <li><a href="#">A propos</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">Concour</a></li>
        </ul>
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
                    <h2>Créer un nouveau concours</h2>
                    <!-- Add create contest form here -->
                </div>
            </section>

            <!-- Manage Contests Section -->
            <section id="manage-contests" class="content-section">
                <div class="section-content">
                    <h2>Gérer les concours</h2>
                    <!-- Add manage contests content here -->
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
                    <!-- Add users content here -->
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
    </script>
</body>
</html>