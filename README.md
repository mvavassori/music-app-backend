# Music App Backend

A simple application to get familiar with making applications using PHP following OOP principles.

## Container creation

MySQL database container:

```
podman run -d \
  --name mysql-music-app-db \
  --network music-app-network \
  -e MYSQL_ROOT_PASSWORD=rootpassword \
  -e MYSQL_DATABASE=musicappdb \
  -e MYSQL_USER=appuser \
  -e MYSQL_PASSWORD=apppassword \
  -p 3306:3306 \
  mysql:lts
```

PHP-Apache backend:

```
podman run -d \
  --name music-app-backend \
  --network music-app-network \
  -p 8080:80 \
  -v .:/app \
  webdevops/php-apache:8.1-alpine
```

Initialize the database tables by executing the tables.sql in the MySQL container:

```
podman exec -i mysql-music-app-db mysql -u root -prootpassword musicappdb < tables.sql
```
