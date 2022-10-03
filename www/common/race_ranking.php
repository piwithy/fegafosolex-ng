<?php
    include 'remoteUrls.php';
    include 'utils.php';

    if(!isset($_GET['race'])){
        gen_error_page();
    }

    $data_url;

    $race_list = get_race_list($fegaf_resquester_url);

    if($_GET['race'] == "lastmod"){
        //checking if there is an active race to display
        if(empty($race_list)) gen_error_page("not_found");
        $race = query_data($fegaf_resquester_url."?ls_lastmod");
        $data_url = $fegaf_data_url. $race;
    } else {
        // cheking if the requested race exists
        if(!in_array($_GET['race'], $race_list)){
            gen_error_page("not_found");
        }
        $data_url = $fegaf_data_url.$_GET['race'];
    }
    $data_url = str_replace(" ", "%20", $data_url);

    $race_data = query_data($data_url);

    //trying to read XML Data
    $xml = simplexml_load_string($race_data);
        
    if($xml == false){ // IF there is encoding error force UTF-8
        $race_data_utf8 = utf8_encode($race_data);
        $xml = simplexml_load_string($race_data_utf8);
    }

    if($xml == false) {
        gen_error_page();
    }

    date_default_timezone_set('Europe/Paris');

    $now = date('d/m/Y H:i:s T');
?>

<div id="ranking">
    <span class='back'><a href='index.html'><i class="fa-solid fa-arrow-left-long"></i> Retour</a></span>

    <h2><?php echo(ucfirst($xml->attributes()->plateau). " | " . ucfirst($xml->attributes()->race)) ?></h2>
    <span>Derniere mise à jour du classement : <?php echo($xml->attributes()->timegen) ?></span>
    
    <table>
        <thead>
            <tr>
                <th scope="col">Évol.</th>
                <th scope="col">Rang</th>
                <th scope="col">N°</th>
                <th scope="col">Équipage</th>
                <th scope="col">Nbr. Tours</th>
                <th scope="col">Meilleur Temps (Tour)</th>
                <th scope="col">Dernier Temps</th>
                <th scope="col">Écart Précédent</th>
                <th scope="col">Écart 1<sup>er</sup></th>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach($xml->result as $result){
                    $team = $result->attributes();
                    $categoryClass = str_replace("é", "e", str_replace(" ", "_", strtolower($team->teamCategory)));
                    if($categoryClass == "") $categoryClass= "unknown";
                    if($team->tours == 0){
                        $team->bestTime = "N/A";
                        $team->bestTimeLap = "N/A";
                        $team->lastTime = "N/A";
                        $team->ecartPrev = "N/A";
                        $team->ecartFirst = "N/A";

                    }
            ?>
            <tr>
                <td data-label="Évolution">
                    <?php
                        if($team->passedRaceStop == 1) echo '<i class="fa-solid fa-flag-checkered themed"></i>';
                        elseif($team->tendance == -1) echo '<i class="fa-solid fa-angle-up green"></i>';
                        elseif($team->tendance == 0) echo '<i class="fa-solid fa-minus turquoise"></i>';
                        else echo '<i class="fa-solid fa-angle-down red"></i>';
                    ?>
                </td>
                <?php echo "<td data-label='Rang'>".$team->rang."</td>"?>
                <?php echo "<td data-label='N°' class='".$categoryClass."'>".$team->teamNumber."</td>"?>
                <?php echo "<td data-label='Équipage'>".ucfirst($team->teamName)."</td>"?>
                <?php echo "<td data-label='Nombre de Tours'>".$team->tours."</td>"?>
                <?php echo "<td data-label='Meilleur Temps (Tour)'>".$team->bestTime." (".$team->bestTimeLap.")"."</td>"?>
                <?php echo "<td data-label='Dernier Temps'>".$team->lastTime."</td>"?>
                <?php 
                    if($team->ecartPrev != "0:00,00") echo "<td data-label='Ecart Précédent'>".$team->ecartPrev."</td>";
                    else echo "<td data-label='Ecart Précédent'>---</td>"
                ?>
                <?php 
                    if($team->ecartFirst != "0:00,00") echo "<td data-label='Ecart Premier'>".$team->ecartFirst."</td>";
                    else echo "<td data-label='Ecart Premier'>---</td>"
                ?>

                
            </tr>

            <?php
                }
            ?>
        </tbody>
    </table>
    <span>Dernier rafraichissement des données : <?php echo $now ?></span>
</div>