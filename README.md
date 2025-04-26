# Projet Web - Kouame Israël

## Informations sur l'auteur
- **Nom**: Kouame Israël
- **Classe**: TS STIC 2
- **Spécialité**: Informatique

## Description du Projet
Ce projet est une application web développée en PHP avec une architecture MVC (Modèle-Vue-Contrôleur). Il utilise Composer pour la gestion des dépendances et intègre la bibliothèque TCPDF pour la génération de documents PDF.

## Structure du Projet
```
├── assets/          # Ressources statiques (CSS, JS, images)
├── config/          # Fichiers de configuration
├── database/        # Scripts et fichiers liés à la base de données
├── docs/            # Documentation du projet
├── src/             # Code source principal
├── uploads/         # Dossier pour les fichiers uploadés
├── vendor/          # Dépendances gérées par Composer
├── index.php        # Point d'entrée de l'application
├── composer.json    # Configuration des dépendances
└── composer.lock    # Verrouillage des versions des dépendances
```

## Dépendances
- **tecnickcom/tcpdf**: Version ^6.9
  - Utilisé pour la génération de documents PDF

## Installation
1. Cloner le dépôt
2. Installer les dépendances avec Composer:
   ```bash
   composer install
   ```
3. Configurer la base de données dans le dossier `config/`
4. Configurer les permissions du dossier `uploads/`

## Fonctionnalités
- Gestion des utilisateurs
- Génération de documents PDF
- Système d'upload de fichiers
- Interface utilisateur responsive

## Technologies Utilisées
- PHP
- MySQL
- TCPDF
- HTML/CSS
- JavaScript

## Contribution
Ce projet a été développé dans le cadre d'un projet scolaire par Kouame Israël, étudiant en TS STIC 2 spécialité Informatique.

## Licence
Tous droits réservés © 2024 Kouame Israël 