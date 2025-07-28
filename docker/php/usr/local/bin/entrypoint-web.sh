#!/bin/bash

rm -f /var/www/vendor/up.dat
cp -a /tmp/composer/vendor/. /var/www/vendor/
touch /var/www/vendor/up.dat

exec php-fpm
