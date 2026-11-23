# AGENTS.md

This file provides the main information and guidelines for AI coding assistants working on the Petly project. Read it before making any changes to the code.

## About the Project

Petly is a pet shop e-commerce web application developed for the IT 2001 Web Programming course at International Burch University.

The idea is to create a platform where customers can browse and purchase pet products such as food, toys, accessories, grooming supplies, and health products. Users can add products to their cart, place orders, and choose delivery within Bosnia and Herzegovina. Payment is handled through cash on delivery.

There is also a Shop Admin role responsible for managing products, categories, and customer orders. Admins can also select which products will be displayed as featured products on the homepage.

## Technologies

The project uses the following technologies:

- Backend: PHP with the FlightPHP framework
- Database: MySQL with PDO for database operations
- Frontend: HTML, CSS, and vanilla JavaScript
- UI: Bootstrap 5 and Bootstrap Icons, loaded through CDN

The frontend does not use React, Vue, or any other JavaScript framework.

The project should stay within these technologies. Do not introduce additional languages, runtimes, build tools, or testing tools such as Python or Node.js.

## Project Architecture

The backend follows a three-layer architecture consisting of Routes, Services, and DAO.

### 1. Routes

Location: `backend/routes/`

Routes are responsible for receiving HTTP requests, calling the appropriate service method, and returning JSON responses with the correct HTTP status codes.

Each route should call exactly one service method.

Routes should not contain SQL queries or business logic.

There is one route file per resource (`UserRoutes.php`, `CategoryRoutes.php`, `ProductRoutes.php`, `OrderRoutes.php`, `OrderItemRoutes.php`, `ContactRoutes.php`, `BreedFactRoutes.php`). Routes use plural nouns and `@id:[0-9]+` for ids, read the body with `Flight::request()->data->getData()` and query parameters with `Flight::request()->query`, cast the id with `(int)`, and answer with `Flight::json()`. When a service returns `null` (or `false` from `delete()`), the route answers 404. `POST` answers 201.

`backend/index.php` is the front controller: it loads `vendor/autoload.php`, registers every service with `Flight::register('productService', 'ProductService')`, loads the route files and maps one error handler (`Flight::map('error')`) plus JSON answers for unknown routes (404) and wrong methods (405). `backend/.htaccess` sends every request that is not a real file or folder to `index.php`.

### 2. Services

Location: `backend/services/`

Services handle the main application logic. This includes validating user input, applying business rules, and communicating with one or more DAO methods.

Services should not contain SQL queries or directly handle HTTP requests and responses.

Services report problems with exceptions and never know about HTTP. Invalid or missing input throws `InvalidArgumentException` (answered with 400), a broken business rule throws `DomainException` (409), a failing external API throws `ExternalApiException` (503), and a missing object is returned as `null` (the route answers 404). The error handler in `index.php` turns these into JSON answers with `Flight::jsonHalt()`; anything else becomes a 500 with a general message, and the real error goes to `backend/logs/error.log`.

Services build a new allow-listed array before calling the DAO and never pass the request body on. Prices, totals, the order status on creation and the payment method always come from the server, never from the client.

Every entity service extends `BaseService`, which holds `getAll()`, `getById()`, `delete()` and small validation helpers (`requiredText()`, `optionalText()`, `requiredEmail()`, `toInt()`). A service that needs several tables receives the extra DAOs through its constructor with default values (dependency injection), for example `OrderService` uses `OrderDao`, `OrderItemDao`, `ProductDao` and `UserDao`. `BreedFactService` calls TheDogAPI and TheCatAPI with cURL and a timeout.

### 3. DAO

Location: `backend/dao/`

The DAO layer is responsible for all database operations.

All SQL queries must be written here and executed using PDO prepared statements.

Each DAO should extend a shared `BaseDao`, which handles the database connection and common CRUD operations.

Do not place business logic or HTTP-related code inside DAO classes.

Current DAO classes (one per table): `UserDao`, `CategoryDao`, `ProductDao`, `OrderDao`, `OrderItemDao` and `ContactDao`. `BaseDao` provides `getAll()`, `getById($id)`, `insert($data)`, `update($id, $data)` and `delete($id)`. A child DAO only passes its table name to `BaseDao` and adds queries that are specific to its table. `insert()` and `update()` build column names from the array keys, so only a service may call them, and only with keys it chose itself, never with keys taken directly from a request.

When several changes belong together (an order and its order items), use a transaction in the DAO (`beginTransaction()`, `commit()`, `rollBack()`), as in `OrderDao::createOrder()`.

## Database

The database is MySQL and is named `Petly`. The schema and the sample data live in `backend/database/petly.sql`. Any change to the schema must be made in that file too, so that the project can always be set up again by importing it.

Tables: `users`, `categories`, `products`, `orders`, `order_items`, `contacts`. Every table has an `id INT AUTO_INCREMENT` primary key, and foreign keys are named after the table they point to (`user_id`, `category_id`, `order_id`, `product_id`).

Prices are stored as `DECIMAL`, never as `FLOAT`. Passwords are stored only as hashes (`password_hash`). `products.pet_type` (`dog`, `cat` or `both`) says which animal a product is for and decides which breed facts the product detail page shows.

## API Documentation

The API is documented with OpenAPI using swagger-php 6 and PHP 8 attributes (`use OpenApi\Attributes as OA;`). `backend/public/v1/docs/doc_setup.php` holds the API info, the server URL and the reusable schemas (an input schema and an output schema per entity, plus `Error` and `Message`). Every route file has a matching class in `paths/` with one empty method per endpoint that only carries the attribute. Whenever a route is added or changed, change its attribute in the same commit and check Swagger UI at `backend/public/v1/docs/`. Paths use `{id}` (OpenAPI), not `@id` (FlightPHP).

## Logging

`backend/utils/Logger.php` writes `backend/logs/info.log` and `backend/logs/error.log` with lines like `[2026-12-01 14:05:12] INFO ...`. Unhandled exceptions and external API failures go to the error log, successful external API calls to the info log. Never log passwords, tokens or API keys.

## Configuration

Database credentials, JWT secrets, and other sensitive configuration values should be stored in:

`backend/config/config.php`

This file must never be committed to Git. Besides the database settings it holds the TheDogAPI and TheCatAPI URLs and keys and the external API timeout.

Instead, keep a `backend/config/config.example.php` file in the repository to show which configuration values are required.

## Frontend Structure

The frontend is built as a single-page application using plain HTML, CSS, and JavaScript.

Navigation is handled through a hash-based router, using URLs such as:

- `#/home`
- `#/shop`
- `#/product/1`
- `#/cart`
- `#/admin`

Pages should load dynamically without refreshing the entire application.

Each page has its own HTML fragment inside `frontend/views/`. These fragments are loaded using JavaScript `fetch()`.

Because of this, the application needs to run through a local web server such as XAMPP with Apache. Opening `index.html` directly in the browser will not work correctly. Without XAMPP, run `php -S localhost:8000 server.php` in the project folder: `server.php` serves real files and sends other `/backend/...` requests to `backend/index.php`, like `.htaccess` does on Apache.

JavaScript functionality should be organized into separate files based on the features they handle.

## Folder Structure

    Petly/
    ├── AGENTS.md
    ├── README.md
    ├── CLAUDE.md
    ├── docs/
    │   ├── AI_LOG.md
    │   └── ERD.png
    ├── .gitignore
    ├── server.php               (router for php -S localhost:8000 server.php)
    ├── backend/
    │   ├── .htaccess
    │   ├── index.php
    │   ├── composer.json
    │   ├── composer.lock
    │   ├── vendor/              (Composer, not in Git)
    │   ├── routes/              (one *Routes.php file per resource)
    │   ├── services/            (BaseService + one service per entity, BreedFactService, ExternalApiException)
    │   ├── public/v1/docs/      (Swagger UI, swagger.php, doc_setup.php, paths/*Paths.php)
    │   ├── utils/
    │   │   └── Logger.php
    │   ├── dao/
    │   │   ├── BaseDao.php
    │   │   ├── UserDao.php
    │   │   ├── CategoryDao.php
    │   │   ├── ProductDao.php
    │   │   ├── OrderDao.php
    │   │   ├── OrderItemDao.php
    │   │   └── ContactDao.php
    │   ├── database/
    │   │   └── petly.sql
    │   ├── middleware/
    │   ├── config/
    │   │   ├── config.example.php
    │   │   └── config.php
    │   └── logs/
    └── frontend/
        ├── index.html
        ├── css/
        │   └── style.css
        ├── js/
        │   ├── products-data.js
        │   ├── main.js
        │   ├── shop.js
        │   ├── cart.js
        │   ├── home.js
        │   └── admin.js
        ├── views/
        └── assets/

Keep the project organized according to this structure. New files should be placed in the appropriate folders.

## Naming Conventions

To keep the code consistent, follow these naming conventions throughout the project.

### PHP

- Class names use PascalCase, for example `ProductDao`, `ProductService`, and `AuthMiddleware`.
- Methods and variables use camelCase.
- Class names should clearly describe their purpose.

### Database

- Tables and columns use snake_case.
- Table names should be plural, such as `products`, `categories`, and `order_items`.

### API

- Use RESTful endpoints with plural resource names.
- For example, `/products` for a collection and `/products/5` for a specific product.

### JavaScript and HTML

- JavaScript files and HTML view files use kebab-case, such as `product-detail.html` and `products-data.js`.
- JavaScript variables and functions use camelCase.
- Frontend modules should be attached to `window`, for example `window.Cart`, `window.Shop`, `window.Admin`, and `window.Home`.
- Do not introduce ES modules, since the project uses regular JavaScript script files.

### CSS

- CSS classes use kebab-case.
- Use the `petly-` prefix for custom project-specific classes when appropriate, for example `btn-petly-primary`.

## Development Guidelines

Follow these rules when working on the project.

### 1. Use only the existing technology stack

Stick to PHP, JavaScript, HTML, CSS, and MySQL.

Do not add other programming languages, runtimes, frameworks, build systems, or testing tools. Any scripts or local testing setup should also respect this requirement.

### 2. Keep the code clean

Do not add comments inside PHP, JavaScript, HTML, or CSS files.

The code should be written clearly enough to understand without unnecessary comments.

If an important technical decision needs an explanation, document it in `CLAUDE.md` instead.

### 3. Keep the architecture consistent

When implementing backend functionality, always respect the separation between Routes, Services, and DAO.

Database queries belong in DAO classes, business logic belongs in services, and HTTP handling belongs in routes.

Do not mix responsibilities between these layers.

### 4. Follow the UI rules

Do not use emojis as icons or as replacements for images.

Use Bootstrap Icons for interface icons and actual images for products and other visual content.

Keep the design consistent with the existing Petly styling and Bootstrap components.

### 5. Keep the writing simple

Do not use em dashes in UI text, documentation, commit messages, or any other written content. Use a regular hyphen instead.

Keep labels, messages, and descriptions clear and natural.

### 6. Test frontend changes

Every frontend change should be tested in a browser before considering it complete.

Do not assume that something works just because the code looks correct.

Check that navigation, page rendering, and related functionality work as expected.

### 7. Keep documentation updated

Whenever you make changes to the project structure, architecture, or important implementation decisions, update `CLAUDE.md`.

The goal is to make it easy for another AI assistant or a new session to understand the current state of the project without having to explore the entire codebase again.

## General Approach

When making changes, try to keep the implementation simple, organized, and consistent with the existing project.

Avoid introducing unnecessary complexity or changing parts of the application that are not related to the current task.

Reuse existing functionality where possible, follow the established naming conventions, and make sure new features fit into the current architecture.
