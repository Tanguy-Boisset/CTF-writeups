import argparse
import json


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


if __name__ == "__main__":
    classes, eleves = parse_school_arguments()

    score = score_total_with_error_notification(classes, eleves)

    print("score total : %d" % score)
    print(verify_score(score))
