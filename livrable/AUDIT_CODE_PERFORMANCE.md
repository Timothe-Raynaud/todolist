# Audit de qualité de code

Nous utiliserons `Symfony insight`. Pour cela, il faut connecter le repository à la plateforme en le configurant sur un projet symfony. À chaque commit le code sera automatiquement annalysé et, en cas de manquement sur la qualité du code, retournera des erreurs qu'il faudra corriger. Le but est de garder un code qui ne génère aucune erreur chez symfony insight pour préserver la qualité du code. 

# Audit de performance de code

- Nous utilisons l'onglet performance du profiler de symfony.
- Pour chaque page du site, il faut respecter les conditions suivantes :
    - Total execution time (Maximum) : 1000ms
    - Symfony initialization (Maximum): 100ms
    - Peak memory usage (Maximum): 200mo
- Ces valeurs prennent en compte l'idée que la plateforme peut évoluer et devenir plus importante. Avec ce que la plateforme propose dans l'immédiat, nous devons nous trouver bien en dessous de ces valeurs.   
