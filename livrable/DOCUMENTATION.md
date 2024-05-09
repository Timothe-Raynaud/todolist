# Implementation de l'authentification

Je fais ici une presentation de la gestion de l'authentification, permettant de savoir rapidement où chercher si une modification est nécessaire. Je ne présente pas tous les cas et pour faire des modifications, il sera nécessaire de se référencer aussi à la documentation de symfony :  `https://symfony.com/doc/current/security.html`

## Fichiers importants

- `config/package/security.yaml` : Gestion de la configuration de l'authentification
- `src/Controller/SecurityController.php` : Gestions des routes
- `templates/app/security/login` : Gestion vue de la page de log
- `src/Entity/User` : Gestion de l'entité utilisateur lié à la base de données via Doctrine

## Configuration

Le fichier de configuration permet de multiples choses telles que la gestion des droits d'accès, définir qui est l'utilisateur, définir les routes de connection et de déconnexion, la gestion du hasher de mot de passe ainsi que la stratégie de droit.
 
#### Explication de la configuration actuel :

- La strategy _unanimous_ définis que si l'on vérifie plusieurs conditions d'accès à un élement, toutes les conditions doivent être respectées pour y avoir accès. 
- Dans la section _provider_, je définis que mon entité `App\Entity\User` est l'utilisateur "doctrine". J'utilise cet utilisateur dans mon firewall _main_ afin que la plateforme utilise ma class comme référence. La valeur property cible la valeur de mon entité utilisée pour s'authentifier.
- Dans mon firewall, je définis qu'en environnement de __dev__, j'autorise l'accès aux routes du profiler ainsi qu'à mes assets. Ensuite, je définis mon provider comme expliqué plus tot ainsi que les routes d'accès aux différentes actions nécessaires à la connection, tel que logout, ou l'accès au formulaire de connection. Ces routes se trouvent dans notre __Controller__.
- Dans la section _access_control,_ je définis les droits à certaines routes. Dans notre cas je dis que toutes routes qui débute par `/login` peut être accessible depuis n'importe quel utilisateur non-authentifié. Ensuite, je dis que toutes les routes qui débutent par `/` peut être accéssible seulement si l'on a le `ROLE_ADMIN` ou le `ROLE_USER`. Attention, la définition de ces droits se fait dans l'ordre du haut en bas. Si je mets l'autorisation `/login` en dernière ligne cela sera toujours refusé avant d'essayer de lire cette autorisation si je ne suis pas authentifié. 
- La dernière section sert à customiser le hashage du mot de passe si on le souhaite. 