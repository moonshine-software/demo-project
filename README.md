## Installation

### Docker
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
- Add .env and configure
- php artisan key:generate
- php artisan storage:link
- php artisan migrate --seed
- Go to /admin
- Optionally, you can configure github or tinymce file manager