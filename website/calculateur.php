<?php require_once "head.php"; ?>

<body>

    <div class="flex">
        <?php
            // Générer un nouveau token CSRF si la variable de session n'existe pas
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        ?>

        <script>
            function getValues() {
                console.log(htmx.values(htmx.find("#calc")));
            }
        </script>
        <div class="zone-totale" id="zones">
            <div class="left-section">
                <h1 class="titre">Prévoyez vos prochaines vacances</h1>
                <form name="Calculateur" id="calc">

                    <div class="dual-input">
                        <div class="container-input">
                            <label for="country_src">Pays de départ</label>
                            <input type="text" id="country_src" name="country_src" placeholder="Saisissez un pays" required autocomplete="off"
                            hx-get="scripts/htmx/listPays.php" hx-trigger="keyup[this.value.trim().length > 0] changed delay:0.5s" hx-vals='js:{search: getSearchValue("country_src"), sens:"src"}'>
                            <div id="country_options_src" class="option-container"></div>
                        </div>

                        <div class="container-input">
                            <label for="city_src">Ville de départ</label>
                            <input type="text" id="city_src" name="city_src" placeholder="Saisissez une ville" required disabled autocomplete="off">
                            <div id="city_options_src" class="option-container"></div>
                        </div>
                    </div>
                    <div class="dual-input">
                        <div class="container-input">
                            <label for="country_dst">Pays d'arrivée</label>
                            <input type="text" id="country_dst" name="country_dst" placeholder="Saisissez un pays" required autocomplete="off"
                            hx-get="scripts/htmx/listPays.php" hx-swap="none" hx-trigger="keyup[this.value.trim().length > 0] changed delay:0.5s" hx-vals='js:{search: getSearchValue("country_dst"), sens:"dst"}'>
                            <div id="country_options_dst" class="option-container"></div>
                        </div>

                        <div class="container-input">
                            <label for="city_dst">Ville d'arrivée</label>
                            <input type="text" id="city_dst" name="city_dst" placeholder="Saisissez une ville" required autocomplete="off" disabled>
                            <div id="city_options_dst" class="option-container"></div>
                        </div>
                    </div>

                    <div class="dual-input">
                        <div class="container-input">
                            <label for="departure-date">Date de départ</label>
                            <input type="date" id="departure-date" name="departure-date" placeholder="Saisissez une date" required autocomplete="off">
                        </div>

                        <div class="container-input">
                            <label for="arrival-date">Date de retour</label>
                            <input type="date" id="arrival-date" name="arrival-date" placeholder="Saisissez une date" required autocomplete="off">
                        </div>
                    </div>

                    <!-- Travel options (Airports if plane, train stations...) -->
                    <div class="dual-input" id="travel-options"></div>

                    <div class="dual-input">

                        <div class="container-input">
                            <label for="country">Nombre de voyageurs</label>
                            <input type="number" id="passengers" name="passengers" placeholder="Saisissez un nombre" required autocomplete="off" value=2 min=1 max=69>
                        </div>
                    </div>
                    <input type="hidden" id="CSRF" name="CSRF" value="<?= $_SESSION["csrf_token"] ?>">

                    <input type="submit" value="Calculer" class="submit" hx-get="kek.php" hx-target="#calculateur-right-section" hx-select="#calculateur-right-section" hx-swap="outerHTML" hx-vals="js:{data:getValues()}">

                </form>
            </div>

            <div class="big-trait"></div>

            <div class="right-section" id="calculateur-right-section">
                <p>Bienvenue sur notre puissant calculateur de trajet et de séjour, votre compagnon de voyage ultime. Vous êtes en quête de l'expérience de voyage parfaite? Notre outil offre une approche inégalée pour planifier vos aventures.</p>
                <p>Pour chaque destination, choisissez entre train, avion et voiture pour découvrir les itinéraires les plus adaptés. Obtenez des estimations précises des coûts, du temps de trajet et de l'empreinte carbone, vous aidant à prendre des décisions éclairées pour des voyages durables et abordables. De plus, avec la possibilité d'ajouter la durée de votre séjour, vous obtiendrez une estimation du prix total de votre aventure.</p>
                <p>Préparez-vous à explorer le monde en toute confiance, grâce à des informations complètes pour des voyages inoubliables.</p>
            </div>

        </div>

        <div class="zone mask"></div>

        <div id="nav-bot" hx-swap-oob="outerHTML"></div>

        <script id="scripting" hx-swap-oob="outerHTML"></script>
        <script id="orders" hx-swap-oob="outerHTML"></script>
        <script id="behave" hx-swap-oob="outerHTML"></script>
        <div id="htmxing" hx-swap-oob="outerHTML"></div>

    </div>

</body>
