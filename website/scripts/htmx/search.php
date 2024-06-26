<?php

if (!isset($_SERVER["HTTP_HX_REQUEST"])) {
    header("HTTP/1.1 401");
    exit;
}

if (!isset($_GET["id_continent"]) || !isset($_GET["search"]) || !isset($_GET["page"])) {
    header("HTTP/1.1 400");
    exit;
}

require("../../functions.php");
$cur = getDB();

$search = $_GET["search"];
$page = $_GET["page"];
$id_continent = $_GET["id_continent"];

if (strlen($search) == 0) {
    echo <<<HTML
        <div id=search class="container-catalogue"></div>
    HTML;
    exit;
}

// INJECTION SQL !!!!!!!!!!!!!!!!!!!!!!!!
if ($id_continent == 0) {
    $queryPays = "SELECT pays.id AS idp, pays.nom AS p, score FROM pays WHERE pays.nom LIKE '%".$search."%' ORDER BY score DESC LIMIT 8";
} else if ($id_continent == 2) {
    $queryPays = "SELECT pays.id AS idp, pays.nom AS p, score FROM pays WHERE (id_continent = 3 OR id_continent = 2 ) AND (pays.nom LIKE '%".$search."%') ORDER BY score DESC LIMIT 8";
} else {
    $queryPays = "SELECT pays.id AS idp, pays.nom AS p, score FROM pays WHERE id_continent = $id_continent AND (pays.nom LIKE '%".$search."%') ORDER BY score DESC LIMIT 8";
}

$resultPays = $cur->query($queryPays);
$i = 0;

echo <<<HTML
    <div id=search class="container-catalogue">
HTML;

while ($rsPays = $resultPays->fetch(PDO::FETCH_ASSOC)) {
    $letter = getLetter($rsPays["score"]);
    echo addSlimCountry($rsPays["idp"],$rsPays["p"],$letter,$page);
    $i++;
}

if ($i == 0) {
    echo <<<HTML
        <p class="no">Aucun résultat.</p>
    HTML;    
}

echo <<<HTML
    </div>
HTML;    


?>