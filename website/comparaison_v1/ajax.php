<?php
require("data.php");
$cur = getDB();

$id_pays = $_GET["id_pays"];

// Nom
$query = "SELECT * FROM pays WHERE id = :id_pays";
$sth = $cur->prepare($query);
$sth->bindParam(":id_pays", $id_pays, PDO::PARAM_STR);
$sth->execute();
$ligne = $sth->fetch();
$nom = $ligne["nom"];

// Capitale
$query = "SELECT * FROM villes WHERE id_pays = :id_pays and capitale = :is_capitale";
$sth = $cur->prepare($query);
$is_capitale = 1;
$sth->bindParam(":id_pays", $id_pays, PDO::PARAM_STR);
$sth->bindParam(":is_capitale", $is_capitale, PDO::PARAM_INT);
$sth->execute();
$ligne = $sth->fetch();
$capitale = $ligne["nom"];

// Spider
$dataSpider = dataSpider($id_pays);

// Line
$dataLine = dataLine($id_pays);

// Bar
$dataBar = dataBar($id_pays);

$dataAjax = array("nom"=>$nom,
"capitale"=>$capitale,
"id_pays"=>$id_pays,
"spider"=>json_encode($dataSpider),
"line"=>json_encode($dataLine),
"bar"=>json_encode($dataBar));

header('Content-Type: application/json; charset=utf-8');
echo json_encode($dataAjax);

?>