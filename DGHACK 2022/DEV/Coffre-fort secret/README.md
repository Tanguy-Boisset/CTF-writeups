50 POINTS

## Description
Le coffre-fort secret ne semble pas déchiffrer correctement. Corrigez-le pour obtenir des points. Il y a peut-être d'autres problèmes, qui sait !

## Solution

### Première erreur :
Quand on lance le programme, on obtient l'erreur :
```
# command-line-arguments
./SecretVault.go:85:45: cannot use '\n' (untyped rune constant 10) as string value in argument to strings.Replace
```

Il faut remplacer `'\n'` par `"\n"` pour que le caractère soit correctement interprété.

### Deuxième erreur :
Il faut rajouter un else dans la fonction main.

### Troisième erreur :
Dans la fonction Decrypt, la condition doit être `err != nil`.

### Quatrième erreur :
Dans la fonction de XOR, la variable de destination et celle de source sont inversées, on doit remplacer par : `cfb.XORKeyStream(plainText, cipherText)`