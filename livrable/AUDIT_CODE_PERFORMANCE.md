# Audit de qualité de code

Nous utiliserons `Symfony insight`. Pour cela, il faut connecter le repository à la plateforme en le configurant sur un projet symfony. À chaque commit le code sera automatiquement annalysé et, en cas de manquement sur la qualité du code, retournera des erreurs qu'il faudra corriger. Le but est de garder un code qui ne génère aucune erreur chez symfony insight pour préserver la qualité du code. 

# Audit de performance de code

- Nous utilisons l'onglet performance du profiler de symfony.
- Pour chaque page du site, il faut respecter les conditions suivantes :
    - Total execution time (Maximum) : 500ms
    - Symfony initialization (Maximum): 100ms
    - Peak memory usage (Maximum): 200mib
- Ces valeurs prennent en compte l'idée que la plateforme peut évoluer et devenir plus importante. Avec ce que la plateforme propose dans l'immédiat, nous devons nous trouver bien en dessous de ces valeurs.   
- Il est important de mettre en place des pratiques telles qu'indéxés les élements utils sur la base de donnée ou créer des requetes qui restreignent au mieux le nombre de recherches pour rester sur de bonnes bases

### Valeurs actuelles vs anciennes :

| Version                |  Old Version  |  New version  |
|:-----------------------|:-------------:|:-------------:|
|                        |  Page login   |  Page login   |
| Total execution time   |     59ms      |     36ms      |
| Symfony initialization |      7ms      |      7ms      |
| Peak memory usage      |     14mib     |     14mib     |
|                        |   Page Home   |   Page Home   |
| Total execution time   |     74ms      |     23ms      |
| Symfony initialization |     10ms      |      4ms      |
| Peak memory usage      |     4mib      |     4mib      |
|                        | Page TaskList | Page TaskList |
| Total execution time   |     60ms      |     34ms      |
| Symfony initialization |     10ms      |      8ms      |
| Peak memory usage      |     4mib      |     4mib      |
