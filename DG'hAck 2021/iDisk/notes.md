# DG'hAck 2021 : iDisk - 150 pts

## Énoncé 

> Il y a quelque temps, l'entreprise ECORP a constaté une intrusion au sein de son système d'information. Les équipes d'administrateurs système ont remarqué que des sauvegardes de la base de données "pre_prod" ont été effectuées à plusieurs reprises (et sans accord au préalable) aux alentours de 00h chaque jour. Après une longue enquête policière, un suspect (ex-employé d'ECORP) a été interpelé avec un ordinateur. Toutefois, la police étant en sous-effectif, nous avons besoin de votre aide afin de mener une investigation numérique sur la machine saisie. Êtes-vous prêt à accepter cette mission ?

Avec cet énoncé, un fichier tar.gz de 12 GB (!!!) nous est fourni : il contient l'image disque `forensic.dd`.


## Analyse

On commence par analyser rapidement l'image fournie :

```
$ mmls forensic.dd

DOS Partition Table
Offset Sector: 0
Units are in 512-byte sectors

      Slot      Start        End          Length       Description
000:  Meta      0000000000   0000000000   0000000001   Primary Table (#0)
001:  -------   0000000000   0000002047   0000002048   Unallocated
002:  000:000   0000002048   0001126399   0001124352   NTFS / exFAT (0x07)
003:  000:001   0001126400   0041938943   0040812544   NTFS / exFAT (0x07)
004:  -------   0041938944   0041943039   0000004096   Unallocated
```

Les partitions sont au format NTFS, on peut donc en déduire qu'il s'agit de fichiers issus d'un ordinateur sous Windows.

La stratégie adoptée ensuite fut d'essayer de récupérer un maximum d'informations en utilisant `strings` et `grep` en utilisant des commandes de la forme :

`strings forensic.dd | grep RECHERCHE > RECHERCHE.txt`

On teste avec différentes valeurs pour RECHERCHE. La plus intéressante est "DGA{" en connaissant le format du flag mais cela n'a rien donné. Finalement, on obtient une première info intéressante en cherchant "pre_prod" qui nous est donné dans l'énoncé :

```
$ strings forensic.dd | grep pre_prod > pre_prod.txt

Invoke-Command -Session $s -ScriptBlock { Backup-SqlDatabase -ServerInstance "DESKTOP-GD806IG" -Database "pre_prod" -BackupFile "C:\Users\root\Documents\db_backup.bak" }
Invoke-Command -Session $s -ScriptBlock { Backup-SqlDatabase -ServerInstance "DESKTOP-GD806IG" -Database "pre_prod" -BackupFile "C:\Users\root\Documents\db_backup.bak" }
Invoke-Command -Session $s -ScriptBlock { Backup-SqlDatabase -ServerInstance "DESKTOP-GD806IG" -Database "pre_prod" -BackupFile "C:\Users\root\Documents\db_backup.bak" }
```

En plus d'incriminer l'ex-employé, ces lignes nous dévoilent que le fichier que l'on recherche s'appelle `db_backup.bak` !

Avant d'essayer de monter le disque, on effectue une dernière recherche avec "db_backup" (ici volontairement tronquée):

```
$ strings forensic.dd | grep db_backup > db_backup.txt

rm db_backup.zip.00*

Invoke-Command -Session $s -ScriptBlock { cd "C:\Program Files\OpenSSL-Win64\bin"; .\openssl.exe aes-256-cbc -in C:\Users\root\Documents\db_backup.bak -out C:\Users\root\Documents\db_backup.bak.enc -iv 48bb06a87601bcf63228f2e06dfe72b6 -K b017d674c1cea5f5c7409573b5bff6d3677e6e8bc06c095d01b0a75dc0ad5756 }

db_backup.zip.003
db_backup.zi0
db_backup.zip.001
```

On apprend plusieurs choses grâce à ces lignes : la base de données a été chiffrée avec l'algorithme aes-256-cbc. Cela aurait été problématique en temps normal, mais grâce à cette recherche, on a la clé `b017d674c1cea5f5c7409573b5bff6d3677e6e8bc06c095d01b0a75dc0ad5756` et le vecteur d'initialisation `48bb06a87601bcf63228f2e06dfe72b6` ! On pourra donc déchiffrer le fichier quand on l'aura récupéré.

On apprend également que le fichier a été placé dans plusieurs fichier zip, mais surtout, ceux-ci ont tous été supprimés. Ça se complique...

Heureusement, ce n'est pas parce qu'un fichier a été supprimé que ses données disparaissent du disque : tant que l'espace mémoire n'a pas été écrasé par d'autres données, il est possible de restorer ce fichier !

## Opération : récupérer le zip perdu

On commence en montant les partitions de l'image via la commande :

`sudo losetup --partscan --find --show forensic.dd`

L'image a été monté sur `/dev/loop16`. Or, seule la 2e partition nous intéresse car elle contient les données de l'utilisateur (cf la commande mmls). On va donc travailler sur `/dev/loop16p2`

Pour restorer les fichiers, on utilise la commande `ntfsundelete` (la partition étant une partition NTFS). On lance un scan des fichiers récupérables en sachant leur nom :

```
$ sudo ntfsundelete /dev/loop16p2 --force | grep db_backup

93694    FN..   100%  2021-10-22 11:06   1048576  db_backup.zip.001
94384    FN..   100%  2021-10-22 11:06   1048576  db_backup.zip.002
94387    FN..   100%  2021-10-22 11:06   1048576  db_backup.zip.003
94393    FN..   100%  2021-10-22 11:06    381112  db_backup.zip.004
98571    FN..   100%  2021-09-23 11:02   1048576  db_backup.zip.002
98574    FN..   100%  2021-09-23 11:02   1048576  db_backup.zip.003
98576    FN..   100%  2021-09-23 11:02    381112  db_backup.zip.004
```

Les fichiers sont normalement récupérables ! On lance alors la commande suivante pour les restorer :

`sudo ntfsundelete -u -m 'db_backup.zip*' /dev/loop16p2 --force`

C'est un succès ! On regroupe les fichiers dans un seul zip puis on le unzip :

```
$ cat db_backup.zip* > zipFinal.zip
$ unzip zipFinal.zip
```

On récupère alors bien le fichier `db_backup.bak.enc`. Il faut maintenant le déchiffrer, heureusement on connait la clé et le vecteur d'initialisation utilisés :

`openssl enc -aes-256-cbc -nosalt -d -in db_backup.bak.enc -out madb.bak -K 'b017d674c1cea5f5c7409573b5bff6d3677e6e8bc06c095d01b0a75dc0ad5756' -iv '48bb06a87601bcf63228f2e06dfe72b6'`

On a récupéré la base donnée volée d'ECORP. Le flag doit sûrement se trouver dedans. On va au plus simple :

```
$ strings madb.bak | grep DGA
DGA{95ecd8f47dc647599e9d1f7a90974a997338cd48}}
```

> Le flag est : DGA{95ecd8f47dc647599e9d1f7a90974a997338cd48}
