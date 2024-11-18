# php-prolog

Un exemple de com cridar Prolog des de PHP.

Suposa que swi-pl està instal·lat al PATH (per defecte, a Windows, no ho fa!).

L'exemple permet configurar tant la base de coneixements com l'objectiu. També permet triar si es vol saber només si l'objectiu és correcte o no o si es volen saber totes les solucions.

Seguint la mateixa idea, es podria permetre configurar també el programa (les regles).

També es podrien deixar la base de coneixements o l'objectiu fixats.

La base de coneixements s'especifica a través d'un text. Canviar-ho perquè funcioni amb una consulta a una base de dades és trivial.

Aquesta base de coneixements s'escriu a un fitxer al subdirectori `tmp`. Assegura't que existeix i que s'hi té permís d'escriptura.

# Executar programes amb PHP

La [funció exec](https://www.php.net/manual/en/function.exec.php) pot anar bé per fer això.

En aquest cas, el que voldrem fer és una cosa semblant a això:

```php
exec("swipl ...", $output, $status);
```

Així, `$status` conté el valor resultat d'executar el programa (útil per saber si l'objectiu ha tengut èxit) i `$output` conté un _array_ de les línies de la sortida estàndard (del que se n'hagi fet `write`).

## Saber si és correcte o no:

S'executa aquesta ordre:

```shell
swipl -q -g "antecessor(toni,aina)" -t halt ./tmp/exemple.pl antecessor.pl
```

És a dir, executa `swipl` en mode _quiet_ (`-q`) per no capturar res que no ens interessi, amb l'objectiu desitjat (`-g`) `"antecessor(toni,aina)"` (entre cometes) seguit del _top level_ (`-t`) `halt` que fa que no entri al mode interactiu i, com a arguments, primer la base de coneixements (`./tmp/exemple.pl`) i després el programa (`antecessor.pl`).

Sabem si això és correcte o no segons el valor resultat d'executar el programa: `0` vol dir que és correcte, `1` vol dir que no.

## Saber (totes) les solucions

Com que no entram en mode interactiu, no podem veure el resultat de la nostra pregunta ni demanar més solucions.

Llavors, hem de canviar lleugerament l'objectiu desitjat perquè imprimeixi totes les solucions:

```shell
swipl -q -g "findall([X,Y],antecessor(X,Y),L),write(L)" -t halt ./tmp/exemple.pl antecessor.pl
```

És a dir, el mateix que abans, però l'objectiu desitjat (`-g`) no és només `"antecessor(X,Y)"` sinó que hem de cercar totes les solucions i posar-les a una llista amb `findall([X,Y],antecessor(X,Y),L)` i després mostrar-la amb `write(L)`. Tot plegat queda `"findall([X,Y],antecessor(X,Y),L),write(L)"`.

Això ens donaria aquest resultat:

```
[[joana,miquel],[toni,miquel],[toni,maria],[miquel,aina],[miquel,gloria],[gloria,jaume],[joana,aina],[joana,gloria],[joana,jaume],[toni,aina],[toni,gloria],[toni,jaume],[miquel,jaume]]
```

Evidentment, això es pot processar fàcilment i mostrar-ho d'alguna altra manera.

Compte que `findall` sempre té èxit, encara que `L` acabi buida (no ha trobat cap solució). Si voleu que en aquest cas falli, es pot fer servir el predicat `bagof`.

Les solucions se poden escriure una a cada línia amb `maplist(writeln,L)` en comptes de `write(L)`.