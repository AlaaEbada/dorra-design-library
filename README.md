# Dorra — Design Library

## Phase 01: local environment

Verified on 2026-10-29: Laravel 12.70.2, PHP 8.2.12, Composer 2.7.9,
Node 24.13.0, npm 11.7.0, MariaDB 10.4.32 (XAMPP).
Installed Composer platform requirements pass. The assignment requests MySQL;
local development currently uses MariaDB through Laravel's mysql driver.
MySQL-specific behavior and performance have not been verified.

The existing application and database are already configured. Keep the existing
`.env` and application key. Run `php artisan migrate:status` to inspect migrations;
the three initial migrations were already applied. Do not reset the database.
Run `php artisan serve` from this directory to start local development.

For a separate fresh local setup, install the locked PHP dependencies with
`composer install`, copy `.env.example` to `.env` only if `.env` does not exist,
and replace the database placeholders with credentials for your own local sample
database. Create that empty sample database first, run `php artisan key:generate`
only for the new environment, then `php artisan migrate` and `php artisan serve`.
These fresh-setup instructions have not been executed against a second database.
Never commit `.env`. `.env.example` contains placeholders only.
Frontend installation/build and browser verification have not been run in Phase 01.

Phase 01 verification used an internal HTTP request returning 200 and confirmed
CSRF middleware is enabled. See `docs/specs/01-environment-setup.md` for evidence.
Authentication and library features belong to later phases.

## Phase 02: internal authentication

Open `/login` after starting the server. Only login/logout are exposed; there is
no public registration, password recovery or profile management.

Local sample credentials only (never use these accounts on a deployed service):

| Role | Email | Password |
|---|---|---|
| Admin | admin@dorra.test | DorraSample123! |
| Viewer | viewer@dorra.test | DorraSample123! |

Run `php artisan migrate`, then `php artisan db:seed --class=SampleAccountSeeder`
for the local sample accounts. The seeder is restricted to local/testing and
resets these two sample accounts to the documented roles/passwords on repeat runs.
Passwords are stored hashed. Existing unrelated users are retained and receive
the viewer default when the role migration runs.

Breeze 2.4.0 login controller and throttled Form Request are configured selectively
with Blade views; the full scaffolder was not run, avoiding unused auth routes
and frontend dependency replacements. The simple login shell needs no Vite build.
`LibraryPolicy` controls access; `/library` requires authentication and read
permission. Admin-only content uses the same policy in Blade. Design CRUD routes,
write Form Requests, model policies and action links will be connected when those
features are introduced in their assigned phases. Phase 02 tests use test-only
write endpoints to verify that the policy middleware returns 403 for Viewer.

Run `php artisan test` (SQLite in-memory, isolated from the local sample database).
Phase 02 result: 11 tests passed, 60 assertions. Browser verification NOT RUN.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
