# php-prolog

Un exemple de com cridar Prolog des de PHP.

Suposa que swi-pl està instal·lat al PATH (per defecte, a Windows, no ho fa!).

L'exemple permet configurar tant la base de coneixements com l'objectiu. També permet triar si es vol saber només si l'objectiu és correcte o no o si es volen saber totes les solucions.

Seguint la mateixa idea, es podria permetre configurar també el programa (les regles).

També es podrien deixar la base de coneixements o l'objectiu fixats.

La base de coneixements s'especifica a través d'un text. Canviar-ho perquè funcioni amb una consulta a una base de dades és trivial.

Aquesta base de coneixements s'escriu a un fitxer al subdirectori `tmp`. Assegura't que existeix i que s'hi té permís d'escriptura.

## Saber si és correcte o no:

S'executa aquesta ordre:

```bash
swipl -q -g antecessor(toni,aina) -t halt ./tmp/exemple.pl antecessor.pl
```