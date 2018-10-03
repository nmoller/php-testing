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


