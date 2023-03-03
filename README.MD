# Projet file-storage

## Prérequis pour l'installation du projet avec Symfony 6.2

- PHP 8.0.13 minimum
- Composer 2.1.14 minimum
- Symfony CLI 4.27.0 minimum

## Installation du projet

- Cloner le projet
- Installer les dépendances avec la commande `composer install`
- Installer tailwindcss avec la commande `npm install`
- Modifier le fichier `.env` pour y ajouter les informations de connexion à la base de données
- Créer la base de données avec la commande `php bin/console doctrine:database:create`
- Créer les tables avec la commande `php bin/console doctrine:migrations:migrate`
- Lancer le serveur avec la commande `symfony server:start`

