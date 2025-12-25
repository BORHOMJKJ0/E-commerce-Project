# E-commerce Project (Laravel)

> Professional, modular RESTful E‑commerce backend built with **Laravel 10** — provides product management, categories, cart, orders, reviews, notifications, and API documentation.

---

## 🚀 Quick Overview

-   **Backend:** Laravel 10 (PHP 8.1+)
-   **Auth & API:** Sanctum + JWT support
-   **API Docs:** Swagger (darkaonline/l5-swagger)
-   **Queue / Jobs:** database queue + queued email jobs
-   **Storage:** Local disk (with support for AWS S3 config)
-   **Tests:** PHPUnit / Laravel Test utilities

## 🔧 Features

-   Product, Category, Offer, Warehouse, Order, Cart and Review management
-   User registration, email verification, password reset flows
-   Image upload and storage for products
-   Swagger-generated API documentation
-   Firebase integration for FCM push notifications
-   Jobs and queued email sending (verification, password reset)

## 🧰 Tech Stack

-   PHP 8.1+
-   Laravel 10
-   MySQL / PostgreSQL (configurable)
-   Vite + Axios (frontend assets)
-   Redis (optional cache/session)
-   l5-swagger (API docs)
-   tymon/jwt-auth and laravel/sanctum (auth)
-   kreait/laravel-firebase (Firebase)

## ✅ Getting Started (Local Development)

### Requirements

-   PHP 8.1+
-   Composer
-   Node 18+ and npm or pnpm
-   MySQL or PostgreSQL
-   Redis (optional)

### Installation

1. Clone the repo

    ```bash
    git clone https://github.com/<your-org>/E-commerce-Project.git
    cd E-commerce-Project
    ```

2. Install PHP dependencies

    ```bash
    composer install
    ```

3. Copy environment file and set values

    - macOS / Linux:
        ```bash
        cp .env.example .env
        ```
    - Windows (cmd):
        ```cmd
        copy .env.example .env
        ```

    Update `.env` with your DB, mail, and other credentials.

4. Generate app key & JWT secret

    ```bash
    php artisan key:generate
    php artisan jwt:secret
    ```

5. Install JS dependencies and build assets

    ```bash
    npm install
    npm run dev    # for development
    npm run build  # for production
    ```

6. Run migrations & seeders

    ```bash
    php artisan migrate --seed
    ```

7. Create storage symlink

    ```bash
    php artisan storage:link
    ```

8. Start the app

    ```bash
    php artisan serve
    ```

## 🔁 Queue & Background Jobs

By default the project uses the `database` queue connection (see `.env` -> `QUEUE_CONNECTION`).

Run a worker locally:

```bash
php artisan queue:work
```

## 📄 API Documentation (Swagger)

This project ships with l5-swagger. To generate or refresh the docs:

```bash
php artisan l5-swagger:generate
```

Then open: `http://localhost:8000/api/documentation` (default path - may vary if config changed)

## 🔐 Authentication

-   API authentication uses **Sanctum** for token-based auth and **JWT** for compatibility with mobile clients.
-   Run `php artisan jwt:secret` after installing to set `JWT_SECRET` in `.env`.

## ⚙️ Important Environment Variables

-   APP_NAME, APP_URL
-   DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
-   MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD
-   JWT_SECRET (generated via `php artisan jwt:secret`)
-   FIREBASE_CREDENTIALS / GOOGLE_APPLICATION_CREDENTIALS (if using Firebase SDK)
-   L5_SWAGGER_GENERATE_ALWAYS (for auto doc generation)

> Tip: Review `.env.example` for more default keys used by the app.

## 🧪 Tests & Quality

-   Run tests:

    ```bash
    php artisan test
    # or
    vendor/bin/phpunit
    ```

-   Code style / formatting with Laravel Pint:

    ```bash
    ./vendor/bin/pint
    ```

## 🧩 Useful Artisan Commands

-   `php artisan migrate` - run DB migrations
-   `php artisan db:seed` - run seeders
-   `php artisan storage:link` - create public storage symlink
-   `php artisan queue:work` - start queue worker
-   `php artisan l5-swagger:generate` - generate Swagger docs

## 🤝 Contributing

Contributions are welcome. Please:

1. Fork the repo
2. Create a feature branch
3. Open a pull request describing your change

Follow standard code style and add tests for new features or bug fixes.

## 📬 Contact

For questions, report issues, or feature requests, open an issue or contact the maintainers.

## 📄 License

This project is open-source and distributed under the **MIT License**.

---

> Created for internal use — customize sections above (Deploy, CI/CD, Docker, Secrets) to match your infrastructure and workflow.

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Cubet Techno Labs](https://cubettech.com)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[Many](https://www.many.co.uk)**
-   **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
-   **[DevSquad](https://devsquad.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[OP.GG](https://op.gg)**
-   **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
-   **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
