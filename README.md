# Installation

_php 8.1_ | _Symfony 6.4_

Voici les differentes étapes d'installation du projet en local :
- Configurer un .env.local pour qu'il ait access à une base de données.
- Lancer la commande : Composer install


- Il y a ensuite deux possibilités : 
  - soit c'est pour un __UPDATE__ de la version précédente dans quel cas faire ce qui suit :
    - `php bin/console doctrine:migrations:execute --up 'DoctrineMigrations\\Version20240419082748'`
    
  - Soit c'est une __PREMIERE INSTALLATION__ dans quel cas faire ceci :
      - `php bin/console doctrine:database:create`
      - `php bin/console doctrine:migrations:diff`
      - `php bin/console doctrine:migrations:execute --up 'DoctrineMigrations\\Version$$$$$$$'` (remplacer les dollars par le nom de la version qui vient d'étre créer.)

# Livrable

À la racine du projet, vous trouverez un dossier 'livrable' comprenant les different élement demandé pour le livrable
- Un fichier symfonyInsight avec un lien vers la dernière analyse du projet.


## Test

Pour lancer le test coverage l faut faire la commande suivante :
`vendor/bin/phpunit --coverage-html public/test-coverage`
Si les tests se sont bien effectué un dossier __test-coverage__ sera créé dans le dossier public avec un __index.html__ permettant d'accéder à une interface pour voir tout ce qui est testé ou ne l'est pas.  
