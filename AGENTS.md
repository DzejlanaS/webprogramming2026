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

### 2. Services

Location: `backend/services/`

Services handle the main application logic. This includes validating user input, applying business rules, and communicating with one or more DAO methods.

Services should not contain SQL queries or directly handle HTTP requests and responses.

Use exceptions for errors, such as `ValidationException` for invalid input or `NotFoundException` when something does not exist. Routes should handle these exceptions and return the appropriate HTTP response.

Services can extend a shared `BaseService` where it makes sense.

### 3. DAO

Location: `backend/dao/`

The DAO layer is responsible for all database operations.

All SQL queries must be written here and executed using PDO prepared statements.

Each DAO should extend a shared `BaseDao`, which handles the database connection and common CRUD operations.

Do not place business logic or HTTP-related code inside DAO classes.

## Configuration

Database credentials, JWT secrets, and other sensitive configuration values should be stored in:

`backend/config/config.php`

This file must never be committed to Git.

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

Because of this, the application needs to run through a local web server such as XAMPP with Apache. Opening `index.html` directly in the browser will not work correctly.

JavaScript functionality should be organized into separate files based on the features they handle.

## Folder Structure

    Pet Shop/
    ├── AGENTS.md
    ├── README.md
    ├── CLAUDE.md
    ├── docs/
    │   ├── AI_LOG.md
    │   └── ERD.png
    ├── .gitignore
    ├── backend/
    │   ├── routes/
    │   ├── services/
    │   ├── dao/
    │   ├── middleware/
    │   ├── config/
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
