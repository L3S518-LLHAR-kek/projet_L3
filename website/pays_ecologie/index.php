<!DOCTYPE html>
<html lang= "fr" class = "nouveau">

<head>

<meta charset="UTF-8">
<link href="styles/style.css" rel="stylesheet" type="text/css" />
<title> Nouveau Client </title>
</head>
 <body> 
    <?php
    function score_ecotourisme($score){
    $critere1 = 20;
    $critere2 = 40;
    $critere3 = 60;
    $critere4 = 80;
    $critere5 = 100;
        if $score < 100 {
            if $score < 80{
                if $score < 60 {
                    if $score < 40 {
                        if $score < 20{
                            echo '<p> E </p>'
                        }
                    }
                    else {
                        echo '<p> D </p>'
                    }
                }
                else {
                    echo '<p> C </p>';
                }
            }
            else {
                echo '<p> B </p>';
            }
        } 
        else {
            echo '<p> A </p>';
        }
    }
    echo score_ecotourisme(30); 
    ?>
</body>  
</html>