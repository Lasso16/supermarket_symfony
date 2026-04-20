# Supermarket Symfony

Supermarket Symfony is a web application built with Symfony 6.1 for managing a supermarket-style catalog. It includes a public storefront, user accounts, product management, shopping cart and checkout flows, administrative screens, and a JWT-protected API.

## Features

- Public product listing with filters by category, date range, search text, and seller
- Product detail pages
- User registration, login, logout, and profile management
- Shopping cart and checkout flow
- Product management for regular users and administrators
- Admin area for users and products
- REST API for authentication, profile access, and product CRUD operations
- PostgreSQL and Mailpit services available through Docker Compose

## Requirements

- PHP 8.1 or newer
- Composer
- A database server compatible with Doctrine ORM
- Optional: Docker and Docker Compose

## Installation

1. Clone the repository.
2. Install PHP dependencies:

```bash
composer install
```

3. Configure your environment variables.

   The committed `.env` file currently points to a local MariaDB/MySQL database, while the included Docker Compose setup provisions PostgreSQL. Make sure `DATABASE_URL` matches the environment you want to use.

4. Generate JWT keys if they are not already present:

```bash
php generate_keys.php
```

5. Create or update the database schema:

```bash
php bin/console doctrine:migrations:migrate
```

   If you prefer to start from the included SQL dump, you can import `sys_final.sql` instead of running migrations.

## Running With Docker

Start the services with:

```bash
docker compose up -d
```

This starts the database and Mailpit services defined in `compose.yaml` and `compose.override.yaml`.

## Running Locally

Start the Symfony local server with:

```bash
symfony serve
```

or use PHP's built-in server if you prefer:

```bash
php -S 127.0.0.1:8000 -t public
```

## Main Web Routes

- `/` Home page and product catalog
- `/login` Login
- `/register` Registration
- `/logout` Logout
- `/producto` Public product listing
- `/producto/detalle/{id}` Product details
- `/categoria` Category listing
- `/carrito` Shopping cart
- `/compra/checkout` Checkout
- `/compra/historial` Purchase history
- `/perfil` User profile
- `/mis-productos` Own products
- `/admin/producto` Product administration
- `/admin/usuarios` User administration

## API Endpoints

The API is exposed under `/api/v1` and uses JWT authentication for protected routes.

Authentication and profile:

- `POST /api/v1/auth/register` Register a new user
- `POST /api/v1/auth/login_check` Obtain a JWT token
- `GET /api/v1/profile` Get the authenticated user profile
- `PATCH /api/v1/profile/password` Change the authenticated user password

Products:

- `GET /api/v1/productos` List products with optional filters
- `GET /api/v1/producto/{id}` Get one product
- `POST /api/v1/producto` Create a product
- `PUT /api/v1/producto/{id}` Update a product
- `DELETE /api/v1/producto/{id}` Delete a product

## Testing

Run the test suite with:

```bash
php bin/phpunit
```

## Project Structure

- `src/Controller` Web and API controllers
- `src/Entity` Doctrine entities
- `src/BLL` Business logic layer
- `src/Form` Symfony form types
- `src/Repository` Doctrine repositories
- `templates` Twig templates
- `migrations` Database migrations

## Notes

- The application uses Symfony security with role-based access control.
- `ROLE_ADMIN` inherits `ROLE_USER` privileges.
- Mailpit is available in Docker for local email testing.