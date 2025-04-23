<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    try {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        // Vérifier si l'email existe dans la table UTILISATEUR
        $stmt = $conn->prepare("SELECT * FROM UTILISATEUR WHERE email = ? AND role = 'candidat'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['email']; // On utilise l'email car il n'y a pas de nom dans UTILISATEUR
            $_SESSION['role'] = $user['role'];
            $_SESSION['success_message'] = "Connexion réussie !";
            
            // Redirection vers le dashboard candidat
            header("Location: ./src/php/dashboard_candidat.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Email ou mot de passe incorrect";
        }
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Erreur de connexion à la base de données";
        error_log("Erreur PDO: " . $e->getMessage());
    } catch(Exception $e) {
        $_SESSION['error_message'] = "Une erreur est survenue";
        error_log("Erreur: " . $e->getMessage());
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/mainpage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>PUBLIGEST CI</title>
</head>

    <body>

        <header>
            <div class="logo">
                <img src="./assets/images/logo.png" alt="logo" onclick="window.location.href='src/php/admin_login.php'"> 
            </div>
            
                <ul class="menu">
                    <li><a href="#">Accueil</a></li>
                    <li><a href="#">A propos</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Concour</a></li>
                </ul>
            <div class="login" id="loginButton" popovertarget="popover">login</div>
        </header>
        <div class="container">
            <div class="contenttitlegeneral">
                <h1>Bienvenue sur PUBLIGEST CI</h1>
            </div>
            <div class="content">
               <p>Notre plateforme facilite l'accès aux concours de la fonction publique en Côte d'Ivoire. Elle permet une inscription rapide, un suivi en temps réel des candidatures et un accès simple à toutes les informations essentielles, partout et à tout moment.</p>
            </div>
            <div class="choixmenu">
                <h2 onclick="window.location.href='./src/pdfs/Communique ENA.pdf'">Informations</h2>
                <h3 onclick="window.location.href='./src/php/inscription_candidat.php'">inscription</h3>
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
                <img src="./assets/images/carroussel1.png" alt="" class="caroussel1">
                <img src="./assets/images/caroussel2.png" alt="" class="caroussel2">
                <img src="./assets/images/carrousel2.png" alt="" class="caroussel3">


            </div>
        </div>



<div class="contentdevellopement">

    <div class="infoblcks">
        <div class="infoblcks1">
            <h1>COMMUNIQUE MEFPMA</h1>
            <h4>Il est porté à la connaissance des personnes désireuses de faire acte de candidature aux concours adminstratifs de la session 2025 que la fin des inscriptions en ligne , initialement prévue pour le vendredi 11 avril 2025, est reportée au mercredi 30 avril 2025.</h4>
            <div  onclick="window.location.href='./src/pdfs/Communique N-0499.pdf'">voir le Communiquer <h4>✒︎</h4></div>
        </div>
        <div class="infoblcks1">
            <h1>EPREUVE DE PRESELECTION </h1>
            <h4>Il est porté à la connaissance de l'ensemble des candidats aux concours direct d'entrée en 2026 à l'Ecole Nationale d'Administration (ENA), que les convocations pour l'étape de présélection sont disponible</h4>
            <div>voir la convocation <h4>✒︎</h4></div>
        </div>

    </div>

    <div class="concourinfobloc">
        <div class="concourinfobloc1">
            <h3>MFP - DIRECTION DES CONCOURS <samp><img src="./assets/images/book_26dp_EFEFEF_FILL0_wght400_GRAD0_opsz24.png" alt=""></samp></h3>
            <h4>Il est porté à la connaissance des personnes désireuses de faire acte de candidature aux concours administratifs de la session 2025 que la fin des inscriptions en ligne , initialement prévue pour le vendredi 11 avril 2025, est reportée au mercredi 30 avril 2025.</h4>
            <div>en savoir plus<h4>✒︎</h4></div>
        </div>
        <div class="concourinfobloc1">
            <h3>MFP. - DIASPORA <samp><img src="./assets/images/Material Icons Assignment 26dp.png" alt=""></samp></h3>
            <h4>Il est porté à la connaissance de l'ensemble des candidats aux concours direct d'entrée en 2026 à l'Ecole Nationale d'Administration (ENA), que les convocations pour l'étape de présélection sont disponible</h4>
            <div>voir la convocation <h4>✒︎</h4></div>
        </div>
        <div class="concourinfobloc1">
            <h3>ENA - ÉCOLE NATIONALE D'ADMINISTRATION <samp><img src="./assets/images/Material Symbols Icon 26dp.png" alt=""></samp></h3>
            <h4>Il est porté à la connaissance des personnes désireuses de faire acte de candidature aux concours direct d'entrée en 2026 à l'Ecole Nationale d'Administration (ENA), que les convocations pour l'étape de présélection sont disponible</h4>
            <div>voir la convocation <h4>✒︎</h4></div>
        </div>
    </div>


    <div class="info-special-container">
        <div class="info-special-header">
            <h3>DISPOSITIONS À PRENDRE AVANT L'INSCRIPTION EN LIGNE</h3>
            <div class="header-decoration"></div>
        </div>
        <div class="info-special-content">
            <div class="info-special-item">
                <div class="info-icon-wrapper">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="info-text">
                    <h4>Conditions de candidature</h4>
                    <p>Le candidat doit s'assurer de remplir les conditions de candidature figurant sur le communiqué ou l'arrêté d'ouverture du concours</p>
                    <div class="info-details">
                        <span class="info-tag">Important</span>
                    </div>
                </div>
            </div>
            <div class="info-special-item">
                <div class="info-icon-wrapper">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="info-text">
                    <h4>Compte Mobile Money</h4>
                    <p>Le candidat doit disposer d'un compte Mobile Money (ORANGE, MTN), TRESOR MONEY ou WAVE avec un montant équivalent au moins aux frais d'inscription</p>
                    <div class="info-details">
                        <span class="info-tag">Paiement</span>
                    </div>
                </div>
            </div>
            <div class="info-special-item">
                <div class="info-icon-wrapper">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="info-text">
                    <h4>Pièce d'identité</h4>
                    <p>Le candidat doit disposer d'une pièce d'identité valide</p>
                    <div class="info-details">
                        <span class="info-tag">Document</span>
                    </div>
                </div>
            </div>
            <div class="info-special-item">
                <div class="info-icon-wrapper">
                    <i class="fas fa-birthday-cake"></i>
                </div>
                <div class="info-text">
                    <h4>Extrait de naissance</h4>
                    <p>Le candidat doit disposer d'un extrait de naissance valide</p>
                    <div class="info-details">
                        <span class="info-tag">Document</span>
                    </div>
                </div>
            </div>
            <div class="info-special-item">
                <div class="info-icon-wrapper">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="info-text">
                    <h4>Document CMU</h4>
                    <p>Le candidat doit disposer d'un document prouvant son enrôlement à la Couverture Maladie Universelle (CMU) (carte d'assuré ou récépissé d'enrolement)</p>
                    <div class="info-details">
                        <span class="info-tag">Santé</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="info-special-notes">
            <div class="note-item">
                <div class="note-icon-wrapper">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="note-text">
                    <h4>Note importante</h4>
                    <p>Tous les paiements se font uniquement dans l'espace candidat.</p>
                    <div class="note-tag">Information</div>
                </div>
            </div>
            <div class="note-item">
                <div class="note-icon-wrapper">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="note-text">
                    <h4>Règlement</h4>
                    <p>Les téléphones portables et/ou tous autres moyens de communication sont formellement interdits sur le site de composition.</p>
                    <div class="note-tag">Avertissement</div>
                </div>
            </div>
            <div class="note-item">
                <div class="note-icon-wrapper">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="note-text">
                    <h4>Restriction ENA</h4>
                    <p>Les récépissés de demande CNI ne sont pas autorisés pour les candidats aux concours de l'ENA</p>
                    <div class="note-tag">Important</div>
                </div>
            </div>
        </div>
    </div>

    
</div>

    <div class="assistance-container">
        <div class="assistance-header">
            <h2>ASSISTANCE AUX CANDIDATS</h2>
            <p class="assistance-subtitle">Nous sommes là pour vous aider</p>
        </div>
        <div class="assistance-grid">
            <div class="assistance-card">
                <div class="card-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>Assistance Administration MFP</h3>
                <div class="phone-numbers">
                    <a href="tel:0595188035" class="phone-link">
                        <i class="fas fa-phone"></i>
                        <span>05 95 18 80 35</span>
                    </a>
                    <a href="tel:0141413009" class="phone-link">
                        <i class="fas fa-phone"></i>
                        <span>01 41 41 30 09</span>
                    </a>
                    <a href="tel:0150412296" class="phone-link">
                        <i class="fas fa-phone"></i>
                        <span>01 50 41 22 96</span>
                    </a>
                </div>
            </div>
            <div class="assistance-card">
                <div class="card-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Assistance Administration ENA</h3>
                <div class="phone-numbers">
                    <a href="tel:2722516060" class="phone-link">
                        <i class="fas fa-phone"></i>
                        <span>27 22 516 060</span>
                    </a>
                    <a href="tel:0778570224" class="phone-link">
                        <i class="fas fa-phone"></i>
                        <span>07 78 57 02 24</span>
                    </a>
                    <a href="tel:0160856175" class="phone-link">
                        <i class="fas fa-phone"></i>
                        <span>01 60 85 61 75</span>
                    </a>
                </div>
            </div>
            <div class="assistance-card">
                <div class="card-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Assistance Technique</h3>
                <div class="phone-numbers">
                    <a href="tel:2722509424" class="phone-link">
                        <i class="fas fa-phone"></i>
                        <span>27 22 50 94 24</span>
                    </a>
                </div>
            </div>
        </div>
    </div>









    <div id="popover" class="popover" popover>
        <div class="containerpop">
          
          <!-- Partie supérieure avec fond dégradé -->
          <div class="containerpopbox1">
            <div class="popup-header">
              <div class="popup-logo-container">
                <img src="./assets/images/logo.png" alt="PUBLIGEST Logo" class="popup-logo">
                <div class="logo-pulse"></div>
              </div>
              <h2>Connectez-vous à <span class="accent-text">PUBLIGEST</span></h2>
              <p class="popup-tagline">Votre plateforme de concours</p>
            </div>
          </div>
          
          <!-- Onglets de connexion -->
          
          <!-- Formulaire de connexion -->
          <div class="containerpopbox2">
            <div class="tab-content active" id="login-tab">
                <?php if(isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger" style="color: red; margin-bottom: 15px; padding: 10px; border-radius: 5px; background-color: #ffe6e6;">
                        <?php 
                            echo $_SESSION['error_message'];
                            unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="input-container">
                        <label for="email">
                            <i class="fas fa-user-circle"></i> Email
                        </label>
                        <div class="input-field">
                            <input
                                type="email"
                                class="entree1"
                                id="email"
                                name="email"
                                placeholder="Votre email"
                                required
                            />
                        </div>
                    </div>
                    
                    <div class="input-container">
                        <label for="password">
                            <i class="fas fa-lock"></i> Mot de passe
                        </label>
                        <div class="input-field password-field">
                            <input
                                type="password"
                                class="entree2"
                                id="password"
                                name="password"
                                placeholder="Votre mot de passe"
                                required
                            />
                            <button type="button" id="togglePassword" class="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="login-button">
                        <span>Connexion</span>
                        <div class="button-loader"></div>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
                
                <div class="login-divider">
                    <span>ou</span>
                </div>
                
                <div class="login-footer">
                    <p>Nouveau sur PUBLIGEST? <a class="switch-tab"  onclick="window.location.href='./src/php/inscription_candidat.php'">Créer un compte</a></p>
                </div>
            </div>
            
            <!-- Onglet d'inscription (masqué par défaut) -->
          </div>
          
          <!-- Informations de sécurité -->
          <div class="security-info">
            <i class="fas fa-shield-alt"></i>
            <span>Connexion sécurisée - Vos données sont protégées</span>
          </div>
        </div>
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
                <a href="#"><img src="./assets/images/facebook.png" alt="Facebook"></a>
                <a href="#"><img src="./assets/images/Instagram.png" alt="Twitter"></a>
                <a href="#"><img src="./assets/images/LinkedIn.webp" alt="LinkedIn"></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 PUBLIGEST CI. Tous droits réservés.</p>
    </div>
</footer>
        <script src="./assets/js/mainpage.js"></script> 
    </body>

</html>