Pour la suite de cette investigation, on vous donne accès à un dump mémoire d'une de nos stations qui a été compromise. Vous devez trouver la source de l'infection ! Aussi, il semblerait que le hackeur ait consulté des ressources depuis cette machine. Savoir quelles sont les techniques sur lesquelles il s'est renseigné nous aiderait beaucoup, alors retrouvez cette information !

Vous devez retrouver :

une adresse IP distante qui a été contactée pour transmettre des données\
un numéro de port de la machine compromise qui a servi à échanger des données\
le nom d'un binaire malveillant\
un lien web correspondant à la ressource consultée par l'attaquant\
Le flag est sous ce format : 404CTF{ip:port:binaire:lien} Par exemple, 404CTF{127.0.0.1:80:bash:https://google.fr} est un format de flag valide.

===============================================================================

grep avec regex pour récupérer les ip :

`$ strings dumpmem.raw | grep -E "^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$" > ip_found.txt`

On supprime les doublons :

`$ cat ip_found.txt | sort | uniq > ips.txt`

On fait des grep avec les différentes ip :

`$ strings dumpmem.raw | grep -F "192.168.61.137" -C 5 > rslt_grep.txt`

```
01;32msuperadmin@EVIL-SERV-81
[00m:
[01;34m~
[00m$ ./JeNeSuisPasDuToutUnFichierMalveillant 
Listening on [0.0.0.0] (family 0, port 13598)
Connection from 192.168.61.137 38088 received!
eh7s
mklost+found
lpmove
setvesablank
pwck
```

Avec ça, on récupère l'IP, le port et le nom du fichier.

Pour trouver le site :

`$ strings dumpmem.raw | grep -F "superadmin" -A 50 | grep -F "http" > rslt_grep.txt`

On trouve https://www.youtube.com/watch?v=3Kq1MIfTWCE qui est un tuto de pentest

Finalement, on obtient :

IP : 192.168.61.137\
Port : 13598\
Binaire : JeNeSuisPasDuToutUnFichierMalveillant\
Lien Web : https://www.youtube.com/watch?v=3Kq1MIfTWCE

404CTF{192.168.61.137:13598:JeNeSuisPasDuToutUnFichierMalveillant:https://www.youtube.com/watch?v=3Kq1MIfTWCE}
