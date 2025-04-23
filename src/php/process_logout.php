<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Détruire la session
session_destroy();

// Renvoyer une réponse JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Déconnexion réussie']);
?> 