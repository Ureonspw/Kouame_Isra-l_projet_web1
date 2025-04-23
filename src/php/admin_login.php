<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/stylelog.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Connexion Admin - PUBLIGEST CI</title>
</head>
<body>
    <form class="form-structor" id="loginForm">
        <div class="signup">
            <h2 class="form-title" id="signup"><span>ou</span>Bienvenue Admin</h2>

            <div class="form-holder">
                <input required type="email" id="email" name="email" class="input" placeholder="Email" />
                <input required type="password" id="password" name="password" class="input" placeholder="Mot de passe" />
            </div>
            <button type="submit" class="submit-btn" id="submitBtn">
                <span>Se connecter</span>
                <div class="loading" style="display: none;"></div>
            </button>
        </div>
        <div class="login slide-up">
            <div class="center">
                <h2 class="form-title" id="login"><span>ou</span>Informations</h2>
                <div class="form-holder2">
                    Cette partie est strictement réservée aux administrateurs autorisés pour gérer la plateforme PUBLIGEST CI.
                    Si ce n'est pas votre cas, je vous invite à retourner au menu principal via ce bouton ▾
                    <br>
                </div>
                <button type="button" class="submit-btn" onclick="window.location.href='../../index.php'">Accueil</button>
            </div>
        </div>
    </form>

    <script src="../../assets/js/admin_login.js"></script>
    <script src="../../assets/js/mainlog.js"></script>
</body>
</html>