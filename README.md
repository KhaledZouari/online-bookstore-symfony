# Online Bookstore Symfony

Application web de librairie en ligne avec catalogue, panier, commandes,
comptes utilisateurs et espace d’administration.

## Fonctionnalités vérifiées

- Catalogue et fiches détaillées des livres.
- Inscription, connexion et gestion du profil utilisateur.
- Ajout et retrait de livres dans le panier, puis validation d’une commande.
- Consultation de l’historique des commandes.
- Gestion des livres, auteurs, éditeurs, catégories, commandes et utilisateurs.
- Tableau de bord d’administration avec EasyAdmin.

## Stack

- PHP 8.2 ou supérieur et Symfony 7.4.
- Doctrine ORM et migrations.
- Twig, Symfony Forms et Symfony Security.
- EasyAdmin 4 pour l’administration.
- PHPUnit 11 pour les tests.

## Architecture

```mermaid
flowchart LR
    Browser[Navigateur] --> Symfony[Contrôleurs Symfony]
    Symfony --> Twig[Vues Twig]
    Symfony --> Services[Services métier]
    Services --> Doctrine[Doctrine ORM]
    Doctrine --> DB[(Base relationnelle)]
    Symfony --> Admin[EasyAdmin]
```

Les principales entités sont `Livre`, `Auteur`, `Editeur`, `Categorie`,
`User`, `Commande` et `LigneCommande`. Le panier est géré par `CartService`.

## Installation

Prérequis : PHP 8.2+, Composer et une base compatible avec Doctrine.

```bash
git clone https://github.com/KhaledZouari/online-bookstore-symfony.git
cd online-bookstore-symfony
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

Configurez `DATABASE_URL` dans un fichier `.env.local`, jamais dans un commit.

## Tests

```bash
php bin/phpunit
```

## Captures d’écran

Les futures captures peuvent être ajoutées dans `docs/screenshots/` avec des
données fictives.
