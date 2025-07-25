��#   E X A M _ G i t 
 # Système de Gestion des Employés

Ce projet est une application web PHP pour gérer les employés dans une base de données MySQL. Il comprend des fonctionnalités CRUD (Create, Read, Update, Delete) avec une interface utilisateur moderne et des animations fluides.
## Fonctionnalités

- Enregistrement de nouveaux employés avec photo
- Affichage liste des employés avec pagination
- Recherche dynamique en temps réel
- Modification des informations employé
- Suppression d'employés
- Design moderne avec animations CSS
-Interface responsive

## Prérequis

- Serveur web Apache (XAMPP/WAMP/MAMP)
- PHP 7.4+
- MySQL 5.7+
- phpMyAdmin (recommandé)

## Installation

1. **Configurer la base de données**:
   - Importer le fichier SQL fourni dans phpMyAdmin
   - OU exécuter manuellement la commande SQL de création de table

2. **Configurer la connexion**:
   - Modifier les identifiants de connexion dans chaque fichier PHP
   ```php
   $host = 'localhost';
   $dbname = 'gestion_employes';
   $username = 'root'; 
   $password = '';

       Droits d'accès:

        Créer un dossier uploads et lui donner les permissions d'écriture

Utilisation

    Page d'enregistrement (inscription.php):

        Remplir le formulaire avec les informations de l'employé

        Télécharger une photo (optionnel)

        Valider l'enregistrement

    Page de liste (liste.php):

        Visualiser tous les employés

        Rechercher avec la barre de recherche

        Modifier/Supprimer des employés

    Page de recherche (search.php):

        Recherche dynamique en temps réel

        Surbrillance des résultats

        Accès rapide à la fiche employé
 
Personnalisation

    Couleurs: Modifier les variables CSS dans :root

    Animations: Ajuster les délais dans les @keyframes

    Champs: Ajouter/supprimer des champs dans la base de données et les formulaires
