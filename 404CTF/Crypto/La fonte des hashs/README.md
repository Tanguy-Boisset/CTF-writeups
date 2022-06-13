La fonte des hashs
998
Nos experts ont réussi à intercepter un message de Hallebarde : 18f2048f7d4de5caabd2d0a3d23f4015af8033d46736a2e2d747b777a4d4d205

Malheureusement il est haché ! L'équipe de rétro-ingénierie vous a laissé cette note :

Voici l'algorithme de hachage. Impossible de remonter le haché mais vous, vous trouverez peut être autre chose. Voici comment lancer le hachage : python3 hash.py

PS : Les conversations interceptées parlaient d'algorithme "très frileux" ...

================================================================================

Pas incroyable ce chall, le hash est facilement cassable car chaque octet dépend uniquement de celui en entrée à la même place...
On peut donc bruteforce chaque caractère à la main et on obtient :

404CTF{yJ7dhDm35pLoJcbQkUygIJ}
