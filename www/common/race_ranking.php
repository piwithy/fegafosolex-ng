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


    function safe_echo($msg){
        echo(htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'));
    }
?>

<div id="ranking">
    <span class='back'><a href='index.html'><i class="fa-solid fa-arrow-left-long"></i> Retour</a></span>

    <h2><?php safe_echo(ucfirst($xml->attributes()->plateau). " | " . ucfirst($xml->attributes()->race)) ?></h2>
    <span>Derniere mise à jour du classement : <?php safe_echo($xml->attributes()->timegen) ?></span>
    
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
                    $categoryClass = str_replace("é", "e", str_replace(" ", "_", strtolower(htmlspecialchars($team->teamCategory), ENT_QUOTES, 'UTF-8')));
                    if($team->rang == "") $team->rang = "-";
                    if($categoryClass == "") $categoryClass= "unknown";
                    if($team->teamNumber == "") $team->teamNumber = "000";
                    if($team->teamName == "") $team->teamName = "unknown";
                    if($team->tours == 0){
                        $team->ecartFirst = "N/A";
                    }
                    if($team->lastTime == "") $team->lastTime = "N/A";
                    if($team->bestTime == ""){
                        $bestTimeString = "N/A" ;
                    }else{
                        $bestTimeString = $team->bestTime." (".$team->bestTimeLap.")";
                    }
                    if($team->ecartPrev == "" || $team->ecartPrev == "0:00,00"){
                        $team->ecartPrev = "---";
                    }
                    if($team->ecartFirst == "" || $team->ecartFirst == "0:00,00"){
                        $team->ecartFirst = "---";
                    }
            ?>
            <tr>
                <td data-label="Évolution">
                    <?php
                        if($team->passedRaceStop == 1) echo('<i class="fa-solid fa-flag-checkered themed"></i>');
                        elseif($team->tendance == -1)  echo('<i class="fa-solid fa-angle-up green"></i>');
                        elseif($team->tendance ==  0)  echo('<i class="fa-solid fa-minus turquoise"></i>');
                        elseif($team->tendance ==  1)  echo('<i class="fa-solid fa-angle-down red"></i>');
                        else                           echo('<i class="fa-solid fa-question themed></i>"');
                    ?>
                </td>
                <?php echo("<td data-label='Rang'>".htmlspecialchars($team->rang, ENT_QUOTES, 'UTF-8')."</td>")?>
                <?php echo("<td data-label='N°' class='".$categoryClass."'>".htmlspecialchars($team->teamNumber, ENT_QUOTES, 'UTF-8')."</td>")?>
                <?php echo("<td data-label='Équipage'>".htmlspecialchars(ucfirst($team->teamName), ENT_QUOTES, 'UTF-8')."</td>")?>
                <?php echo("<td data-label='Nombre de Tours'>".$team->tours."</td>")?>
                <?php echo("<td data-label='Meilleur Temps'>".$bestTimeString."</td>")?>
                <?php echo("<td data-label='Dernier Temps'>&nbsp;".$team->lastTime."</td>")?>
                <?php echo("<td data-label='Ecart Précédent'>".$team->ecartPrev."</td>")?>
                <?php echo("<td data-label='Ecart Premier'>".$team->ecartFirst."</td>")?>
            </tr>

            <?php
                }
            ?>
        </tbody>
    </table>
    <span>Dernier rafraichissement des données : <?php safe_echo($now) ?></span>
</div>