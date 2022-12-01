100 POINTS

## Description
Notre serveur a été piraté. C'est une évidence.

Ils dévoilent notre code source sans arrêt, dès que nous le mettons à jour.

Vous devez trouver l'origine de cette backdoor dès que possible.

## Solution
L'énoncé indique que la backdoor est lancée dès que le serveur est mis à jour.

Présence de fichiers pour un serveur web : pas de backdoor dedans.

Dans le fichier `/etc/systemd/system/systembd.service`, on trouve une référence à une backdoor :

```
[Unit]
Description=backdoor
After=network.target

[Service]
User=root
Type=simple
ExecStart=/usr/sbin/groupdel start_backdoor
Restart=on-failure
RestartSec=10s

[Install]
WantedBy=multi-user.target
```

Le fichier `/usr/sbin/groupdel` permettrait donc de lancer une backdoor. Quand on l'exécute avec l'argument `start_backdoor`, on obtient le message "Program running as intended."

Ce fichier utilise `libsysd.so` pour fonctionner, or ce fichier n'existe pas habituellement. On l'ouvre avec Ghidra.

Dans les strings, on trouve : `REdIQUNLe1N5c3RlbURJc0FGcmVuY2hFeHByZXNzaW9uQWJvdXRMaXZpbmdPdXRPZlJlc291cmNlZnVsbmVzc1dpdGhMaXR0bGVNb25leX0K`

Qui est en fait le flag en base64 !

**DGHACK{SystemDIsAFrenchExpressionAboutLivingOutOfResourcefulnessWithLittleMoney}**