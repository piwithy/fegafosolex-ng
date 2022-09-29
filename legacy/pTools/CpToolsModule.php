<?php
class pToolsModule
{
  public function getDescription()
  {
    return "pToolsModule";
  }
  
  public function getVersion()
  {
    return ""; // "" = NOT INSTALLED
  }
  
  public function isInstalled()
  {
    return ($this->getVersion() != "");
  }

  public function checkModuleVersion($youneedversion)
  {
    return ($this->getVersion() >= $youneedversion);
  }
  
  public function loadJavascript($relativePathFromInclusion) /* function to overload if the module has javascript features */
  {
  }
  
  public function checkDependencies()
  {
  }
}
?>