<?php
class pTools_navigatorCompat extends pToolsModule
{
  public function __construct()
  {
  }
  
  public function __destruct()
  {
  }
  
  public function checkDependencies()
  {
    pTools_Assert(pTools::checkModuleVersion("html", "1.01"), __FILE__, __LINE__, "pTools/navigatorCompat ".$this->getVersion()." requires <b>pTools/html 1.01</b> or compatible");
  }

  public function getDescription()
  {
    return "JavaScript Tools for Navigator Compatibility (XmlHttpRequest, DOMParser, XSLT, formular, events, data islands)";
  }
  
  public function getVersion()
  {
    return "1.53";
  }
  
  public function loadJavascript($relativePathFromInclusion)
  {
    if (file_exists($relativePathFromInclusion."navigatorCompat/_pToolsnavigatorCompat.js")) //release
    {
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/_pToolsnavigatorCompat.js");
    }
    else
    {
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/ncXmlHttpRequest.js");
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/ncDOMParser.js");
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/ncNotDOMGetter.js");
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/DOM.js");
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/events.js");
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/navigator.js");
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/XSLT.js");
      CHTML::head_script($relativePathFromInclusion."navigatorCompat/dataIslands.js");
    }
  }
}
?>