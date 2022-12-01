50 POINTS

## Description
Le logo officiel du DG'hAck a été repris par un artiste de talent, mais pourquoi ?

## Solution
J'ai d'abord pensé à du LSB. J'ai donc extrait les lignes de pixels colorés avec `extract_colors.py`

Malheureusmeent, les analyses LSB n'ont rien donné.

J'ai alors pensé au langage PIET qui est un langage ésotérique de programmation basé sur les images.

On peut exécuter l'image fournie sur le site `http://www.bertnase.de/npiet/npiet-execute.php` mais cela ne marche pas.

En lisant la doc, on comprend qu'il faut noircir le logo du DGHACK pour que seuls les pixels en plus soient exécutés.

Avec la version `black.png`, on obtient le flag en exécutant l'image sur le site précédent :

**DGHACK{P13T_IS_S0_FUN}**