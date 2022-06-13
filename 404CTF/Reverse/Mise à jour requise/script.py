import random
import string
import time
import sys

chars = string.ascii_letters + string.digits + "____________."
s = [16, 3, 12, 9, 1, 60, 1, 3, 14, 39, 13, 16, 16, 1, 9, 13, 3, 39, 60,
    16, 16, 1, 60, 7, 39, 13, 3, 13, 18, 3, 13, 25, 14, 3, 1, 14, 60,
    13, 32, 13, 3, 39, 16, 18, 18, 3, 43, 16, 18, 3, 1, 43, 18, 16,
    13, 16, 1, 3, 1, 16, 13, 18, 60, 16, 3, 3, 14, 18, 13, 14, 16, 18,
    7, 3, 7, 25, 7, 7, 13, 13, 13, 3, 60, 1, 3, 13, 1, 25, 18, 16, 32,
    16, 60, 1, 7, 44, 18, 39, 39, 39, 60, 3, 1, 60, 3, 16, 13, 13, 14,
    1, 3, 39, 39, 31, 32, 39, 32, 18, 39, 3, 13, 32, 60, 7, 7, 39, 14,
    3, 18, 14, 60, 39, 18, 7, 1, 32, 13, 3, 14, 39, 39, 7, 1, 1, 13,
    29, 60, 13, 39, 14, 14, 16, 60, 1, 3, 44, 14, 3, 1, 1, 1, 39, 13,
    14, 39, 18, 3, 7, 13, 39, 32, 1, 43, 1, 16, 1, 3, 18, 14, 25, 32,
    7, 13, 39, 7, 1, 3, 60, 13, 13, 7, 18, 1, 3, 18, 1, 60, 7, 1, 39,
    14, 3, 39, 7, 31, 1, 7, 18, 7, 32, 3, 3, 14, 32, 14, 1, 32, 12,
    18, 31, 39, 1, 13, 13, 43, 44, 32, 3, 32, 60, 14, 60, 60, 7, 3, 1,
    3, 3, 14, 1, 60, 16, 44, 3, 1, 32, 13, 5, 16, 39, 3, 60, 7, 14, 3,
    13, 7, 31, 13, 39, 9, 3, 44, 13, 16, 14, 18, 18, 3, 7, 3, 3, 3, 7,
    3, 3, 16, 39, 3, 3, 13, 32, 13, 3, 18, 7, 10, 3, 18, 1, 7, 7, 18,
    13, 43, 18, 3, 32, 39, 32, 13, 1, 18, 10, 1, 32, 1, 16, 32, 3, 44,
    3, 18, 1, 1, 1, 16, 18, 25, 60, 1, 39, 1, 18, 60, 16, 1, 7, 3, 13,
    16, 18, 39, 14, 7, 14, 3, 14, 13, 7, 16, 10, 18, 13, 3, 16, 13, 3,
    32, 43, 13, 14, 1, 13, 1, 14, 18, 60, 7, 3, 7, 31, 1, 18, 26, 7,
    3, 3, 32, 1, 7, 18, 7, 1, 16, 18, 39, 14, 7, 3
]


# f = b(flag, 1) qu'on trouve facilement grâce à la vérification effectuée par c
f = [11, 7, 15, 14, 4, 9, 4, 7, 3, 6, 8, 11, 11, 4, 14, 8, 7, 6, 9, 11, 11, 4, 9, 10, 6, 8, 7, 8, 5, 7, 8, 12, 3, 7, 4, 3, 9, 8, 2, 8, 7, 6, 11, 5, 5, 7, 1, 11, 5, 7, 4, 1, 5, 11, 8, 11, 4, 7, 4, 11, 8, 5, 9, 11, 7, 7, 3, 5, 8, 3, 11, 5, 10, 7, 10, 12, 10, 10, 8, 8, 8, 7, 9, 4, 7, 8, 4, 12, 5, 11, 2, 11, 9, 4, 10, 13, 5, 6, 6, 6, 9, 7, 4, 9, 7, 11, 8, 8, 3, 4, 7, 6, 6, 0, 2, 6, 2, 5, 6, 7, 8, 2, 9, 10, 10, 6, 3, 7, 5, 3, 9, 6, 5, 10, 4, 2, 8, 7, 3, 6, 6, 10, 4, 4, 8, 17, 9, 8, 6, 3, 3, 11, 9, 4, 7, 13, 3, 7, 4, 4, 4, 6, 8, 3, 6, 5, 7, 10, 8, 6, 2, 4, 1, 4, 11, 4, 7, 5, 3, 12, 2, 10, 8, 6, 10, 4, 7, 9, 8, 8, 10, 5, 4, 7, 5, 4, 9, 10, 4, 6, 3, 7, 6, 10, 0, 4, 10, 5, 10, 2, 7, 7, 3, 2, 3, 4, 2, 15, 5, 0, 6, 4, 8, 8, 1, 13, 2, 7, 2, 9, 3, 9, 9, 10, 7, 4, 7, 7, 3, 4, 9, 11, 13, 7, 4, 2, 8, 19, 11, 6, 7, 9, 10, 3, 7, 8, 10, 0, 8, 6, 14, 7, 13, 8, 11, 3, 5, 5, 7, 10, 7, 7, 7, 10, 7, 7, 11, 6, 7, 7, 8, 2, 8, 7, 5, 10, 18, 7, 5, 4, 10, 10, 5, 8, 1, 5, 7, 2, 6, 2, 8, 4, 5, 18, 4, 2, 4, 11, 2, 7, 13, 7, 5, 4, 4, 4, 11, 5, 12, 9, 4, 6, 4, 5, 9, 11, 4, 10, 7, 8, 11, 5, 6, 3, 10, 3, 7, 3, 8, 10, 11, 18, 5, 8, 7, 11, 8, 7, 2, 1, 8, 3, 4, 8, 4, 3, 5, 9, 10, 7, 10, 0, 4, 5, 23, 10, 7, 7, 2, 4, 10, 5, 10, 4, 11, 5, 6, 3, 10, 7]

def a(c, r=True):
    n = ord(c)
    if r: random.seed(n)
    match n:
        case 0:
            return dict.fromkeys(range(10), 0)
        case _:
            return (d:=a(chr(n - 1), False)) | {(m:=random.randint(0, 9)): d[m] + random.randint(0,2)}


def b(p, n):
    match list(p):
        case []:
            return []
        case [f, *rest]:
            l = list(a(f).values()) + b(''.join(rest), n*2)
            random.seed(n)
            random.shuffle(l)
            return l

def score(mdp):
    mdp_parsed = b(mdp, 1)
    score = 0
    for i in range(len(mdp_parsed)):
        if mdp_parsed[i] == f[i]:
            score += 1
    return score

def encapsule(mdp):
    return '404CTF{' + mdp + '}'


if __name__ == "__main__":
    nb_mutations = int(sys.argv[1])

    # Score max obtenu
    maxi = 0

    # Environnement
    ENV = []

    # 10 premiers éléments aléatoires
    for _ in range(10):
        mdp = ''
        for i in range(30):
            mdp += chars[random.randint(0,len(chars)-1)]
        ENV.append(mdp)

    scoreMax = -1

    while (scoreMax < 380):
        # Score des répartitions dans ENV

        listeScores = [score(encapsule(mdp)) for mdp in ENV]

        scoreMax = max(listeScores)
        indexMax = listeScores.index(scoreMax)
        mdpSurvivant = ENV[indexMax]

        ENV.pop(indexMax)
        listeScores.pop(indexMax)

        tmpMax = max(listeScores)
        indexMax = listeScores.index(tmpMax)
        mdpSurvivant_2 = ENV[indexMax]

        # On ne fait survivre que les deux plus forts
        ENV = [mdpSurvivant,mdpSurvivant_2]

        random.seed(time.time())

        for i in range(8):
            # Reproduction
            new_mdp = ''
            for j in range(len(mdpSurvivant)):
                if mdpSurvivant[j] == mdpSurvivant_2[j]:
                    new_mdp += mdpSurvivant[j]
                else:
                    gene = random.randint(0,1)
                    if gene == 0:
                        new_mdp += mdpSurvivant[j]
                    else:
                        new_mdp += mdpSurvivant_2[j]

            # Mutation
            for i in range(nb_mutations):
                index = random.randint(1,len(mdp))
                new_mdp = new_mdp[:index-1] + chars[random.randint(0,len(chars)-1)] + new_mdp[index:]

            ENV.append(new_mdp)

        if scoreMax > maxi:
            maxi = scoreMax
            print("Nouveau score max : %d" % maxi)
            if scoreMax > 350:
                print("flag partiel : %s" % encapsule(mdpSurvivant))

    print("===============================================")
    print("FLAG TROUVÉ : %s" % encapsule(mdpSurvivant))
