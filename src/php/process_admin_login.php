<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

try {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe et est un admin
    $stmt = $conn->prepare("SELECT id, email, mot_de_passe, role FROM UTILISATEUR WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("Email ou mot de passe incorrect.");
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier le mot de passe
    if (!password_verify($password, $user['mot_de_passe'])) {
        throw new Exception("Email ou mot de passe incorrect.");
    }

    // Stocker les informations de l'utilisateur dans la session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['is_logged_in'] = true;

    echo json_encode(['success' => true, 'message' => 'Connexion réussie', 'redirect' => 'admin_dashboard.php']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 