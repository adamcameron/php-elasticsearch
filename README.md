# php-elasticsearch
Experimenting with ElasticSearch integration into a Symfony app

## Notes

One must create some files containing env var values that we don't want
in source control due to the sensitive nature of the values.

* `docker/mariadb/mariadb_password_file.private`
* `docker/mariadb/mariadb_root_password_file.private`
* `docker/php/appEnvVars.private`

The MariaDB ones should contain only the password for the DB user the app users;
and the root user used to create the DB, respectively.

`appEnvVars.private` should have a `[name]=[value]` pair for the following env vars:

```bash
APP_SECRET=[the value of the APP_SECRET env var]
```
The value doesn't really matter.


## Building for dev

```bash
# from the root of the project

# only need to do this once or if Dockerfile.base changes
docker build \
  -f docker/php/Dockerfile.base \
  -t adamcameron/php-elasticsearch-base:x.y \ # where x.y is the actual version, e.g. 3.0 \
  -t adamcameron/php-elasticsearch-base:latest \
  .
  
docker push adamcameron/php-elasticsearch-base:x.y 
docker push adamcameron/php-elasticsearch-base:latest  

docker compose -f docker/docker-compose.yml build
docker compose -f docker/docker-compose.yml up --detach

# verify stability
docker container ls --format "table {{.Names}}\t{{.Status}}"
NAMES     STATUS
php       Up 15 seconds (healthy)
nginx     Up 15 seconds (healthy)
db        Up 15 seconds (healthy)

docker exec php composer test-all
./composer.json is valid
PHPUnit 12.2.7 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.10 with Xdebug 3.4.4
Configuration: /var/www/phpunit.xml.dist

Time: 00:02.270, Memory: 28.00 MB

OK (10 tests, 25 assertions)

Generating code coverage report in HTML format ... done [00:00.006]
```

## Building PHP container for prod

This presupposes appropriate Nginx and DB servers are already running
(the dev containers would be fine).

```bash
# from the root of the project

# rebuild base image if it's changed (see above)

# this is for the prod container
docker build \
    -f docker/php/Dockerfile.prod \
    -t adamcameron/php-elasticsearch:x.y \ # where x.y is the actual version, e.g. 0.6 \
    -t adamcameron/php-elasticsearch:latest \
    .

docker push adamcameron/php-elasticsearch:x.y
docker push adamcameron/php-elasticsearch:latest
```

## Changes

0.1 - Baseline setup copied from php8-swarm, with Docker Swarm and Redis stuff removed
