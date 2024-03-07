# Installation

_php 8.1_ | _Symfony 6.4_

Voici les differentes étapes d'installation du projet en local :
- Lancer la commande : Composer install
- Parameter .env.local pour qu'il ait access à une base de données.
- Lancer les commandes suivantes en acceptant avec 'yes' lorsque necessaire :
    - php bin/console doctrine:database:create
    - php bin/console doctrine:migrations:migrate
    - php bin/console doctrine:fixtures:load

# Livrable

À la racine du projet, vous trouverez un dossier 'livrable' comprenant les different élement demandé pour le livrable
- Un fichier symfonyInsight avec un lien vers la dernière analyse du projet.


## Amélioration

- Mise à jour symfony 3.3 vers 6.4
- Mise à jour php5.6 vers 8.1
- Mise à jour bootstrap v3 vers v5 (utilisation du CDN pour faciliter la migration)
- Ajout du form_theme bootstrap
