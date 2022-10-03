<?php 
    include 'remoteUrls.php';
    include 'utils.php';
    
    $lastmod_race = query_data($fegaf_resquester_url."?ls_lastmod");

    $race_list_raw = query_data($fegaf_resquester_url."?ls");
    
    $race_list = get_race_list($fegaf_resquester_url);
    if(empty($race_list)) gen_error_page("no_race");
?>



<div id="selector">
    <h2>Sélectionnez une course</h2>
    <br>
    <form action="index.html" method="GET"> 
        <select id="raceSelector" name="race">
            <option value="lastmod">(Course&nbsp;Active)</option>
            <?php
                $array_length = count($race_list);
                echo($array_length);
                for($i=0 ; $i<$array_length ; $i++){
                    $val = $race_list[$i];
                    $noext_val = substr($val, 0, -4);
                    if($val == $lastmod_race) $noext_val .= " (Active)";
                    print("<option value='$val'>$noext_val</option>");
                }
            ?>
            <!-- TODO add PHP Generated selection -->
        </select>
        <input type="submit" value="Ok">
    </form>
</div>