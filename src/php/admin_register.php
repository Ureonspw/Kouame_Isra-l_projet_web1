<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérifier si les mots de passe correspondent
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM UTILISATEUR WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Cet email est déjà utilisé.");
        }

        // Préparer et exécuter la requête d'insertion
        $stmt = $conn->prepare("INSERT INTO UTILISATEUR (email, mot_de_passe, role) VALUES (?, ?, ?)");
        
        if ($stmt->execute([
            $_POST['email'],
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            'candidat' // Par défaut, tous les nouveaux utilisateurs sont des candidats
        ])) {
            $_SESSION['success_message'] = "Votre compte a été créé avec succès !";
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Compte créé avec succès']);
            exit();
        } else {
            throw new Exception("Une erreur est survenue lors de la création du compte.");
        }
    } catch (PDOException $e) {
        error_log("Erreur PDO: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Une erreur de base de données est survenue.']);
        exit();
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - PUBLIGEST CI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #F47721;
            --primary-light: #FF9F4A;
            --secondary-color: #0E9F60;
            --secondary-light: #2ECC71;
            --accent-color: #FFFFFF;
            --text-color: #2C3E50;
            --text-light: #64748B;
            --bg-color: #F8FAFC;
            --card-bg: #FFFFFF;
            --error-color: #EF4444;
            --success-color: #10B981;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(244, 119, 33, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(14, 159, 96, 0.1) 0%, transparent 50%);
            z-index: -1;
        }

        .register-container {
            width: 100%;
            max-width: 1200px;
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 700px;
            position: relative;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .illustration-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .illustration-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="rgba(255,255,255,0.1)" d="M0 0h100v100H0z"/></svg>');
            opacity: 0.1;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .logo-container img {
            width: 40px;
            height: 40px;
        }

        .logo-container h1 {
            color: var(--accent-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .illustration-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--accent-color);
        }

        .feature-text h3 {
            color: var(--accent-color);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .feature-text p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .form-section {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--text-light);
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--bg-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(244, 119, 33, 0.1);
            outline: none;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .password-toggle i {
            font-size: 1.1rem;
        }

        .btn {
            width: 100%;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: none;
            color: var(--accent-color);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--bg-color);
            border: 2px solid #E2E8F0;
            color: var(--text-color);
        }

        .btn-secondary:hover {
            background: #E2E8F0;
            transform: translateY(-2px);
        }

        .form-footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-light);
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .form-footer a:hover {
            color: var(--primary-light);
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--error-color);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success-color);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .register-container {
                grid-template-columns: 1fr;
            }

            .illustration-section {
                display: none;
            }

            .form-section {
                padding: 2rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }

            .register-container {
                border-radius: 16px;
            }

            .form-section {
                padding: 1.5rem;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="illustration-section">
            <div class="logo-container">
                <img src="../../assets/images/logo.png" alt="PUBLIGEST Logo">
                <h1>PUBLIGEST CI</h1>
            </div>
            
            <div class="illustration-content">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Inscription rapide</h3>
                        <p>Créez votre compte en quelques étapes simples</p>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Sécurité maximale</h3>
                        <p>Vos données sont protégées et sécurisées</p>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Concours accessibles</h3>
                        <p>Accédez à tous les concours de la fonction publique</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-header">
                <h2>Créez votre compte</h2>
                <p>Rejoignez PUBLIGEST CI pour accéder aux concours</p>
            </div>

            <?php if(isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form id="inscriptionForm" method="POST" action="admin_register.php">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="votre@email.com">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required
                               placeholder="••••••••">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                               placeholder="••••••••">
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="submitButton">
                            <span>Créer mon compte</span>
                            <div class="loading" style="display: none;"></div>
                        </button>
                    </div>
                </form>

                <div class="form-footer">
                    <p>Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('inscriptionForm');
            const submitButton = document.getElementById('submitButton');
            const loadingSpinner = submitButton.querySelector('.loading');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

            // Toggle password visibility
            function togglePasswordVisibility(input, button) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Toggle eye icon
                const icon = button.querySelector('i');
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            // Add click event listeners
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    togglePasswordVisibility(passwordInput, this);
                });
            }

            if (toggleConfirmPassword) {
                toggleConfirmPassword.addEventListener('click', function() {
                    togglePasswordVisibility(confirmPasswordInput, this);
                });
            }

            // Form validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('email').value;
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Veuillez entrer une adresse email valide',
                        confirmButtonColor: '#F47721'
                    });
                    return;
                }

                // Password validation
                if (password.length < 8) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Le mot de passe doit contenir au moins 8 caractères',
                        confirmButtonColor: '#F47721'
                    });
                    return;
                }

                if (password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Les mots de passe ne correspondent pas',
                        confirmButtonColor: '#F47721'
                    });
                    return;
                }

                // Show loading state
                submitButton.disabled = true;
                submitButton.querySelector('span').style.display = 'none';
                loadingSpinner.style.display = 'block';

                // Submit form
                fetch('process_register.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès !',
                            text: data.message,
                            confirmButtonColor: '#F47721'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'admin_register_success.php';
                            }
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: error.message || 'Une erreur est survenue lors de la création du compte',
                        confirmButtonColor: '#F47721'
                    });
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.querySelector('span').style.display = 'block';
                    loadingSpinner.style.display = 'none';
                });
            });

            // Add animation to feature cards
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html> 