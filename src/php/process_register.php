<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

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
        'admin' // Créer un compte administrateur
    ])) {
        $_SESSION['success_message'] = "Votre compte administrateur a été créé avec succès !";
        echo json_encode(['success' => true, 'message' => 'Compte administrateur créé avec succès']);
    } else {
        throw new Exception("Une erreur est survenue lors de la création du compte.");
    }
} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur de base de données est survenue.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 