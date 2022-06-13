Un agent compromis [1/3]

Nous avons surpris un de nos agents en train d'envoyer des fichiers confidentiels depuis son ordinateur dans nos locaux vers Hallebarde. Malheureusement, il a eu le temps de finir l'exfiltration et de supprimer les fichiers en question avant que nous l'arrêtions.

Heureusement, nous surveillons ce qu'il se passe sur notre réseau et nous avons donc une capture réseau de l'activité de son ordinateur. Retrouvez le fichier qu'il a téléchargé pour exfiltrer nos fichiers confidentiels.

=================================================================

On a une capture réseau à analyser.
Le 80e flux TCP dévoile un script python où est écrit le premier flag :

404CTF{t3l3ch4rg3m3n7_b1z4rr3}

=================================================================

Un agent compromis [2/3]

Maintenant, nous avons besoin de savoir quels fichiers il a exfiltré.

Format du flag : 404CTF{fichier1,fichier2,fichier3,...} Le nom des fichiers doit être mis par ordre alphabétique.

==================================================================

On reprend le code python du flux précédent dans exfiltration.py.

On comprend qu'il s'agit d'une exfiltration DNS. On va donc extraire les différentes requêtes DNS du fichier.
On remarque que les échanges anormaux sont envoyés à l'IP 192.168.122.1.
On les extrait grâce à tshark :

`$ tshark -r capture-reseau.pcapng  -Y "ip.dst==192.168.122.1" -T fields -e dns.qry.name`

On nettoie les données qui ne nous intéresse pas.\
En lisant le code, on comprend que le nom de chaque fichier est extrait dans une requête DNS, elle-même envoyée entre une requête à "never-gonna-give-you-up.hallebarde.404ctf.fr" et "626567696E.hallebarde.404ctf.fr"

Ensuite, le contenu des fichiers est envoyé (mais cela ne nous intéresse pas tout de suite).

Quand on analyse les données extraites, on trouve 4 requêtes de nom de fichiers :
```
666c61672e747874.hallebarde.404ctf.fr
68616c6c6562617264652e706e67.hallebarde.404ctf.fr
73757065722d7365637265742e706466.hallebarde.404ctf.fr
657866696c74726174696f6e2e7079.hallebarde.404ctf.fr
```
qui décodé de hex :

flag.txt\
hallebarde.png\
super-secret.pdf\
exfiltration.py

On a alors comme flag :

404CTF{exfiltration.py,flag.txt,hallebarde.png,super-secret.pdf}

=================================================================

Un agent compromis [3/3]

Il semblerait que l'agent compromis a effacé toutes les sauvegardes des fichiers qu'il a exfiltré. Récupérez le contenu des fichiers.

=================================================================

On veut probablement extraire les données de super-secret.pdf.
On récupère les requêtes DNS et avec un ctf+f, on enlève ".hallebarde.404ctf.fr"

On reconstruit le fichier... et c'est le drame.

Il est corrompu, il doit manquer des paquets.

Le tableau xref n'est pas bon : il devrait y avoir 14 entrées mais il n'y en a que 13 + un bout qui n'a pas de sens.
On reconstruit la ligne manquante.

Après je pense à utiliser binwalk. On obtient un fichier avec des octets qu'on déchiffre :

40CTF{DNS_3xf1ltrnhaebd}

Il manque des caractères... En observant bien, chaque caractère apparait une et une seule fois seulement : les caractères en plus ont disparus ! 
Avec un peu de guessing, on retrouve le flag :

404CTF{DNS_3xf1ltr4t10n_hallebarde}
