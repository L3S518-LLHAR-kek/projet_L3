<?php
require("../functions.php");

function dataLine($pays) {
    $conn = getDB();

    $query = "SELECT allk.id_pays, allk.annee AS year, co2, elecRenew AS Enr, pibParHab AS pib, cpi, gpi, arriveesTotal AS arrivees, departs
    FROM (SELECT id_pays, annee FROM economie UNION 
            SELECT id_pays, annee FROM tourisme UNION
            SELECT id_pays, annee FROM surete UNION
            SELECT id_pays, annee FROM ecologie
            ) allk 
    LEFT OUTER JOIN economie ON allk.id_pays = economie.id_pays AND allk.annee = economie.annee 
    LEFT OUTER JOIN ecologie ON allk.id_pays = ecologie.id_pays AND allk.annee = ecologie.annee 
    LEFT OUTER JOIN surete ON allk.id_pays = surete.id_pays AND allk.annee = surete.annee 
    LEFT OUTER JOIN tourisme ON allk.id_pays = tourisme.id_pays AND allk.annee = tourisme.annee
    WHERE allk.id_pays = '$pays'
    ORDER BY allk.annee;";

    $result = $conn->query($query);

    $data = array();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        foreach (array("pib","Enr","co2","arrivees","departs","gpi","cpi") as $key => $value) {
            if (!isset($rs[$value])){
                $rs[$value]="null";
            } 
        }

        $data[] = $rs;

    }

    return $data;

}

function dataSpider($pays) {
    $conn = getDB();

    $query = "SELECT ecologie.annee as annee,
    pibParHab AS pib, elecRenew AS Enr, co2, arriveesTotal AS arrivees, departs, gpi, cpi

    FROM ecologie_norm AS ecologie, economie_norm AS economie, tourisme_norm AS tourisme, surete_norm AS surete
    WHERE ecologie.id_pays = economie.id_pays
    AND economie.id_pays = tourisme.id_pays
    AND tourisme.id_pays = surete.id_pays
    AND surete.id_pays = '$pays'

    AND ecologie.annee = economie.annee
    AND economie.annee = tourisme.annee
    AND tourisme.annee = surete.annee  
    ORDER BY `ecologie`.`annee` DESC;
    ";

    $result = $conn->query($query);

    $data = array();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $data[$rs["annee"]] = array();
        foreach (array("pib","Enr","co2","arrivees","departs","gpi","cpi") as $key => $value) {
            if (!isset($rs[$value])){
                $rs[$value]="null";
            } 
            $data[$rs["annee"]][] = array("var" => $value, "value" => $rs[$value]);
        }
    }

    return $data;
}

function dataBar($pays) {
    $conn = getDB();

    $query = "SELECT ecologie.annee as annee,
    pibParHab AS pib, co2, arriveesTotal AS arrivees, gpi, cpi

    FROM ecologie_grow AS ecologie, economie_grow AS economie, tourisme_grow AS tourisme, surete_grow AS surete
    WHERE ecologie.id_pays = economie.id_pays
    AND economie.id_pays = tourisme.id_pays
    AND tourisme.id_pays = surete.id_pays
    AND surete.id_pays = '$pays'

    AND ecologie.annee = economie.annee
    AND economie.annee = tourisme.annee
    AND tourisme.annee = surete.annee  
    ORDER BY `ecologie`.`annee` DESC;
    ";


    $result = $conn->query($query);

    $data = array();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $data[$rs["annee"]] = array();
        foreach (array("pib","co2","arrivees","gpi","cpi") as $key => $value) {
            if (!isset($rs[$value])){
                $rs[$value]="null";
            } 
            $data[$rs["annee"]][] = array("var" => $value, "value" => $rs[$value]);
        }
    }

    return $data;
}
