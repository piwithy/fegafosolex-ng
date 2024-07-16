<?php 
    include 'remoteUrls.php';
    include 'utils.php';
    
    $lastmod_race = query_data($fegaf_resquester_url."?ls_lastmod");

    $race_list_raw = query_data($fegaf_resquester_url."?ls");
    
    $race_list = get_race_list($fegaf_resquester_url);
    if(empty($race_list)) gen_error_page("no_race");
?>



<div class="cards-container">
    <div class="card featured">
        <a href="index.html?race=lastmod">
            <div class="container">
                <h4><b>Course Active</b></h4>
            </div>
        </a>
    </div>
    <?php
        $array_length = count($race_list);
        for ($i=0; $i<$array_length; $i++){
            $val = $race_list[$i];
            $noext_val = substr($val, 0, -4);
            if ($val == $lastmod_race){
                print("<div class='card featured'>");
            }else{
                print("<div class='card'>");
            }
            print("<a href='index.html?race=$val''>")
            ?>
                    <div class="container">
                        <?php
                        print("<h4><b>$noext_val</b></h4>")
                        ?>
                    </div>
                </a>
            </div>
            <?php
        }
    ?>
</div>

