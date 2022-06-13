On remarque dans l'image un chunk sTeG :

```
$ ./steg-png inspect ../../stage2.png 
png file summary:
stage2.png  100644 497701 d2b0c87422c4b4237d22d85150a80943
chunks: IHDR (1), sTeG (1), IDAT (14), IEND (1)

Showing all chunks:

chunk type: IHDR
file offset: 8
data length: 13
cyclic redundancy check: 3176001464 (network byte order 0xb8ef4dbd)

chunk type: sTeG
file offset: 33
data length: 388350
cyclic redundancy check: 3296899893 (network byte order 0x35b382c4)

chunk type: IDAT
file offset: 388395
data length: 8192
cyclic redundancy check: 1185275935 (network byte order 0x1fe0a546)
```

On l'extrait :

`$ dd skip=33 count=388350 if=../../stage2.png of=../../extracted bs=1`

```
$ file extracted 
extracted: TTComp archive data, binary, 2K dictionary
```

On télécharge ça : http://www.exelana.com/techie/c/ttdecomp.html

Mais il s'agit d'une fausse piste.

On remarque qu'en fait le chunk sTeG est une image PNG dont le header a été modifié !

```
$ hexdump -C extracted | head                                        
00000000  00 05 ec fe 73 54 65 47  00 00 00 0d 49 48 44 52  |....sTeG....IHDR|
00000010  00 00 01 f4 00 00 01 d9  08 06 00 00 00 70 e4 c9  |.............p..|
00000020  62 00 00 20 00 49 44 41  54 78 9c ec bd 07 94 24  |b.. .IDATx.....$|
```

En comparaison :

```
$ hexdump -C stage2.png | head 
00000000  89 50 4e 47 0d 0a 1a 0a  00 00 00 0d 49 48 44 52  |.PNG........IHDR|
00000010  00 00 02 01 00 00 01 54  08 06 00 00 00 bd 4d ef  |.......T......M.|
00000020  b8 00 05 ec fe 73 54 65  47 00 00 00 0d 49 48 44  |.....sTeG....IHD|
```

On modifie donc les premiers octets avec hexedit :

```
$ hexdump -C edited_extr | head
00000000  89 50 4e 47 0d 0a 1a 0a  00 00 00 0d 49 48 44 52  |.PNG........IHDR|
00000010  00 00 01 f4 00 00 01 d9  08 06 00 00 00 70 e4 c9  |.............p..|
```

On récupère une image qui contient le flag !

404CTF{7h47_v1c10us_m1zzing_z19natur3}
