## Installation

### Docker
 - Create a .env file and copy the values from .env.example,
 - Change DOCKER_USER value,
 - Configure the ports and database to work (optional).

The demo can be run to docker-compose. Use:
```
make demo-install
```
And go to http://localhost or http://localhost/admin

**Settings.**

Before you run "make demo-install", you can configure the ports and database to work.

In .env.example replace
```
DOCKER_USER=laravel #OS user
LOCAL_WEB_PORT=80
LOCAL_MYSQL_PORT=3306
LOCAL_REDIS_PORT=6379
```

The database setup is located in docker/volumes/MySQL/dump
Default:
```
DB_DATABASE=moonshine_demo
DB_USERNAME=moonshine_demo
DB_PASSWORD=12345
```

### Manually
- Add database
- Run composer install
- Run npm install && vite build
- Add .env and configure
- php artisan key:generate
- php artisan storage:link
- php artisan migrate --seed
- Go to /admin
- Optionally, you can configure github or tinymce file manager

## End-to-end tests

This branch runs Laravel 13 and MoonShine 5.x-dev.

Requires PHP 8.4 (with PDO SQLite), Node.js 22.12+ and Composer dependencies.

```sh
composer install
php artisan vendor:publish --tag=laravel-assets --force
npm ci
npx playwright install chromium
npm run test:e2e
```

Playwright builds the frontend, seeds a temporary SQLite database and starts its own
Laravel server at `http://127.0.0.1:8005`. The database configured in `.env` is not used.
Keep port 8005 free and stop Vite before running the tests (`public/hot` must be absent).

The suite checks public pages, authentication, the dashboard and its asynchronous tabs,
the profile, and every registered resource page (lists, create/edit forms and details,
including resources normally opened in modals). It fails on JavaScript errors, console
errors and unsuccessful local requests. Screenshots and traces are saved in
`test-results/` on failure. Browser workflows also cover user and category CRUD,
profile/settings persistence, XLSX import/export and table/card switching. CSRF protection remains enabled.
The database and generated storage files are removed after the run.

`php artisan test` additionally checks CRUD for every resource, validation, settings
and access restrictions using an in-memory SQLite database.

GitHub Actions runs the same checks on pushes to `5.0` and pull requests.
