<?php
class pTools_jutil extends pToolsModule
{
  public function __construct()
  {
  }
  
  public function __destruct()
  {
  }
  
  public function checkDependencies()
  {
    pTools_Assert(pTools::checkModuleVersion("navigatorCompat", "1.00"), __FILE__, __LINE__, "pTools/jutil ".$this->getVersion()." requires <b>pTools/navigatorCompat 1.00</b> or compatible");
  }

  public function getDescription()
  {
    return "jutil - Javascript utils (fifo, htmlspecialchars, getCookie, setCookie, trim)";
  }
  
  public function getVersion()
  {
    return "1.05";
  }
  
  public function loadJavascript($relativePathFromInclusion)
  {
    CHTML::head_script($relativePathFromInclusion."jutil/util.js");
    CHTML::head_script($relativePathFromInclusion."jutil/fifo.js");
    CHTML::head_script($relativePathFromInclusion."jutil/UTF8CoderDecoder.js");
  }
}
?>