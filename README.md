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

:v: Il est conseillé d'utiliser la variable `COMPOSER_HOME` pour avoir une bonne gestion des caches composer... il ne fait pas mal de le mettre dans `.gitignore`
```
docker run --rm --interactive --tty -v ${PWD}:/app \
-u $(id -u):$(id -g) \
-e COMPOSER_HOME=/app/composer \
-w /app prooph/composer:7.1 update --no-dev
```

# On se rend plus loin

Supposons que l'on veut utiliser `bruli/php-git-hooks`.

[bruli/php-git-hooks](https://packagist.org/packages/bruli/php-git-hooks)

Comme on veut utiliser des containers ... on a à modifier les fichiers 
` .git\hooks`.

On copie le contenu original de `pre-commit` dans `pre-commit.php` et le contenu de `pre-commit` devient:
```
#!/usr/bin/env bash

docker run --rm --interactive -v ${PWD}:/app -u $(id -u):$(id -g) \
-e COMPOSER_HOME=/app/composer -w /app  --entrypoint=php prooph/composer:7.1 .git/hooks/pre-commit.php
```
:tada: On n'a plus d'excuse pour dire qu'on n'a pas la bonne version de php.

Si vous faites un commit avec la version originelle dans un ordi avec `PHP 5.6`
... on peut recevoir des messages du genre:
```
PHP Parse error:  syntax error, unexpected 'function' (T_FUNCTION), 
expecting identifier (T_STRING) or \\ (T_NS_SEPARATOR) in ...
```