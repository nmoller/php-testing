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

Rouler les tests unitaires:

```
docker run --rm --interactive --tty -v ${PWD}:/app -u $(id -u):$(id -g) -w /app moodlehq/moodle-php-apache:7.1 php vendor/bin/phpunit  --bootstrap vendor/autoload.php tests/Basic/Test01.php
```

:warning: L'image composer utilise la dernière version de php (2018/10/05 7.2.10) donc, si vos dépendances vous restreint à utiliser d'autres versions de php considerez l'utilisation de `prooph/composer:7.0` ou  `prooph/composer:7.1`. Sinon, cherchez, il y a toujours dans `dockerhub` quelqu'un qui à créé l'image dont vous avez besoin.