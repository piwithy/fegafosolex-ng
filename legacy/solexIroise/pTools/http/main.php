<?php
require("HTTPStructures.php");
require("uri_functions.php");
require("http_functions.php");

class pTools_http extends pToolsModule
{
  public function __construct()
  {
  }
  
  public function __destruct()
  {
  }

  public function getDescription()
  {
    return "Accessing an HTTP external web site. Features : GET, POST, proxy";
  }
  
  public function getVersion()
  {
    return "1.07";
  }
}
?>