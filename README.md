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

docker compose -f docker/docker-compose.yml build
docker compose -f docker/docker-compose.yml up --detach

# verify stability
docker container ls --format "table {{.Names}}\t{{.Status}}"
NAMES           STATUS
php             Up 2 minutes (healthy)
db              Up 2 minutes (healthy)
nginx           Up 2 minutes (healthy)
elasticsearch   Up 2 minutes (healthy)

docker exec php composer test-all
./composer.json is valid
PHPUnit 12.2.7 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.10 with Xdebug 3.4.5
Configuration: /var/www/phpunit.xml.dist

Time: 00:02.270, Memory: 28.00 MB

OK (16 tests, 34 assertions)

Generating code coverage report in HTML format ... done [00:00.006]
```
Generating test data:

```
docker exec -it php php -d memory_limit=2G bin/console doctrine:fixtures:load --append
```

Data volumes can be controlled via `tests/Fixtures/FixtureLimits.php`.
Note that it's fairly resource intensive (the statement above requires giving PHP 2GB of memory...)

This generates data volume as follows:

| Entity       | Count   |
|--------------|---------|
| institution  | 100     |
| department   | 990     |
| course       | 7515    |
| instructor   | 9739    |
| assignment   | 22719   |
| enrolment    | 118112  |
| student      | 29528   |

One can reindex the data in ElasticSearch by running:

```bash
docker exec php php bin/console search:reindex all
```

The command also takes individual entity names, e.g. `course`, `instructor`, etc:
```bash
docker exec php php bin/console search:reindex course
```

## Endpoints

Data-view UI can be entered via `http://localhost:8080/institutions`.
All entities can be viewed and searched there, via drilling down.

All entities can be edited, but only students can be added
(via the course detail page: `http://localhost:8080/courses/{id}/view`).

Searching can be tested via `http://localhost:8080/search`.


## Changes

0.1 - Baseline setup copied from php8-swarm, with Docker Swarm and Redis stuff removed
