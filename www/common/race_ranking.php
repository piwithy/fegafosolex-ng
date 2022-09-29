<?php
    include 'remoteUrls.php';
    if(isset($_GET['race'])){
        $headers = array(
            "Accept: */*",
        );
        if($_GET['race'] == "lastmod"){
            $curl=curl_init($fegaf_resquester_url."?ls_lastmod");
            curl_setopt($curl, CURLOPT_URL, $fegaf_resquester_url."?ls_lastmod");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $res = curl_exec($curl);
            curl_close($curl);
            $fegaf_data_url .=$res; 
        }else{
            $fegaf_data_url .= $_GET['race'];
        }
        
        $fegaf_data_url = str_replace(" ", "%20", $fegaf_data_url);
        //echo $fegaf_data_url;

        $curl = curl_init($fegaf_data_url);
        curl_setopt($curl, CURLOPT_URL, $fegaf_data_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        //for debug only!
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $xml = simplexml_load_string($resp);
        
        if($xml == false){ // IF there is encoding error force UTF-8
            $resp_utf8 = utf8_encode($resp);
            $xml = simplexml_load_string($resp_utf8);
        }
        if($xml != false){

        //print_r($xml);
?>

<div id="ranking">
    <h2><?php echo($xml->attributes()->plateau. " | " . $xml->attributes()->race) ?></h2>
    <span>Derniere mise à jour du classement : <?php echo($xml->attributes()->timegen) ?></span>
    
    <table>
        <thead>
            <tr>
                <th scope="col">Tend.</th>
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
                    $categoryClass = str_replace("é", "e", str_replace(" ", "_", $team->teamCategory));
                    if($team->tours == 0){
                        $team->bestTime = "N/A";
                        $team->bestTimeLap = "N/A";
                        $team->lastTime = "N/A";
                        $team->ecartPrev = "N/A";
                        $team->ecartFirst = "N/A";

                    }
            ?>
            <tr>
                <td data-label="Tendence">
                    <?php
                        if($team->passedRaceStop == 1) echo '<i class="fa-solid fa-flag-checkered"></i>';
                        elseif($team->tendance == -1) echo '<i class="fa-solid fa-angle-up green"></i>';
                        elseif($team->tendance == 0) echo '<i class="fa-solid fa-minus turquoise"></i>';
                        else echo '<i class="fa-solid fa-angle-down red"></i>';
                    ?>
                </td>
                <?php echo "<td data-label='Rang'>".$team->rang."</td>"?>
                <?php echo "<td data-label='N°' class='".$categoryClass."'>".$team->teamNumber."</td>"?>
                <?php echo "<td data-label='Équipage'>".$team->teamName."</td>"?>
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
</div>

<?php
        }else{
?>
        <div>
            <h2>Nous sommes désolés</h2>
	        Les données de la course séléctionnée sont corrompus 😭</br> 
            Veillez retrouner à <a href="index.html">l'écran de selection des courses</a>
        </div>
<?php
        }
    }
?>