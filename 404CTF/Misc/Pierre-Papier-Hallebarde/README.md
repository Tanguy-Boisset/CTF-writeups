Hallebarde a mis en place sa variante du Pierre-papier-ciseaux. À ce jour, personne de nos services n'est parvenu à vaincre l'ordinateur. Montrez-leur de quoi vous êtes capable en récupérant leur précieux flag.txt !


nc challenge.404ctf.fr 30806

Indice : J'ai à peine eu le temps d'installer Python !

=================================================================================

Je pense pas avoir trouvé la solution "normale". On utilise le input pour extraire le flag en binaire et on récupère bit à bit grâce à la sortie du jeu :

`'2'.join(format(ord(x), 'b') for x in open("flag.txt", "r").readline())[i]`

404CTF{cH0iX_nUm3r0_4_v1c701r3}
