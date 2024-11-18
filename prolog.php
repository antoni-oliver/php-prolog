<?php
$familia = explode("\n", $_POST['familia'] ?? ''); // array de línies
$vell = $_POST['vell'] ?? '';
$jove = $_POST['jove'] ?? '';
$tipus_pregunta = $_POST['tipus_pregunta'] ?? 'correcte_o_no';

if (count($familia) == 0) {
    http_response_code(400); // BAD REQUEST
    header('Content-Type: application/json');
    echo json_encode(["error" => "La família és buida!"]);
    exit();
} else if (preg_match("/^[a-zA-Z]+$/", $vell) !== 1 || preg_match("/^[a-zA-Z]+$/", $jove) !== 1) {
    http_response_code(400); // BAD REQUEST
    header('Content-Type: application/json');
    echo json_encode(["error" => "Has d'indicar bé el vell ($vell) i el jove ($jove)!"]);
    exit();
}

// Processem les dades de la família.
//Aquí ho feim així, però podria haver vengut d'una base de dades o el que fos.
$dades = [];
foreach ($familia as $linia) {
    if (preg_match("/^\s*([a-z]+) -> ([a-z+]+)\s*$/", $linia, $matches) === 1) {
        // inici, 0+ espais, nom, espai, fletxa, espai, nom, 0+ espais, final
        $dades[] = "ascendent($matches[1], $matches[2]).";
    } else {
        http_response_code(400); // BAD REQUEST
        header('Content-Type: application/json');
        echo json_encode(["error" => "La línia \"$linia\" és incorrecta."]);
        exit();
    }
}

// Objectiu: què ens interessa?
// Aquí ho hem fet interactiu, però pot ser voldrem que sigui fix.
$goal = "antecessor($vell,$jove)";

// Segons el que vulguem saber, hem de respondre una cosa o no:
// Si només volem saber si és correcte o no, ens basta amb el resultat d'executar swipl
// si volem saber totes les possibles respostes, ens ho hem de currar un poc més.
if ($tipus_pregunta === 'correcte_o_no') {
    // No fa falta fer res més
} else if ($tipus_pregunta === 'totes_les_solucions') {
    /*
    Aquí se complica: -- $vell i $jove poden ser simbols o variables, així hauria d'anar bé
    findall([$vell,$jove],antecessor($vell,$jove),L),
    maplist(writeln,L)
    */
    $goal = "findall([$vell,$jove]," . $goal . ",L),maplist(writeln,L)";
}

// Fitxer temporal per desar-hi les dades de la família
$nomfitxer = "./tmp/" . uniqid() . ".pl";
file_put_contents($nomfitxer, implode("\n", $dades));

/*
Executam swipl:
-q: quiet: no volem missatges de presentació
-g $goal: volem executar l'objectiu d'abans
-t halt: després d'això, volem aturar el programa
$nomfitxer: primer fitxer: la base de coneixements
antecessor.pl: segon fitxer: les regles prolog fixes
*/
exec("swipl -q -g \"$goal\" -t halt $nomfitxer antecessor.pl", $output, $status);

header('Content-Type: application/json');
if ($tipus_pregunta === 'correcte_o_no') {
    // Volem dir si ha anat bé --> $status === 0
    echo json_encode(["text" => $status === 0 ? "true." : "false."]);
} else if ($tipus_pregunta === 'totes_les_solucions') {
    // La llista de respostes, que pot ser buida (si no ha anat bé)
    echo json_encode(["text" => implode("\n", $output)]);
}

// Esborram el fitxer temporal
unlink($nomfitxer);

?>