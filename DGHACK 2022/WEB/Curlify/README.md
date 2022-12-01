100 POINTS

## Description
Vous avez été contacté dans le cadre d'un programme de bug bounty pour auditer l'appli Curlify en preprod et valider sa robustesse avant son déploiement en Production.

Objectif: Lire le flag dans le fichier flag.php

La DSI nous indique que les administrateurs sont très réactifs dans le traitement des tickets.

## Solution

On a un form sur lequel on peut mettre une url à curl a priori. Sauf que quand on met une url, il y a écrit "Not implemented yet".

Dans les sources, on trouve en commentaire un lien vers une page dev.php. Lorsqu'on s'y rend, un message d'erreur apparait "Internal access only".

/index.php
/dev.php
/admin_panel
|   /index.php
|   /task.php
|   /create_task.php
|   /db.php
|   /config.php
|   /firewall.php
|   /prefs.php
/server-status

Taper : `localhost/index.php` marche et affiche le contenu de la page d'accueil.

Taper `localhost/dev.php` affiche le message : `Code zip bakfile saved on /536707b92`

On se rend à `http://localhost/536707b92/` via le Curlify, on a du directory listing. Avec cela, on peut télécharger le fichier `Code.zip.bak` à l'adresse `https://curlify3.chall.malicecyber.com/536707b92/Code.zip.bak`. On peut alors dezipper le fichier et récupérer les sources du site.

