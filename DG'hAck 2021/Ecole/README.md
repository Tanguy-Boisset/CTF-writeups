# DG'hAck 2021 : École - 100 pts

## Énoncé 

>**Description**\
>Dans une ecole il y a **90 élèves** que l'on doit repartir en **3 classes**. Les enfants on fait un classement de leurs quatre meilleurs copains. Vous devez trouver une repartition maximisant le plaisir des enfants :
>
>Si un enfant est avec son meilleur copain cela rapporte 20 points, 15 points pour le 2ème copain, 10 points pour le 3e et 5 pour le 4e. Les vœux des élèves sont mentionnés dans le fichier `dghack2021-ecole-repartition.json` du toolkit fourni.
>
>Pour passer le challenge, vous devez avoir un score superieur ou égale à **2950**.

Un toolkit nous est fourni contenant :
- La liste de voeux des élèves (avec leurs copains favoris) `dghack2021-ecole-repartition.json`
- Un algorithme de scoring `score.py`
- Un fichier json de test `test.json`

Le toolkit est dans le fichier zip.

Il est donc possible de tester sa répartition d'élèves en lançant la commande `python3 score.py -i=dghack2021-ecole-repartition.json -c=test.json`

À noter qu'il n'est pas obligatoire de passer par un fichier json pour tester sa répartition. En modifiant `score.py`, on peut simplement tester sur des listes.


## Stratégie

Quand on voit l'énoncé, on pense rapidement au [problème des mariages stables](https://fr.wikipedia.org/wiki/Probl%C3%A8me_des_mariages_stables).

Il existe plusieurs stratégies d'algorithmes pour résoudre ce problème. Ceux-ci permettent théoriquement d'atteindre le score maximal.

Cependant, il peut être intéressant de vérifier s'il existe des algorithmes plus basiques permettant d'atteindre le score de 2950.

### Bogosort

La première chose que j'ai voulu tester est un grand nombre de liste aléatoire.

Pour cela, je crée une liste L contenant mes trois classes de la forme `L = [ [classe1], [classe2], [classe3] ]` où chaque est une liste d'entiers entre 1 et 90, de taille 30.

Il faut faire attention à ce que chaque élève apparaisse une seule fois dans la répartition.

Je crée donc initialement `L = [k for k in range(1,91)]`.

Puis je randomise ma liste avec `random.shuffle(L)` et teste le score.

Je modifie la fonction main de `score.py` pour créer une boucle pour tester un grand nombre de listes aléatoires.

```
if __name__ == "__main__":
    # Score max obtenu
    maxi = 0

    L = [k for k in range(1,91)]

    classes, eleves = parse_school_arguments()

    score = score_total_with_error_notification(classes, eleves)

    while (not verify_score(score)):

        # On mélange L
        classes = random.shuffle(L)

        # On recalcule le score
        score = score_total_with_error_notification(classes, eleves)

        # On affiche le score si le meilleur score est battu
        if score > maxi:
            maxi = score
            print("Nouveau max : " + str(maxi))


    print("score total : %d" % score)
    print(verify_score(score))
    print(classes)

```


Avec cette approche, j'obtiens un score maximale de **~2100**. On est donc bien loin du score voulu.


### Algorithme génétique simplifié

Une deuxième alternative est un algorithme génétique. Si bien mis en place, il est possible d'atteindre un bon score assez rapidement.

Le principe est le suivant :
- On crée un environnement (appelé `ENV`) dans lequel je vais stocker 10 répartitions d'élèves possibles : `ENV = [ [Répartition1], [Répartition2], ... ]`
- Initialement, ENV est rempli par 10 répartitions aléatoires
- On calcule le score pour chaque répartition
- La répartition qui obtient le meilleur score est conservée dans ENV, les autres sont supprimées (survie du plus fort)
- Cette répartition est recopiée 9 fois dans ENV (reproduction)
- Les 9 nouvelles répartions subissent des modifications aléatoires (mutation génétique)
- Puis on reboucle l'étape 3 jusqu'à obtenir le score voulu

On parle d'algorithme génétique (on remarque assez bien l'analogie avec la sélection naturelle).

J'ai mis en place ici une alternative simplifiée. En effet, dans un vrai algorithme génétique, on fait survivre plusieurs éléments dans le but de les faire se reproduire entre eux.

Voici le code associé :

```
if __name__ == "__main__":
    # Score max obtenu
    maxi = 0

    # Environnement
    ENV = []

    # 10 premiers éléments aléatoires
    L = [k for k in range(1,91)]
    for i in range(10):
        random.shuffle(L)
        maClasse = [L[:30],L[30:60],L[60:90]]
        ENV += [maClasse]

    classes, eleves = parse_school_arguments()

    score = score_total_with_error_notification(classes, eleves)

    while (not verify_score(score)):
        # Score des répartitions dans ENV
        mesScores = []
        for classe in ENV:
            score = score_total_with_error_notification(classe, eleves)
            mesScores.append(score)

        # Récupération du survivant
        valeur_max = max(mesScores)
        score = valeur_max
        index_max = mesScores.index(valeur_max)

        maClasseForte = ENV[index_max]

        # On ne fait survivre que le plus fort
        ENV = [maClasseForte]

        # Reproduction + mutations
        for k in range(9):
            # Utilisation de .copy() pour créer plusieurs objets
            newClasse = [maClasseForte[i].copy() for i in range(3)]

            # Permutations aléatoires entre les différentes classes
            for j in range(6):
                x = random.randint(0,29)
                y = random.randint(0,29)

                m = random.randint(0,2)
                n = random.randint(0,2)
                newClasse[m][x], newClasse[n][y] = newClasse[n][y], newClasse[m][x]

            ENV += [newClasse]

        if score > maxi:
            maxi = score
            print("Nouveau max : " + str(maxi))

        if (verify_score(score)):
            print(maClasseForte)

    print("score total : %d" % score)
    print(verify_score(score))

```

Avec cet algorithme, en faisant tourner 4 instances en parallèles, je trouve une solution en **~ 10 min**.

Voici une solution trouvée qui donne un score de **2955** :

```
[
    [78, 79, 51, 68, 72, 82, 19, 43, 37, 39, 90, 83, 13, 57, 55, 71, 36, 30, 52, 7, 17, 81, 15, 9, 86, 46, 31, 88, 3, 21],
    [49, 20, 34, 84, 24, 53, 10, 23, 54, 16, 2, 59, 33, 87, 32, 64, 22, 29, 73, 14, 28, 4, 60, 50, 63, 77, 38, 27, 8, 42],
    [41, 1, 48, 70, 56, 62, 74, 44, 65, 66, 6, 47, 25, 12, 40, 26, 67, 35, 18, 75, 76, 11, 85, 45, 5, 80, 61, 58, 89, 69]
]
```

On upload alors le fichier json contenant ce résultat sur l'interface web du challenge et obtient le flag :

> DGA{Bri@nIsInThe_Kitchen}


En conclusion : un challenge intéressant avec plusieurs approches possibles qui m'a permis de décrocher mes premiers points en scorant le second blood !
