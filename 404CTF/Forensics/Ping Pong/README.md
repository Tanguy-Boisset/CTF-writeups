Nous avons repéré une communication bizarre provenant des serveurs de Hallebarde. On soupçonne qu'ils en aient profité pour s'échanger des informations vitales. Pouvez-vous investiguer ?

=====================================================================

Quand on ouvre la capture, on remarque qu'il s'agit d'une communication cachée dans une charge icmp.

`$ tshark -r ping.pcapng -Y "ip.src == 10.1.0.10" -T fields -e data > raw_data.txt`

On récupère les octets. On les transforme en texte avec Cyberchef.

Mais il s'agit d'une fausse piste...

En fait on trouve que c'est la taille des data qui compte :\
On filtre pour n'avoir que les ping (et pas les pong) : ip.src == 10.1.0.10

En décimal : 52 48 52 67 84 70 123 85 110 95 112 49 110 103 95 112 48 110 103 95 112 52 115 95 115 105 95 49 110 110 48 99 51 110 116 125

Un coup de cyberchef :

404CTF{Un_p1ng_p0ng_p4s_si_1nn0c3nt}
