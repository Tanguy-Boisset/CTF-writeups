Mise à jour requise

Notre service de renseignement nous a informé qu'un agent de Hallebarde avait une curieuse façon de gérer la sécurité de ses fichiers. Il semblerait qu'il s'agisse d'un fan inconditionnel de Python au point de l'utiliser pour gérer ses mots de passe! Nous avons réussi à intercepter une partie du code source qui gère la vérification du mot de passe maître.

Votre mission est de trouver ce mot de passe. Attention cependant, il semblerait que notre pythonesque ami ait utilisé des syntaxes spécifiques à Python3.10, j'espère que cela ne vous posera pas de problèmes!

Bonne chance à vous!

================================================================

On reverse juste la fonction c pour obtenir la contante f puis on passe à l'algo génétique ! Il marche ici car la fonction b a l'air d'être "continue" : une légère modification en entrée change peu la sortie.

Obtenu en ~3 min avec 3 threads en parralèles :

```
$ python3 script.py 2
    ...
    ...
    Nouveau score max : 341
    Nouveau score max : 350
    Nouveau score max : 357
    flag partiel : 404CTF{33RC1_PY0H0N3.10_P0UR_I3_M47CH}
    Nouveau score max : 363
    flag partiel : 404CTF{M3RC1_PY0H0N3.10_PEUR_L3_M47CH}
    Nouveau score max : 365
    flag partiel : 404CTF{M3RC1kPY0H0N3.10_P0UR_L3_M47CH}
    Nouveau score max : 370
    flag partiel : 404CTF{M3RC1_PYOH0N3.10_P0UR_L3_M47CH}
    Nouveau score max : 372
    flag partiel : 404CTF{M3RC1_PYUH0N3.10_P0UR_L3_M47CH}
    Nouveau score max : 380
    flag partiel : 404CTF{M3RC1_PY7H0N3.10_P0UR_L3_M47CH}
    ===============================================
    FLAG TROUVÉ : 404CTF{M3RC1_PY7H0N3.10_P0UR_L3_M47CH}
```
