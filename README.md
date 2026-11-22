# Petly - Pet Shop

Course project for IT 2001 Web Programming, International Burch University. This README is updated as each milestone is completed (currently Milestones 1, 2 and 3).

## Project description

Petly is a single-page e-commerce application for a pet shop selling food, toys, accessories, grooming and health products for dogs and cats. Customers can browse products, add them to a cart and check out with delivery anywhere in Bosnia and Herzegovina, paying cash on delivery. A Shop Admin role manages products, categories and orders.

## Entities and relationships

The MySQL database (`Petly`) has six tables. The full schema with sample data is in [`backend/database/petly.sql`](backend/database/petly.sql).

| Table | Main columns | Foreign keys |
|---|---|---|
| `users` | first_name, last_name, email (unique), password_hash, role (`customer` / `admin`), created_at | - |
| `categories` | name (unique), description | - |
| `products` | name, description, price, stock_quantity, image_url, is_featured, pet_type (`dog` / `cat` / `both`), created_at | `category_id` -> `categories.id` |
| `orders` | order_number (unique, e.g. `PET-104822`), order_date, total_amount, status, payment_method, full_name, phone, email, street, city, postal_code, notes | `user_id` -> `users.id` |
| `order_items` | quantity, unit_price, subtotal | `order_id` -> `orders.id`, `product_id` -> `products.id` |
| `contacts` | name, email, subject, message, created_at | `user_id` -> `users.id` (optional) |

Every table has an `id INT AUTO_INCREMENT` primary key.

Relationships:

- A category has many products (1-N). A category that still has products cannot be deleted.
- A user places many orders (1-N). Every order belongs to a registered user.
- An order has many order items (1-N). Deleting an order also deletes its items (`ON DELETE CASCADE`).
- A product appears in many order items (1-N). A product that was ordered cannot be deleted, so order history stays complete.
- A user may send many contact messages (1-N). `contacts.user_id` is optional, because the Contact Us page also works for visitors who are not logged in. If a user is deleted, their messages stay and `user_id` becomes `NULL`.

The draft ERD from Milestone 1 is at [`docs/ERD.png`](docs/ERD.png). The final schema differs from it in a few places: primary keys are named `id` in every table, the Contact table is named `contacts` and its `user_id` is optional, and `orders` stores the delivery details in separate columns instead of one `delivery_address` column.

## Roles and test accounts

- Customer - browses products, manages a cart, places orders, views their own order history
- Shop Admin - manages products, categories and orders, and picks which products are featured on the homepage

Test accounts (included in the sample data; logging in works once authentication is implemented in Milestone 4):

| Role | Email | Password |
|---|---|---|
| Shop Admin | admin@petly.ba | Admin123! |
| Customer | amina.karic@example.com | Customer123! |
| Customer | emir.kovac@example.com | Customer123! |

## External API

The shop uses [TheDogAPI](https://thedogapi.com) and [TheCatAPI](https://thecatapi.com) for a **breed facts** feature on the product detail page. Every product has a `pet_type` (`dog`, `cat` or `both`). The endpoint `GET /products/{id}/breed-facts` asks the matching API for a random breed and returns its name, temperament, life span, breed group, origin, a short description and a photo. A product made for both animals gets one dog breed and one cat breed.

The call is made by `BreedFactService` on the backend, so the API keys never reach the browser. Both keys are stored in `backend/config/config.php`. The request has a 5 second timeout. If an API is slow, answers with an error or returns something unexpected, the failure is written to `backend/logs/error.log` and the endpoint answers `503` with a friendly message, while the rest of the application keeps working. Successful calls are written to `backend/logs/info.log`. The frontend card that shows these facts is connected in Milestone 4.

## Running the project locally

Requirements: XAMPP (Apache with `mod_rewrite`, MySQL or MariaDB, PHP 8.2 or newer with the `curl` and `pdo_mysql` extensions) and [Composer](https://getcomposer.org).

1. Copy the project folder into your XAMPP `htdocs` directory (or the document root of your local Apache server).
2. Start Apache and MySQL from the XAMPP control panel.
3. Import the database: open phpMyAdmin, go to the Import tab and import `backend/database/petly.sql`. The script creates the `Petly` database (if it does not exist yet), all tables and the sample data. From a terminal the same can be done with `mysql -u root < backend/database/petly.sql`.
4. Install the backend libraries (FlightPHP and swagger-php): open a terminal in the `backend` folder and run `composer install`. This creates the `vendor/` folder, which is ignored by Git.
5. Create the configuration file: copy `backend/config/config.example.php` to `backend/config/config.php` and fill in your database host, name, user and password, and your TheDogAPI and TheCatAPI keys (both are free at [thedogapi.com](https://thedogapi.com) and [thecatapi.com](https://thecatapi.com)). `config.php` is ignored by Git.
6. Make sure the web server can write to `backend/logs/` (the API writes `info.log` and `error.log` there).
7. Open `http://localhost/<project-folder>/frontend/` for the shop and `http://localhost/<project-folder>/backend/public/v1/docs/` for the API documentation.

**Without XAMPP (PHP built-in server).** Steps 3 to 5 are the same, with MySQL running. Then, in the project folder:

```bash
php -S localhost:8000 server.php
```

`server.php` does the job of Apache and `.htaccess`: it serves real files directly and sends every other `/backend/...` request to `backend/index.php`. Open `http://localhost:8000/frontend/` for the shop and `http://localhost:8000/backend/public/v1/docs/` for Swagger UI; the API base URL is `http://localhost:8000/backend`. Stop the server with Ctrl+C.

The frontend uses `fetch()` to load its pages, so it must be opened through a web server and not by double-clicking `index.html` directly. The frontend is not connected to the backend yet; that happens in Milestone 4.

## REST API

The backend is a FlightPHP REST API in `backend/`. Every request goes through `backend/.htaccess` to `backend/index.php`, which registers the services and loads the route files. The code has three layers: routes (`backend/routes/`) read the request and answer with JSON, services (`backend/services/`) validate the data and apply the business rules, and DAOs (`backend/dao/`) run the SQL with PDO prepared statements.

Base URL: `http://localhost/<project-folder>/backend`

| Resource | Endpoints |
|---|---|
| Users | `GET /users` (`?role=`), `GET /users/{id}`, `GET /users/{id}/orders`, `POST /users`, `PUT /users/{id}`, `DELETE /users/{id}` |
| Categories | `GET /categories`, `GET /categories/{id}`, `GET /categories/{id}/products`, `POST /categories`, `PUT /categories/{id}`, `DELETE /categories/{id}` |
| Products | `GET /products` (`?category_id=`, `?search=`, `?pet_type=`, `?featured=`), `GET /products/{id}`, `POST /products`, `PUT /products/{id}`, `DELETE /products/{id}` |
| Orders | `GET /orders`, `GET /orders/{id}`, `POST /orders`, `PUT /orders/{id}`, `DELETE /orders/{id}` |
| Order items | `GET /order-items` (`?order_id=`), `GET /order-items/{id}`, `POST /order-items`, `PUT /order-items/{id}`, `DELETE /order-items/{id}` |
| Contacts | `GET /contacts` (`?user_id=`), `GET /contacts/{id}`, `POST /contacts`, `PUT /contacts/{id}`, `DELETE /contacts/{id}` |
| Breed facts (external API) | `GET /products/{id}/breed-facts` |

Every answer is JSON, also the errors (`{"error": "..."}`). Status codes: `200` OK, `201` created, `400` invalid data, `404` not found, `405` wrong method, `409` a business rule was broken (for example deleting a category that still has products, or ordering more than is in stock), `500` unexpected server error (the details go only to `backend/logs/error.log`), `503` the external API is not available. `401` and `403` are added with JWT authentication in Milestone 4.

Main business rules: prices and order totals always come from the database, never from the request; placing an order reduces the stock and cancelling it returns the stock; order status goes `pending` -> `shipped` -> `delivered`, and a pending or shipped order can be cancelled; only pending orders can have their items changed; only delivered or cancelled orders can be deleted; at most 4 products can be featured; emails and category names are unique; users with orders, categories with products and products that were ordered cannot be deleted. Until authentication exists (Milestone 4), `POST /orders` takes the `user_id` from the request body.

## API documentation

The API is documented with OpenAPI 3 (swagger-php attributes in `backend/public/v1/docs/`). Swagger UI: `http://localhost/<project-folder>/backend/public/v1/docs/`. The raw OpenAPI JSON is at `backend/public/v1/docs/swagger.php`. "Try it out" sends real requests, so a `POST` or `DELETE` there really changes the database.

## Live deployment

Deployment link will be added in Milestone 5.

## Project structure

```
Petly/
├── AGENTS.md
├── README.md
├── CLAUDE.md
├── docs/
│   ├── AI_LOG.md
│   └── ERD.png
├── .gitignore
├── server.php                (router for the PHP built-in server)
├── backend/
│   ├── config/
│   │   ├── config.example.php
│   │   └── config.php        (not in Git)
│   ├── .htaccess
│   ├── index.php
│   ├── composer.json
│   ├── composer.lock
│   ├── vendor/               (not in Git)
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
│   ├── services/
│   │   ├── BaseService.php
│   │   ├── UserService.php
│   │   ├── CategoryService.php
│   │   ├── ProductService.php
│   │   ├── OrderService.php
│   │   ├── OrderItemService.php
│   │   ├── ContactService.php
│   │   ├── BreedFactService.php
│   │   └── ExternalApiException.php
│   ├── routes/
│   │   ├── UserRoutes.php
│   │   ├── CategoryRoutes.php
│   │   ├── ProductRoutes.php
│   │   ├── OrderRoutes.php
│   │   ├── OrderItemRoutes.php
│   │   ├── ContactRoutes.php
│   │   └── BreedFactRoutes.php
│   ├── public/v1/docs/
│   │   ├── index.html        (Swagger UI)
│   │   ├── swagger.php       (generates the OpenAPI JSON)
│   │   ├── doc_setup.php     (API info and schemas)
│   │   └── paths/            (one *Paths.php class per route file)
│   ├── utils/
│   │   └── Logger.php
│   ├── middleware/
│   └── logs/                 (info.log, error.log; not in Git)
└── frontend/
    ├── index.html
    ├── css/style.css
    ├── js/
    ├── views/
    └── assets/
```

## Technology stack

PHP 8 (FlightPHP 3, PDO, swagger-php 6, Composer), MySQL, vanilla JavaScript, HTML, CSS, Bootstrap 5, Swagger UI.