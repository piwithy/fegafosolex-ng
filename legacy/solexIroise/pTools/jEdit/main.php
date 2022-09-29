<?php
class pTools_jEdit extends pToolsModule
{
  public function __construct()
  {
  }
  
  public function __destruct()
  {
  }
  
  public function checkDependencies()
  {
    pTools_Assert(pTools::checkModuleVersion("navigatorCompat", "1.05"), __FILE__, __LINE__, "pTools/jEdit ".$this->getVersion()." requires <b>pTools/navigatorCompat 1.05</b> or compatible");
    pTools_Assert(pTools::checkModuleVersion("jutil", "1.01"), __FILE__, __LINE__, "pTools/jEdit ".$this->getVersion()." requires <b>pTools/jutil 1.01</b> or compatible");
  }

  public function getDescription()
  {
    return "jEdit - Javascript Text Editor";
  }
  
  public function getVersion()
  {
    return "0.4";
  }
  
  public function loadJavascript($relativePathFromInclusion)
  {
    CHTML::javascript("var jEditor_Path=\"".$relativePathFromInclusion."jEdit/\";");
    if (file_exists($relativePathFromInclusion."jEdit/_pToolsjEdit.js")) // release
    {
      CHTML::head_script($relativePathFromInclusion."jEdit/_pToolsjEdit.js");
    }
    else //debug
    {
      CHTML::head_script($relativePathFromInclusion."jEdit/jEditor.js");
      CHTML::head_script($relativePathFromInclusion."jEdit/jAction.js");
      CHTML::head_script($relativePathFromInclusion."jEdit/jExpression.js");
    }
  }
}
?>