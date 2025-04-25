<?php
session_start();

// Vérifier si l'inscription a réussi
if (!isset($_SESSION['inscription_success']) || !$_SESSION['inscription_success']) {
    header('Location: inscription_candidat.php');
    exit;
}

$num_inscription = $_SESSION['num_inscription'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Réussie - GUICHET UNIQUE DES CONCOURS ADMINISTRATIFS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-container {
            max-width: 600px;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 50px;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); }
            70% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .success-title {
            color: #28a745;
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .success-message {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .num-inscription {
            background: #e9ecef;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: #495057;
        }
        .btn-success {
            background: #28a745;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1 class="success-title">Inscription Réussie !</h1>
        <p class="success-message">
            Félicitations ! Votre inscription a été enregistrée avec succès.
        </p>
        <div class="num-inscription">
            Votre numéro d'inscription : <strong><?php echo htmlspecialchars($num_inscription); ?></strong>
        </div>
        <p class="success-message">
            Conservez précieusement ce numéro, il vous sera demandé pour toutes vos démarches.
        </p>
        <a href="../../index.php" class="btn btn-success">
            <i class="fas fa-home me-2"></i>Retour à l'accueil
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Nettoyer la session
unset($_SESSION['inscription_success']);
unset($_SESSION['num_inscription']);
?> 