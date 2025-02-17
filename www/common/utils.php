<?php

function query_data($url){
    $headers = array(
        "Accept: */*",
    );
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    if(curl_errno($ch) != 0){
        echo (curl_error($ch)."<br>");
    }
    return $res;
}

function gen_error_page($error=NULL){
    echo "<div><h2>Nous sommes désolés</h2>";
    switch ($error){
        case "no_race":
            echo 'Il semble qu\'il n\'y ai pas de course enregistré 😭</br> Veuillez revenir ultérieurement';
            break;
        case "not_found":
            echo 'Les données de la course séléctionnée n\'ont pas été trouvés sur le serveur 😭</br> Veuillez retrouner à <a href="index.html">l\'écran de selection des courses</a>';
            break;
        default:
            echo 'Les données de la course séléctionnée sont corrompus 😭</br> Veuillez retrouner à <a href="index.html">l\'écran de selection des courses</a>';
            break;
        
    }
    echo "</div>";
    exit();
    
}

function get_race_list($fegaf_requester_url){
    $race_list_raw = query_data($fegaf_requester_url."?ls");
    
    $race_list_regex;
    preg_match_all("/(?:^|\s)([A-Za-zéèàù,\.\ \-0-9]+)(?:\|)(?:[0-9]+|DIR)/", $race_list_raw, $race_list_regex);
    
    $race_list = $race_list_regex[1];

    $race_list = array_filter($race_list, static function ($elem){
        return ($elem != ".") && ($elem != "..");
    });

    $race_list = array_values($race_list); // re-aligning to zero array after fitlering
    return $race_list;
}


function to_utf8($str){
    return array_map(function ($item){return mb_convert_encoding($item, "UTF-8", mb_detect_encoding($item));},$str);
}