# Online Bookstore — Symfony

A full-stack bookstore application with a product catalog, shopping cart,
ordering workflow, user accounts, and an administration area.

## Features

- Book catalog with authors, publishers, categories, and search
- Session-based cart management
- Customer accounts and order history
- Checkout and order lifecycle management
- EasyAdmin back office for catalog and order administration

## Stack

PHP, Symfony, Doctrine ORM, Twig, EasyAdmin, Bootstrap, and PHPUnit.

## Architecture

The application follows Symfony's controller–service–repository structure.
Doctrine entities model books, authors, publishers, categories, users, orders,
and order lines.

## Local setup

Prerequisites: PHP 8.2+, Composer, and a Doctrine-compatible database.

```bash
git clone https://github.com/KhaledZouari/online-bookstore-symfony.git
cd online-bookstore-symfony
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

Set `DATABASE_URL` in `.env.local`; never commit credentials.

## Tests

```bash
php bin/phpunit
```

## Screenshots

Use `docs/screenshots/` for screenshots containing only fictional data.

## License

Distributed under the MIT License. See [LICENSE](LICENSE).

