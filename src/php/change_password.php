<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Récupérer les données du formulaire
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Vérifier que tous les champs sont remplis
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
    exit();
}

// Vérifier que les nouveaux mots de passe correspondent
if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Les nouveaux mots de passe ne correspondent pas']);
    exit();
}

// Récupérer le mot de passe actuel de l'utilisateur
$stmt = $conn->prepare("SELECT mot_de_passe FROM UTILISATEUR WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si le mot de passe actuel est correct
if (!password_verify($currentPassword, $user['mot_de_passe'])) {
    echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect']);
    exit();
}

// Hasher le nouveau mot de passe
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Mettre à jour le mot de passe
$stmt = $conn->prepare("UPDATE UTILISATEUR SET mot_de_passe = ?, updated_at = NOW() WHERE id = ?");
$result = $stmt->execute([$hashedPassword, $_SESSION['user_id']]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Mot de passe mis à jour avec succès']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe']);
} 