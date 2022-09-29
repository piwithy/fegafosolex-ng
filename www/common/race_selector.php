<?php 
    include 'remoteUrls.php';

    $headers = array(
        "Accept: */*",
    );

    $curl = curl_init($fegaf_resquester_url."?ls_lastmod");
    curl_setopt($curl, CURLOPT_URL,$fegaf_resquester_url."?ls_lastmod");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $lastmod_race = curl_exec($curl);
    curl_close($curl);

    $curl = curl_init($fegaf_resquester_url."?ls");
    curl_setopt($curl, CURLOPT_URL,$fegaf_resquester_url."?ls");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($curl);
    curl_close($curl);
    
    $races;
    preg_match_all("/(?:^|\s)([A-Za-z\.\ \-0-9]+)(?:\|)(?:[0-9]+|DIR)/", $resp, $races);
?>



<div id="selector">
    <h2>Sélectionnez une course</h2>
    <br>
    <form action="index.html" method="GET"> 
        <select id="raceSelector" name="race">
            <option value="lastmod">(Course&nbsp;Active)</option>
            <?php
                $array_length = count($races[1]);
                //print($array_length);
                for($i=1 ; $i<$array_length ; $i++){
                    $val = $races[1][$i];
                    $noext_val = substr($val, 0, -4);
                    if($val == $lastmod_race) $noext_val .= " (Active)";
                    //$nospace_val= str_replace(" ", "%20", $val);
                    if($val != "." && $val != ".."){
                        print("<option value='$val'>$noext_val</option>");
                    }
                }
            ?>
            <!-- TODO add PHP Generated selection -->
        </select>
        <input type="submit" value="Ok">
    </form>
</div>