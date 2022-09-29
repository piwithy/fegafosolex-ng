<?php
require("xmlnode.php");
require("xmlFunctions.php");
require("xmlparser.php");

class pTools_xml extends pToolsModule
{
  public function __construct()
  {
  }
  
  public function __destruct()
  {
  }

  public function getDescription()
  {
    return "A simple XML Parser";
  }
  
  public function getVersion()
  {
    return "1.04";
  }
}
?>