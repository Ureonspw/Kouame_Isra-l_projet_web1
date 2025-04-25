<?php
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: text/plain');

try {
    // Vérifier si la table INSCRIPTION existe
    $query = "SHOW TABLES LIKE 'INSCRIPTION'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $tableExists = $stmt->rowCount() > 0;

    if (!$tableExists) {
        echo "La table INSCRIPTION n'existe pas dans la base de données.\n";
        exit;
    }

    // Vérifier la structure de la table INSCRIPTION
    $query = "DESCRIBE INSCRIPTION";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Structure de la table INSCRIPTION :\n";
    print_r($structure);

    // Vérifier les données dans la table INSCRIPTION
    $query = "SELECT * FROM INSCRIPTION LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nDonnées dans la table INSCRIPTION :\n";
    print_r($data);

} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?> 