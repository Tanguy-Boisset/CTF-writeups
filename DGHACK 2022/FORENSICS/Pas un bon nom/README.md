50 POINTS

## Description
J'étais là tranquillou sur mon PC, m'voyez ? Je télécharge des films et tout, m'voyez ? Et alors il y a ce message étrange que je dois payer Dogecoin pour déchiffrer mes données. Je ne l'ai pas fait... donc maintenant mes données sont chiffrées :( Donc tiens, prends le disque dur, c'est pas comme si il était utile maintenant... Sauf si c'était possible de déchiffrer les données et trouver mes données, m'voyez ? S'il te plaiiiit ? Tu serais adorable merci !

## Solution
On a un fichier `pc-jeanne.ova` à télécharger.

D'après le code : chiffrer en XOR avec une clé inconnue.

On trouve un fichier .swp qui est un fichier de sauvegarde vim.

On le restore avec la commande `vim -r 2021_Q1_report.txt`.

Puisque l'opération est un XOR, on peut retrouver la clé en refaisant un XOR entre le fichier chiffré et le fichier restoré.

Malheureusement, ça ne donne rien mais en gardant que la deuxième partie du fichier restoré, on trouve la clé en base64 :

`REdIQUNLezdIMTVfMVNfN0gzX0szWV9HMVYzTl83MF83SDNfR1RBX1ZfUjRONTBNVzRSM19WMUM3MU01fQo=`

Qui est aussi le flag du challenge !

**DGHACK{7H15_1S_7H3_K3Y_G1V3N_70_7H3_GTA_V_R4N50MW4R3_V1C71M5}**