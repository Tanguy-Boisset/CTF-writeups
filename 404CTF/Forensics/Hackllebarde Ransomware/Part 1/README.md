Alerte, nous sommes attaqués ! Un ransomware vient de frapper l'infrastructure de notre centre de commandement ! Hors de question de payer la rançon.
Comme vous êtes notre meilleur élément, nous vous chargeons de la gestion de cette crise. Nous pourrions faire une simple réinstallation avec nos sauvegardes, mais nous souhaitons récupérer avant tout le plus d'informations. Avant de nous intéresser à l'origine de ce malware, nous devons trouver si des informations confidentielles ont été exfiltrées afin de prendre les mesures appropriées. Nous avons pu enregistrer un trafic réseau suspect, mais impossible de savoir ce qu'il contient. Jetez-y un oeil !

=======================================================================

Pas de données TCP mais on remarque que les flags ont des valeurs étranges.
On les extrait :

`$ tshark -r ransomware1.pcapng  -T fields -e tcp.flags > extracted.txt`

Quand on les mets sur cyberchef, on voit qu'il s'agit d'un fichier pdf mais il faut filtrer certains de ces caractères.

On remarque que les caractères intéressants sont ceux envoyés par 172.17.0.1, on les récupère donc :

`$ tshark -r ransomware1.pcapng  -Y "ip.src==172.17.0.1" -T fields -e tcp.flags > extracted.txt`

On ouvre le fichier et à la fin on trouve le flag :

404CTF{L3s_fL4gS_TCP_Pr1S_3n_fL4G}
