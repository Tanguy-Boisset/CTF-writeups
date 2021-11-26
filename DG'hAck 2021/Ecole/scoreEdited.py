import argparse
import json
import random


def parse_school_arguments():
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "-i",
        "--input",
        type=argparse.FileType("r", encoding="UTF-8"),
        required=True,
        help="Fichier de voeux des élèves des trois classes (ex.: /path/to/dghack2021-ecole-repartition.json)",
        default="./dghack2021-ecole-repartition.json",
    )
    parser.add_argument(
        "-c",
        "--classes",
        type=argparse.FileType("r", encoding="UTF-8"),
        required=True,
        help="Fichier de répartition finale des élèves (à scorer)",
    )

    args = parser.parse_args()

    eleves = json.load(args.input)
    classes = json.load(args.classes)

    return classes, eleves


def score_for_eleve(el, classe):
    score = 0
    for note in range(0, 4):
        if el["friends"][note] in classe:
            score += (4 - note) * 5
    return score


def score_for_classe(classe, eleves):
    score = 0
    for e in classe:
        el = eleves[e - 1]
        score += score_for_eleve(el, classe)
    return score


def score_total(classes, eleves):
    return (
        score_for_classe(classes[0], eleves)
        + score_for_classe(classes[1], eleves)
        + score_for_classe(classes[2], eleves)
    )


def score_total_with_error_notification(classes, eleves):
    for i in range(0, 2):
        if len(classes[i]) > 30:
            raise ValueError("Une classe ne peut pas contenir plus de 30 élèves.")
        for e in classes[i]:
            if e in classes[(i + 1) % 3] or e in classes[(i + 2) % 3]:
                raise ValueError("Un élève ne peut pas être dans deux classes.")
            if classes[i].count(e) > 1:
                raise ValueError("Un élève ne peut pas être deux fois dans une classe.")

    return score_total(classes, eleves)


def verify_score(score):
    return score >= 2950


#if __name__ == "__main__":
#    classes, eleves = parse_school_arguments()
#
#    score = score_total_with_error_notification(classes, eleves)
#
#    print("score total : %d" % score)
#    print(verify_score(score))


if __name__ == "__main__":

    maxi = 0

    ENV = []

    L = [k for k in range(1,91)]
    for i in range(10):
        random.shuffle(L)
        maClasse = [L[:30],L[30:60],L[60:90]]
        ENV += [maClasse]

    classes, eleves = parse_school_arguments()

    score = score_total_with_error_notification(classes, eleves)

    while (not verify_score(score)):
        mesScores = []
        for classe in ENV:
            score = score_total_with_error_notification(classe, eleves)
            mesScores.append(score)

        valeur_max = max(mesScores)
        score = valeur_max
        index_max = mesScores.index(valeur_max)

        maClasseForte = ENV[index_max]

        ENV = [maClasseForte]

        for k in range(9):

            newClasse = [maClasseForte[i].copy() for i in range(3)]

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
    #print(classe)

 