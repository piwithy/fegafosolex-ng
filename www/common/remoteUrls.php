<?php
$FEGAF_REMOTE = getenv("FEGAF_REQUEST_ROOT");
if(!$FEGAF_REMOTE || strtolower($FEGAF_REMOTE) == "local"){
    $FEGAF_REMOTE = "nginx";
}
$fegaf_resquester_url = $FEGAF_REMOTE."/fegafosolexRequest.php";
$fegaf_data_url= $FEGAF_REMOTE."/data/";

?>