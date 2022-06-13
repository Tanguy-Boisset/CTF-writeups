Nous avons réussi à infiltrer Hallebarde et à exfiltrer des données. Cependant nos agents se sont fait repérer durant la copie et ils n'ont pas pu copier l'intégralité des données. Pouvez-vous analyser ce qu'ils ont réussi à récupérer ?

On a 2 disques et un indice qui nous met sur la voie d'un raid 5.

On restore un troisième disque en xorant les deux premiers entre eux.

`$ python xor.py`

Grâce à un super script de Itarow, on récupère les données originelles.

```
$ python raid.py disk0.img disk1.img disk2.img diskFinal
$ file diskFinal 
diskFinal: Zip archive data, at least v2.0 to extract, compression method=deflate
$ mv diskFinal archive.zip
$ unzip archive.zip 
    Archive:  archive.zip
    End-of-central-directory signature not found.  Either this file is not
    a zipfile, or it constitutes one disk of a multi-part archive.  In the
    latter case the central directory and zipfile comment will be found on
    the last disk(s) of this archive.
    unzip:  cannot find zipfile directory in one of archive.zip or
            archive.zip.zip, and cannot find archive.zip.ZIP, period.
```

Aïe ! On la restore avec l'option -FF :

```
$ zip -FF archive.zip --out restored.zip
    Fix archive (-FF) - salvage what can
        zip warning: Missing end (EOCDR) signature - either this archive
                        is not readable or the end is damaged
    Is this a single-disk archive?  (y/n): y
    Assuming single-disk archive
    Scanning for entries...
    copying: flag.txt  (52 bytes)
    copying: flag_c0rr_pt3d.png 
        zip warning: no end of stream entry found: flag_c0rr_pt3d.png
        zip warning: rewinding and scanning for later entries
```
```
$ unzip restored.zip 
    Archive:  restored.zip
    error [restored.zip]:  missing 27618 bytes in zipfile
    (attempting to process anyway)
    error: invalid zip file with overlapped components (possible zip bomb)
```
Aïe à nouveau... On a quand même des infos intéressantes sur un fichier texte et une image png.
On tente de restaurer une deuxième fois :

```
$ zip -FF restored.zip --out restored2.zip
    Fix archive (-FF) - salvage what can
    Found end record (EOCDR) - says expect single disk archive
    Scanning for entries...
    copying: flag.txt  (52 bytes)
    Central Directory found...
    EOCDR found ( 1    224)...
```
```
$ unzip restored2.zip
    Archive:  restored2.zip
    inflating: flag.txt 
```

On lit le flag :

404CTF{RAID_5_3st_p4s_tr3s_c0mpl1qu3_1abe46685ecf}

Note : il est possible de tout extraire avec :

```
$ jar -xvf archive.zip  
Picked up _JAVA_OPTIONS: -Dawt.useSystemAAFontSettings=on -Dswing.aatext=true
 décompressé : flag.txt
java.io.EOFException: Unexpected end of ZLIB input stream
	at java.base/java.util.zip.InflaterInputStream.fill(InflaterInputStream.java:245)
	at java.base/java.util.zip.InflaterInputStream.read(InflaterInputStream.java:159)
	at java.base/java.util.zip.ZipInputStream.read(ZipInputStream.java:197)
	at java.base/java.util.zip.ZipInputStream.closeEntry(ZipInputStream.java:143)
	at jdk.jartool/sun.tools.jar.Main.extractFile(Main.java:1456)
	at jdk.jartool/sun.tools.jar.Main.extract(Main.java:1363)
	at jdk.jartool/sun.tools.jar.Main.run(Main.java:409)
	at jdk.jartool/sun.tools.jar.Main.main(Main.java:1680)

```

On obtient flag.txt et flag_c0rr_pt3d.png.

On analyse l'image avec pngcheck :

```
$ pngcheck -cvv flag_c0rr_pt3d.png  
File: flag_c0rr_pt3d.png (31243 bytes)
  File is CORRUPTED.  It seems to have suffered EOL conversion.
ERRORS DETECTED in flag_c0rr_pt3d.png
```

On modifie le header dans une image copie "copie_corr.png" :

00000000  89 50 4e 47 0d 00 00 0a  00 00 ff 0d 49 48 44 52  |.PNG........IHDR|

qu'on change en (cf https://en.wikipedia.org/wiki/Portable_Network_Graphics#File_header):

00000000  89 50 4e 47 0d 0a 1a 0a  00 00 ff 0d 49 48 44 52  |.PNG........IHDR|

```
$ pngcheck -cvv copie_corr.png         
File: copie_corr.png (31243 bytes)
  chunk IHDR at offset 0x0000c, length 65293:  EOF while reading data
ERRORS DETECTED in copie_corr.png
```

On a un problème sur la longueur du chunk IHDR. La longueur est notée 00 00 ff 0d alors qu'on a que 14 octets (=0x000d) de données (26 au total, dont on retire 4 pour la taille, 4 de nom de chunk et 4 de CRC).

On edit :

00000000  89 50 4e 47 0d 0a 1a 0a  00 00 00 0d 49 48 44 52  |.PNG........IHDR|

```
$ pngcheck -cvv copie_corr.png
File: copie_corr.png (31243 bytes)
  chunk IHDR at offset 0x0000c, length 13
    1152 x 62088 image, 32-bit RGB+alpha, non-interlaced
  CRC error in chunk IHDR (computed 6c55bbd4, expected 082b810d)
ERRORS DETECTED in copie_corr.png
```

On a une erreur de CRC qu'on peut corriger par la valeur indiquée :

00000000  89 50 4e 47 0d 0a 1a 0a  00 00 00 0d 49 48 44 52  |.PNG........IHDR|
00000010  00 00 04 80 00 00 f2 88  08 06 00 00 00 08 2b 81  |..............+.|
00000020  0d 00 00 00 01 73 52 47  42 00 ae ce 1c e9 00 00  |.....sRGB.......|

En :

00000000  89 50 4e 47 0d 0a 1a 0a  00 00 00 0d 49 48 44 52  |.PNG........IHDR|
00000010  00 00 04 80 00 00 f2 88  08 06 00 00 00 6c 55 bb  |.............lU.|
00000020  d4 00 00 00 01 73 52 47  42 00 ae ce 1c e9 00 00  |.....sRGB.......|

```
$ pngcheck -cv copie_corr.png 
File: copie_corr.png (31243 bytes)
  chunk IHDR at offset 0x0000c, length 13
    1152 x 62088 image, 32-bit RGB+alpha, non-interlaced
  chunk sRGB at offset 0x00025, length 1
    rendering intent = perceptual
  chunk gAMA at offset 0x00032, length 4: 0.45455
  chunk pHYs at offset 0x00042, length 9: 3780x3780 pixels/meter (96 dpi)
  chunk IDAT at offset 0x00057, length 61756:  EOF while reading data
ERRORS DETECTED in copie_corr.png
```

Problème de longueur sur le chunk IDAT

Vrai longueur : 0x79ac qu'on obtient en sélectionnant le reste du fichier avec bless et en retirant 4 du CRC.

00000050  2b 0e 1b 00 00 f1 3c 49  44 41 54 78 5e ec fd 65  |+.....<IDATx^..e|

En :

00000050  2b 0e 1b 00 00 79 ac 49  44 41 54 78 5e ec fd 65  |+....y.IDATx^..e|

```
$ pngcheck -cv copie_corr.png
File: copie_corr.png (31243 bytes)
  chunk IHDR at offset 0x0000c, length 13
    1152 x 62088 image, 32-bit RGB+alpha, non-interlaced
  chunk sRGB at offset 0x00025, length 1
    rendering intent = perceptual
  chunk gAMA at offset 0x00032, length 4: 0.45455
  chunk pHYs at offset 0x00042, length 9: 3780x3780 pixels/meter (96 dpi)
  chunk IDAT at offset 0x00057, length 31148
    zlib: deflated, 32K window, fast compression
  CRC error in chunk IDAT (computed 95cc5738, expected 9fc5e9e3)
ERRORS DETECTED in copie_corr.png
```

00007a00  49 21 28 59 ea 1d 55 9f  c5 e9 e3                 |I!(Y..U....|

En :

00007a00  49 21 28 59 ea 1d 55 95  cc 57 38                 |I!(Y..U..W8|

```
$ pngcheck -cv copie_corr.png    
File: copie_corr.png (31243 bytes)
  chunk IHDR at offset 0x0000c, length 13
    1152 x 62088 image, 32-bit RGB+alpha, non-interlaced
  chunk sRGB at offset 0x00025, length 1
    rendering intent = perceptual
  chunk gAMA at offset 0x00032, length 4: 0.45455
  chunk pHYs at offset 0x00042, length 9: 3780x3780 pixels/meter (96 dpi)
  chunk IDAT at offset 0x00057, length 31148
    zlib: deflated, 32K window, fast compression
  file doesn't end with an IEND chunk
ERRORS DETECTED in copie_corr.png
```

Il manque le chunk IEND.
Un chunk IEND vaut ça :
00 00 00 00 49 45 4e 44 ae 42 60 82

On ajoute ce chunk avec le programme python.

On ouvre l'image sur aperisolve et on obtient un flag qu'on arrive à lire :

404CTF{L4_C0rr_pt10N_s3_r_p4r_}
