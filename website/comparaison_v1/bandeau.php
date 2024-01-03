<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoTourism - Comparaison</title>
    <link rel="stylesheet" href="styles-bandeau.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Base -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

    <!-- Map -->
    <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/lang/FR.js"></script>
    

    <!-- Graph -->
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="../assets/js/lineCompare.js"></script>

    <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
    <script src="../assets/js/spiderCompare.js"></script>

    <script src="../assets/js/barCompare.js"></script>

    <script src="../assets/js/amTools.js"></script>
    <script src="../assets/js/ajax.js"></script>
    <script src="../assets/js/map.js"></script>

</head>

<body>
    <?php
        require("data.php");

        function getPays($arg, $default) {
            if (isset($_GET[$arg])) {
                return $_GET[$arg];
            } else {
                return $default;
            }
        }

        $cur = getDB();

        $pays1 = getPays("pays0", "FR");
        $pays2 = getPays("pays1", "JP");
        $noms = array();

        foreach (array($pays1,$pays2) as $key => $id_pays) {
            $query = "SELECT * FROM pays WHERE id = :id_pays";
            $sth = $cur->prepare($query);
            $sth->bindParam(":id_pays", $id_pays, PDO::PARAM_STR);
            $sth->execute();

            $ligne = $sth->fetch();
            $noms[]=$ligne["nom"];
        }

    ?>

    <div class="container-map">
        <div id="map"></div>
    </div>

    <div id="bandeau">

    </div>

    <div class="grille">
    
        <div class="sidebar">
            <div class="container-mini bg-354F52" style="width: 300px;height:300px">
                <div class="mini-bandeau"> 
                    <img class="img-small" src='../assets/img/US.jpg' alt="Bandeau">
                    <img class="flag-small" src='../assets/twemoji/US.svg'>
                    <h2 class="nom-small">United States</h2>
                </div>

                <div class=mini-stats> 


                </div>

            </div>

            <div class="container-mini bg-52796F" style="width: 300px;height:300px">
                <div class="mini-bandeau"> 
                    <img class="img-small" src='../assets/img/CA.jpg' alt="Bandeau">
                    <img class="flag-small" src='../assets/twemoji/CA.svg'>
                    <h2 class="nom-small">Canada</h2>
                </div>

                <div class=mini-stats> 


                </div>

            </div>

            <div class="container-mini bg-52796F" style="width: 300px">
                <div class="mini-bandeau"> 
                    <img id=plus class="flag-small" src='../assets/img/plus.svg'>
                    <h2 class="nom-small">Ajouter un pays</h2>
                </div>

                <div class=mini-stats> 


                </div>

            </div>

            <div class="container-mini bg-52796F" style="width: 300px">
                <div class="mini-navig">
                    <p>Scores EcoTourism</p>
                    <p>Indicateurs clés</p>
                    <p>Comparaison de chaque indicateur</p>
                    <p>Croissance des indicateurs</p>
                    <p>Savez-vous ?</p>

                </div>

            </div>

        </div>

        <div class="main">
            <div class="container-stats bg-52796F">
                <h2 id=t1>Scores EcoTourism</h1>
                <div class=score> 
                    <div class="score-box">A</div>
                    <div class="trait"></div>
                    <div class="score-box" style="background-color:#BB5C00">D</div>
                    
                </div>
            </div>
        

            <div class="container-stats bg-354F52">
                <h2 id=t1>Indicateurs clés</h1>
                <div class= "flex">
                    <div class=graph id="spider"></div>

                    <table class=p50 style="text-align:center">
                        <tr>
                            <th>USA</th>
                            <th>Indic</th>
                            <th>Chine</th>
                        </tr>
                        <tr>
                            <td>667</td>
                            <td>Test</td>
                            <td>667</td>
                        </tr>
                        <tr>
                            <td>667</td>
                            <td>Test</td>
                            <td>667</td>
                        </tr>
                        <tr>
                            <td>667</td>
                            <td>Test</td>
                            <td>667</td>
                        </tr>
                        <tr>
                            <td>667</td>
                            <td>Test</td>
                            <td>667</td>
                        </tr>
                        <tr>
                            <td>667</td>
                            <td>Test</td>
                            <td>667</td>
                        </tr>
                        <tr>
                            <td>667</td>
                            <td>Test</td>
                            <td>667</td>
                        </tr>
                        

                    </table>
                </div>
            </div>

            <div class="container-stats bg-354F52">
                <h2 id=t1>Comparaison de chaque indicateur</h2>
                <div class=container-buttons>
                    <button onclick="changeVar('co2')" class=button-compare>Émissions de CO2</button>
                    <button onclick="changeVar('pib')" class=button-compare>PIB par habitant</button>
                    <button onclick="changeVar('gpi')" class=button-compare>Indice de paix</button>
                    <button onclick="changeVar('arrivees')" class=button-compare>Arrivées</button>
                    <button onclick="changeVar('departs')" class=button-compare>Départs</button>
                    <button onclick="changeVar('cpi')" class=button-compare>Consumer Price Index</button>
                    <button onclick="changeVar('Enr')" class=button-compare>% énergies renouvellables</button>
                </div>
                <div class= "flex">
                    <div class=p50>
                        
                        <p>Actuellement le [pays 1] est au dessus du [pays 2], montrant que [pays 1] pollue plus que [pays 2]. Au cours du temps on peut voir que le tourisme ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a metus pellentesque massa lacinia scelerisque et nec purus. Proin mattis elementum euismod. Curabitur et felis felis. Donec vel nulla malesuada, tempor nisi in, faucibus nulla. Cras at ipsum tempor, rutrum sapien ut, auctor sapien.
                        Curabitur a metus pellentesque massa lacinia scelerisque et nec purus. Proin mattis elementum euismod. </p>
                        
                    </div>
                    
                    
                    <div class=graph id="chartdiv"></div>

                    
                </div>

            </div>

            <!-- <div class="trait-hori"></div> -->

            <div class="container-stats bg-354F52">
                <h2 id=t1>Croissance des indicateurs</h2>
                <div class= "flex">
                    <div class=graph id="bar"></div>
                    <p class=p50>Actuellement le [pays 1] est au dessus du [pays 2], montrant que [pays 1] pollue plus que [pays 2]. Au cours du temps on peut voir que le tourisme ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a metus pellentesque massa lacinia scelerisque et nec purus. Proin mattis elementum euismod. Curabitur et felis felis. Donec vel nulla malesuada, tempor nisi in, faucibus nulla. Cras at ipsum tempor, rutrum sapien ut, auctor sapien.
                    Curabitur a metus pellentesque massa lacinia scelerisque et nec purus. Proin mattis elementum euismod. </p>
                    
                    
                </div>
            

            </div>
            
            <div class='container-double'>
                <div class="container-stats bg-52796F marg-r">
                    <h2 id=t1>Savez-vous ?</h2>
                    <div id="carousel-wrapper">
                
                        <div id="menu">
                            <div id="current-option">
                                <span id="current-option-text1" data-previous-text="" data-next-text=""></span>
                                <span id="current-option-text2" data-previous-text="" data-next-text=""></span>
                            </div>
                            <button id="previous-option"></button>
                            <button id="next-option"></button>
                        </div>
                        <div id="image"></div>
                    </div>
                </div>    

                <div class="container-stats bg-52796F marg-l">
                    <h2 id=t1>Définitions</h2>
                    
                </div>
                </div>    

            </div>
             
            

            <script src="../assets/js/carousel.js"></script>
        </div>
    
    </div>

    <script>
        
        spider()
        createGraph()
        graphBar()
        createMapCompare(['<?=$pays1?>','<?=$pays2?>'])

    </script>
    
</body>
</html>

