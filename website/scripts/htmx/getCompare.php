<?php
require("../../functions.php");

$cur = getDB();

$id_pays = $_GET["id_pays"];

if (isset($_GET["incr"])) {
    $incr = $_GET["incr"];
    $_SESSION["incr"] = $incr;
} else {
    $incr = $_SESSION["incr"];
}

$old = "00";
if (!in_array($id_pays,$_SESSION["pays"])) {
    if (count($_SESSION["pays"]) != $incr) {
        $old = $_SESSION["pays"][$incr];
    }
    $_SESSION["pays"][$incr] = $id_pays;
}

if (isset($_GET["map"])) {
    $map = "false";
} else {
    $map = "true";
}

// Nom
$query = "SELECT * FROM pays WHERE id = :id_pays";
$sth = $cur->prepare($query);
$sth->bindParam(":id_pays", $id_pays, PDO::PARAM_STR);
$sth->execute();
$ligne = $sth->fetch();
$nom = $ligne["nom"];
$sv1 = explode(" : ",htmlspecialchars($ligne["sv1"]));
$sv2 = explode(" : ",htmlspecialchars($ligne["sv2"]));
$sv3 = explode(" : ",htmlspecialchars($ligne["sv3"]));
$letter = getLetter($ligne["score"]);

// Capitale
$query = "SELECT * FROM villes WHERE id_pays = :id_pays and capitale = :is_capitale";
$sth = $cur->prepare($query);
$is_capitale = 1;
$sth->bindParam(":id_pays", $id_pays, PDO::PARAM_STR);
$sth->bindParam(":is_capitale", $is_capitale, PDO::PARAM_INT);
$sth->execute();
$ligne = $sth->fetch();
$capitale = $ligne["nom"];

// Line
$dictLine = dataLine($id_pays, $cur);
$dataLine = json_encode($dictLine,JSON_NUMERIC_CHECK);

$dataSpider = json_encode(dataSpider($id_pays, $cur),JSON_NUMERIC_CHECK);

$dataBar = json_encode(dataBar($id_pays, $cur),JSON_NUMERIC_CHECK);
$dataTab = json_encode(dataTab($id_pays, $cur),JSON_NUMERIC_CHECK);

$incrP = $incr+1;

echo <<<HTML

<div class="bandeau half" id="bandeau$incr" hx-swap-oob="outerHTML">     
    <img class="img" src='assets/img/$id_pays.jpg' alt="Bandeau">
    <img class="flag" src='assets/twemoji/$id_pays.svg'>
    <h1 class="nom">$nom</h1>
    <p class="capital">Capitale : $capitale</p>
   
</div>

<div class="score-box score-$letter" id="score$incr" hx-swap-oob="outerHTML">$letter</div>

<div class="container-side g$incrP-1" id="mini$incr" hx-swap-oob="outerHTML">
    <img class="img img-side" src='assets/img/$id_pays.jpg' alt="Bandeau">
    <img class="flag-small" src='assets/twemoji/$id_pays.svg'>
    <h2 class="nom-small">$nom</h2>
</div>

<table id="tabtemp">
    <tr><td id="nom_$incr" hx-swap-oob=outerHTML>$nom</td></tr>
</table>


<script id=scripting hx-swap-oob=outerHTML>
    // spiderCHTMX($incr, $dataSpider, $dataTab, "$nom")
    lineHTMX($incr, $dataLine, "$nom")
    // barHTMX($incr, $dataBar, "$nom")

    if ($map) {
        map.setActive("$id_pays")
    }

    addListe("$sv1[0]","$sv1[1]","test","$id_pays")
    addListe("$sv2[0]","$sv2[1]","test","$id_pays")
    addListe("$sv3[0]","$sv3[1]","test","$id_pays")
    delListe("$old")

    $("#tabtemp").remove()
    // $("#scripting").empty()
</script>

HTML;

$_SESSION["incr"] = ($_SESSION["incr"]+1)%2

?>