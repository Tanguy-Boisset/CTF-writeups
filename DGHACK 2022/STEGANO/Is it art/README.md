42 POINTS

## Description
Rendez-vous au stand DGA lors de l’European Cyber Week 2022 qui se tiendra à Rennes, un challenge se cache dans un des goodies qui vous seront offerts.

Saurez-vous le retrouver et le résoudre ?

## Solution

Comme image d'illustration, il y a un code barre orange. On le télécharge et à l'aide de Gimp, on transforme toutes les barres oranges en barres noires. On obtient alors un code barre lisible.

En le passant dans un lecteur de code barre en ligne, on obtient `ho le joli code barre`. Malheureusement, ce n'est pas le flag...

On récupère finalement le vrai goodies.

Il suffit d'extraire les deux codes barre de chaque côté du personnage (ils sont à la verticale). C'est un peu laborieux mais avec l'outil capture d'écran et powerpoint, j'obtiens deux codes barre qui réunit se décode en :

`4447417b2332327d`

Qui décodé depuis la base16 donne le flag :

**DGA{#22}**