# AI_LOG.md

Record of AI tool usage for this project, one section per milestone.

## Milestone 1

**Tool used:** Claude (Claude Code), used for the full Milestone 1 scope: setting up the project folder structure, building the static SPA frontend (all 13 pages), the admin featured-products feature, the checkout flow and design cleanup.

**Parts the AI worked on:**
- Project folder structure (`frontend/`, `backend/`, `.gitignore`) per the Milestone 1 requirement 1 wording.
- All static frontend pages (home, shop, product detail, cart, checkout, order confirmation, login, register, profile, about, contact, admin, 404) as a hash-routed single-page application.
- Visual design: color palette, typography, layout, and real product photography.
- The admin "featured products" toggle (localStorage-backed, max 4).
- Client-side checkout validation.
- Drafting this documentation set (`AGENTS.md`, `README.md`, `CLAUDE.md`, this file).

**Key prompts used:**
- "find a folder Pet Shop on desktop and there we will do everything" - set the working directory for the whole project.
- The pasted Milestone 1 requirement 1 text (project structure: frontend/backend folders, `.gitignore`).
- The pasted Milestone 1 requirement 2 text (static frontend, SPA, required pages), with the added instruction to keep the design realistic for a student project, avoid downloaded templates and heavy animation, and ask 5 clarifying questions first.
- Answers to those 5 questions: products-only shop, Customer and Shop Admin roles, dog/cat breed facts as the planned external API, a warm pastel theme, and a Bosnia-and-Herzegovina-only checkout with cash on delivery and no payment gateway.
- "ok just make sure that design isn't tipically AI BASED you can search for ui ux skill and try to use something there ... also you have to use real pictures and now it says when I open index.html file could not load this page please fix that" - led to using the UI/UX skill for layout guidance, replacing emoji with real photos, and an early fix for opening the page without a server.
- "make homepage more rich with for example more sections such as maybe with qoute or something like that, add a option to switch feature products through the admin dashboard so we can change maybe newer product or something like that(max 4 feature products) and add check of the street phone number email etc inside chechout process so we cannot order something without any data" - added the testimonials section, the admin featured-products toggle, and checkout validation.
- "maybe you should add one more section to the homepage ... from register form remove phone number cuz we dont need it and inside contact page where it says street franje ... change to Francuske revolucije bb, Sarajevo" - added the "How it works" section, removed the register phone field, fixed the store address.
- "also go through the code and remove all comments cuz we dont need anyhting ... also create and .md file only for you in case I open a new session you dont need to go again through the whole code base ... write it as detailed as you can and update it every time you change/add/delete something" - removed all code comments and created `CLAUDE.md`.
- "now lets remove this AI generated dashes - and replace it with -" - swept em dashes out of all page copy and docs.
- "tell me now why you use python and is there some document that says we should use python ... never used languages or techs that are not provided and marked as able to use inside project documentations so only php js mysql" - removed the Python build script and local server tooling, reverted the router to plain `fetch()`.
- "you write me a agents.md file and also a readme file" - created `AGENTS.md` and `README.md`.
- "ok write also that file and 2 cases where AI missed something mention no validations on field and placing emotes instead of images where i see these emotes and i tried checkout form and i see no validations" - created this file with the two cases below.

**Cases where the AI was wrong or produced poor code:**

1. **Emoji used instead of real images.** The AI's first version of the frontend used emoji characters (a bone, a fish, paw prints, and so on) as the hero image and as every product thumbnail, instead of real photography. This was noticed when reviewing the site and asking the AI to make the design look less generic and less obviously AI-generated. Fix: all emoji imagery was replaced with real stock photos (one per product, plus a hero photo and an about-page photo, sourced from Unsplash), and emoji used as icons elsewhere in the UI were replaced with Bootstrap Icons.

2. **No real validation on the checkout form.** The AI's first version of the checkout form had `required` attributes on its fields but also set `novalidate` on the `<form>` and never wrote any JavaScript to check the field values before submitting. This meant the checkout could be submitted with every field empty and would still silently create an order. Noticed by manually testing the checkout page and submitting it with blank fields. Fix: added client-side validation (name, phone, email, street, city, postal code) that shows an inline error under each invalid field and blocks the order from being placed until every field passes, with a note that server-side validation still needs to be added once the backend exists in Milestone 3, since client-side checks alone can be bypassed.

## Milestone 2

**Tool used:** Claude (Claude Code), used for the database schema, the sample data, the DAO layer and testing it against the local MySQL database.

**Parts the AI worked on:**
- Turning the draft ERD into the MySQL schema (`backend/database/petly.sql`): six tables with primary keys, foreign keys, `NOT NULL`, `UNIQUE` and `ENUM` constraints, and sample data that matches what the Milestone 1 frontend shows (the 9 products, 5 categories, the 3 orders from the admin table, test users and contact messages).
- The configuration class (`backend/config/config.php`, not in Git, and `config.example.php`).
- `BaseDao` with the shared CRUD methods and one DAO per table (`UserDao`, `CategoryDao`, `ProductDao`, `OrderDao`, `OrderItemDao`, `ContactDao`), following the structure of Reading 03 (DAO). `OrderDao::createOrder()` saves an order and its items in one transaction.
- A local test script (not committed) that runs create, read, update and delete for every DAO against the `Petly` database, plus checks for foreign keys, cascades, transaction rollback and SQL injection.
- Updating `README.md`, `AGENTS.md` and this file.

**Key prompts used:**
- The Milestone 2 task, given together with the project requirements and Readings 03 (DAO), 04 (Business Logic Layer and Services) and 05 (Presentation Layer): do only Milestone 2, follow the course structure and style, use only the allowed technologies, create the tables and data in the existing `Petly` database on `localhost` (user `root`, no password), do not delete existing tables or data without approval, and ask at least 5 questions before starting.
- Answers to the AI's questions: primary keys named `id` in every table (as in the reading), `contacts.user_id` optional because the Contact page is public, orders store the checkout fields in separate columns and always belong to a logged-in user, PHP installed through Homebrew only for local testing, realistic sample data taken from the frontend, test passwords chosen by the AI, the `.sql` file in `backend/database/`, and real AI mistakes written down in this log.

**Cases where the AI was wrong or produced poor code:**

1. **Broken letters č, ć, ž and š after importing the `.sql` file.** The first version of `petly.sql` did not say which character encoding it uses. When it was imported with the `mysql` command line client, the client used its default encoding (`latin1`), so names like "Karić" were stored as "KariÄ‡". The `mysql` terminal output still looked correct, because the client read the text back with the same wrong encoding, so this was not visible right away. It was noticed because the DAO test script checked that `getById(2)` returns the last name "Karić", and that check failed. Comparing the stored bytes with `HEX(last_name)` showed the text had been encoded twice. Fix: `SET NAMES utf8mb4;` was added as the first line of the `.sql` file so every import uses UTF-8, the affected rows in the database were repaired in place with an `UPDATE` (no tables were dropped), and the corrected file was imported into a separate temporary database to confirm the letters now arrive correctly.

2. **`update()` reports "nothing changed" as if the row did not exist.** The AI wrote `BaseDao::update()` exactly like the reading: it returns `rowCount() > 0`, and the reading says the service can turn `false` into a "not found" answer. But MySQL only counts rows whose values actually changed. Saving a category with the same name it already has (for example an admin opens the edit form and clicks Save without changing anything) returns `false` even though the row exists, so a Milestone 3 route built on that advice would answer 404 for a valid request. The AI's own test script did not catch it at first, because every update test changed a value. Noticed when reviewing what `rowCount()` really counts, and confirmed by calling `update(1, ['name' => 'Food'])` on the existing Food category, which returned `false`. Fix: the DAO was kept as in the reading (the PDO option that changes this behavior is named differently in older and newer PHP versions, which would make the code depend on the PHP version XAMPP ships), and the rule was written into `CLAUDE.md` for Milestone 3: a service must check that the row exists with `getById()` before calling `update()`, and decide 404 from that, not from the return value of `update()`.

## Milestone 3

**Tool used:** Claude (Claude Code), used for the service layer, the FlightPHP routes, the OpenAPI documentation with Swagger UI, the external API integration and for testing all of it against the local `Petly` database.

**Parts the AI worked on:**
- Installing Composer (Homebrew) and the libraries from the readings: `flightphp/core` and `zircote/swagger-php` in `backend/composer.json`. `vendor/` stays out of Git, `composer.json` and `composer.lock` are committed.
- `BaseService` and one service per entity (`UserService`, `CategoryService`, `ProductService`, `OrderService`, `OrderItemService`, `ContactService`) following Reading 04: allow-listed arrays, validation in the service, exceptions instead of HTTP codes, several DAOs through the constructor. Business rules: prices and totals from the database, stock reduced when an order is placed and returned when it is cancelled, allowed order status changes, at most 4 featured products, unique emails and category names, no deleting of rows that other rows still need.
- One route file per resource with all CRUD endpoints, `backend/index.php` as the front controller, `backend/.htaccess`, one central error handler and JSON answers for every error (Reading 05).
- OpenAPI documentation with PHP 8 attributes in `backend/public/v1/docs/` (`doc_setup.php`, one `*Paths.php` class per route file, `swagger.php`, Swagger UI in `index.html`), following Reading 06.
- `BreedFactService` and `GET /products/{id}/breed-facts`: calls TheDogAPI or TheCatAPI with cURL, a 5 second timeout and the API key from `config.php`, logs failures to `backend/logs/error.log` (small `Logger` class) and answers 503 with a friendly message when the API is not available.
- A new column `products.pet_type` (`dog`, `cat`, `both`), added to the live database and to `petly.sql`, so each product knows which breed facts to show.
- A local test script (not committed) with 224 checks over every endpoint: correct requests, invalid data, missing objects, business rules, stock changes, cascades, a simulated database failure (500 without details in the answer) and a simulated API timeout. The database was backed up before the tests and restored afterwards.
- Updating `README.md`, `AGENTS.md`, `CLAUDE.md` and this file.

**Key prompts used:**
- The Milestone 3 task, given with the project requirements and Readings 04 (Business Logic Layer and Services), 05 (Presentation Layer), 06 (OpenAPI and Swagger) and 07 (JWT Authentication and Middleware): do only Milestone 3, follow the readings strictly and use only their technologies, no comments in the code, ask questions before starting, work directly in the `Petly` database.
- Answers to the AI's questions: use TheDogAPI and TheCatAPI with free API keys kept in `config.php`, add a `pet_type` column to products, install Composer with Homebrew, and keep Milestone 3 backend only as long as everything can be tested (the frontend card is connected in Milestone 4).

**Cases where the AI was wrong or produced poor code:**

1. **Every validation error came back as an empty `200 OK`.** The AI wrote the central error handler exactly like Reading 05: `Flight::map('error', ...)` with `Flight::json([...], 400)` inside. With FlightPHP 3.19 this does not work: an exception stops `Flight::start()` before the response is sent, so `Flight::json()` only fills the buffer and nothing is ever sent. A request with a missing name, a duplicate email or too little stock got `200` with an empty body, and nothing was saved, so it looked like a silent success. Noticed when the AI's test script ran: 60 of the checks that expected 400 or 409 failed with status 200. Reading the FlightPHP source (`Engine::_error()` sends the response itself, `Engine::_json()` does not) showed why. Fix: the error handler now uses `Flight::jsonHalt()`, which sends the JSON and stops, the same function Reading 07 mentions for middleware. After the fix all 224 checks passed.

2. **`composer.lock` would not install on XAMPP.** The first `composer update` ran with the PHP 8.5 installed on this Mac, so Composer picked the newest Symfony 8 packages (needed by swagger-php), which require PHP 8.4. Everything worked locally, but `composer install` from the committed lock file would fail on XAMPP, which ships PHP 8.2, and on many hosting servers. Noticed when reading the installed package versions after the install. Fix: `"config": {"platform": {"php": "8.2.0"}}` was added to `composer.json`, so Composer resolves versions as if PHP 8.2 were installed. The lock file now has Symfony 7.4 packages that work from PHP 8.2 upwards, and the API was tested again.

3. **Breed fields guessed instead of checked.** The AI wrote `BreedFactService` from memory of older versions of TheDogAPI and TheCatAPI: it read the weight from `weight.metric`, expected the dog API to send the life span with the word "years" already in it, and used `bred_for` as a fallback description. The keys were not available yet, so the code could only be tested with failing calls. When the real keys were added, every answer had `"weight_kg": null`. Printing the raw API answers showed that the current APIs have no weight field at all, send the life span as plain numbers (`10-12`) for both dogs and cats, and leave `bred_for` empty. Fix: `weight_kg` was replaced with `breed_group` (for example "Terrier" or "Short-haired"), which is always filled, "years" is added to every life span, the `bred_for` fallback was removed, and the Swagger schema was changed to match. Lesson: an external API must be tested with real answers before the code that reads it is called done.
