# Utilisation phpunit


Tout se passe au niveau du dossier où l'on travaille

``` 
docker run --rm --interactive --tty -v ${PWD}:/app -u $(id -u):$(id -g) composer init
```

``` 
docker run --rm --interactive --tty -v ${PWD}:/app -u $(id -u):$(id -g) composer install
```

```
docker run --rm --interactive --tty -v ${PWD}:/app -u $(id -u):$(id -g) -w /app moodlehq/moodle-php-apache:7.1 vendor/bin/phpunit
```
Si vous utilisez composer en locale, ou vous voulez profiter de la cache:
```
 docker run --rm --interactive --tty -v ${PWD}:/app \
 -v /home/nmoller/.composer:/root/composer \
 -w /app  prooph/composer:7.1 require php-http/guzzle6-adapter
```

Rouler les tests unitaires:

```
docker run --rm --interactive --tty -v ${PWD}:/app -u $(id -u):$(id -g) -w /app moodlehq/moodle-php-apache:7.1 php vendor/bin/phpunit  --bootstrap vendor/autoload.php tests/Basic/Test01.php
```

:warning: L'image composer utilise la dernière version de php (2018/10/05 7.2.10) donc, si vos dépendances vous restreint à utiliser d'autres versions de php considerez l'utilisation de `prooph/composer:7.0` ou  `prooph/composer:7.1`. Sinon, cherchez, il y a toujours dans `dockerhub` quelqu'un qui à créé l'image dont vous avez besoin.

On peut aussi faire :
```
docker run --rm --interactive --tty -v ${PWD}:/app \
-u $(id -u):$(id -g) -w /app \
--entrypoint /usr/local/bin/php  prooph/composer:7.1 bin/console make:migration
```
