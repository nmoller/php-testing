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

`composer.json`:
```
{
    "autoload": {
        "psr-4": {
            "Nmoller\\": "src/"
        }
    },
    "require-dev": {
        "bruli/php-git-hooks": "^5.7"
    },
    "config": {
        "bin-dir": "bin"
    },
    "scripts": {
        "post-install-cmd": [
            "PhpGitHooks\\Infrastructure\\Composer\\ConfiguratorScript::buildConfig"
        ],
        "post-update-cmd": [
            "PhpGitHooks\\Infrastructure\\Composer\\ConfiguratorScript::buildConfig"
        ]
    }
}
```
Comme on veut utiliser des containers ... on a à modifier les fichiers 
` .git\hooks`.

:red_circle: Le contrôle le plus simple `php -l` ou `phplint` posse des problèmes; le pre-commmit devient un peu plus compliqué pour en tenir compte.

On copie le contenu original de `pre-commit` dans `pre-commit.php` et le contenu de `pre-commit` devient:
```
#!/usr/bin/env bash

# Comme phplint de git-hooks ne fonctionne pas dans la version containers
# c'est fait à la main car c'est le check le plus simple à avoir à la main.


# Voir si c'est le premier commit ou pas
if git rev-parse --verify HEAD >/dev/null 2>&1
then
    against=HEAD
else
    # Initial commit: diff against an empty tree object
    against=4b825dc642cb6eb9a060e54bf8d69288fbee4904
fi

# Une seule place à modifier si autre version php 
CMD="docker run --rm  -v ${PWD}:/app -u $(id -u):$(id -g) -e COMPOSER_HOME=/app/composer -w /app --entrypoint=php prooph/composer:7.1"


# Identifier les fichiers qui ont changé
FILES=$(git diff  --cached --name-only $againt --)

for f in ${FILES}
do
    lint=$( ${CMD} -l $f)
    if [[ ! $lint =~ "No syntax errors" ]]; then
        echo $lint
        exit 1
    fi
done

${CMD} .git/hooks/pre-commit.php
```

:tada: On n'a plus d'excuse pour dire qu'on n'a pas la bonne version de php.

Si vous faites un commit avec la version originelle dans un ordi avec `PHP 5.6`
... on peut recevoir des messages du genre:
```
PHP Parse error:  syntax error, unexpected 'function' (T_FUNCTION), 
expecting identifier (T_STRING) or \\ (T_NS_SEPARATOR) in ...
```

Pour corriger un problème:
```
docker run --rm --interactive --tty -v ${PWD}:/app \
-u $(id -u):$(id -g) -e COMPOSER_HOME=/app/composer \
 -w /app --entrypoint=php prooph/composer:7.1 \
 bin/phpcbf src/Base/Command.php --standard=PSR2

 PHPCBF RESULT SUMMARY
 ----------------------------------------------------------------------
 FILE                                                  FIXED  REMAINING
 ----------------------------------------------------------------------
 /app/src/Base/Command.php                             3      0
 ----------------------------------------------------------------------
 A TOTAL OF 3 ERRORS WERE FIXED IN 1 FILE
 ----------------------------------------------------------------------

 Time: 37ms; Memory: 6Mb

 git add src/Base/Command.php
 git commit -m "Fichier réparé"
 ```
Ajouter le fichier `PmdRules.xml` à la racine et:

 ```
  git commit -m "Test.."
 Pre-Commit tool
 Running PHPLINT...................................0K
 Checking code style with PHPCS....................0K
 Checking PSR2 code style with PHP-CS-FIXER........0K
 Checking code mess with PHPMD.....................0K

                  @@@@@@@@@@@@@@@
      @@@@      @@@@@@@@@@@@@@@@@@@
     @    @   @@@@@@@@@@@@@@@@@@@@@@@
     @    @  @@@@@@@@   @@@   @@@@@@@@@
     @   @  @@@@@@@@@   @@@   @@@@@@@@@@
     @  @   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    @@@@@@@@@ @@@@@@@@@@@@@@@@@@@@@@@@@@@@
   @         @ @@  @@@@@@@@@@@@@  @@@@@@@@
  @@         @ @@@  @@@@@@@@@@@  @@@@@@@@@
 @@   @@@@@@@@ @@@@  @@@@@@@@@  @@@@@@@@@@
 @            @ @@@@           @@@@@@@@@@
 @@           @ @@@@@@@@@@@@@@@@@@@@@@@@
  @   @@@@@@@@@ @@@@@@@@@@@@@@@@@@@@@@@
  @@         @ @@@@@@@@@@@@@@@@@@@@@@
   @@@@@@@@@@   @@@@@@@@@@@@@@@@@@@
                  @@@@@@@@@@@@@@@
         

                HEY, GOOD JOB!!       
 [master cad3ed3] Test..
  1 file changed, 2 insertions(+), 3 deletions(-)
 ```