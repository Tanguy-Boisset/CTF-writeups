On télécharge le code source index.js

La première partie du code concerne le keypad et ne contient rien d'intéressant.

La deuxième partie est une fonction. En exécutant la partie replace, on obtient une chaîne de caractère qui est en réalité le résultat d'une fonction !\
Pour en avoir le contenu, il suffit de copier ce texte en enlevant les ' et le () final et on trouve :
```
(function anonymous(
) {
/* FONCTIONNEMENT */
var key = $(".keypad").keypad(function (pin) {
  if (pin == "240801300505131273100172") {
    document.location.href = "./nob03y_w1lL_Ev3r_fiNd_th15_PaGe.html";
  }
});
})
```
On se rend sur la page indiquée et on obtient le flag !

404CTF{Haha_J3_5ui$_f4N_dObfu5c4tIoN_en_JS}
