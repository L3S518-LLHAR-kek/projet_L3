<?php

$country_src = isset($_GET["country_src"]) ? $_GET["country_src"] : null;
$city_src = isset($_GET["city_src"]) ? $_GET["city_src"] : null;
$country_dst = isset($_GET["country_dst"]) ? $_GET["country_dst"] : null;
$city_dst = isset($_GET["city_dst"]) ? $_GET["city_dst"] : null;
$departure_date = isset($_GET["departure_date"]) ? $_GET["departure_date"] : null;
$arrival_date = isset($_GET["arrival_date"]) ? $_GET["arrival_date"] : null;
$passengers = isset($_GET["passengers"]) ? $_GET["passengers"] : null;

exec("./plane_cli.py --departure Toulouse --arrival Faro", $output);
var_dump($output);