Je suis une théière

Le travail que vous avez fourni jusqu'à maintenant est remarquable, surtout pour une nouvelle recrue ! Une pause café ne vous ferait pas de mal. Je ne vous propose pas de thé : j'ai égaré ma théière par erreur sur un site web... Si jamais vous la retrouvez, faites-moi chauffer de l'eau et je vous récompenserai, foi de CSS117 !

Format du flag : 404CTF{chaîne de caractères trouvée en leet}

=============================================================

La première étape est de trouver le challenge. L'url https://theiere.404ctf.fr/ nous donne une page de challenge !\
Retrospectivement, n'importe quel préfixe fonctionne.

On a un mini-jeu :

La technique consiste à partir de la fin pour refaire le chemin. à chaque étape, des symboles s'affichent qu'on note :

NDE4X0FfbTR6ZV9pbmdfVGU0UDB0
qui décodé en base64 donne : 418_A_m4ze_ing_Te4P0t

404CTF{418_A_m4ze_ing_Te4P0t}
