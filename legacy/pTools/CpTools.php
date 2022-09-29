<?php
define('PTOOLS_VERSION', "1.70");
define('PTOOLS_BUILD', "1601");
define('PTOOLS_CUSTOMNAME', "");

global $mainpToolsObject;
$mainpToolsObject=null;

class pTools
{
  private $modulecount;
  private $module;
  private $relativePathFromInclusion;

  private function __construct($relativePathFromInclusion=".")
  {
    global $mainpToolsObject;
    $mainpToolsObject = $this;
    $this->relativePathFromInclusion = $relativePathFromInclusion;
    if ($this->relativePathFromInclusion[strlen($this->relativePathFromInclusion)-1]!='/') $this->relativePathFromInclusion.="/";
    $this->modulecount=0;
    
    // Including all modules
    $rid = opendir(pTools_GetAbsoluteServerPath());
    while ($entry = readdir($rid)) 
    {
      if (($entry != ".") && ($entry != "..")) 
      {
        $path = pTools_GetAbsoluteServerPath().$entry;
        if (is_dir($path)) 
        {
          $path .= "/main.php";
          if (file_exists($path)) 
          {
            require($path);
            $this->module[$this->modulecount]['name'] = $entry;
            $moduleclassname = "pTools_$entry";
            $this->module[$this->modulecount]['instance'] = new $moduleclassname;
            $this->modulecount++;
          }
        }
      }
    }
    closedir($rid);
    
    /* Now check for dependencies */
    for ($a=0; $a < $this->modulecount; $a++) 
    {
      $mod = $this->module[$a]['instance'];
      if ($mod->isInstalled())
      {
        $mod->checkDependencies();
      }
    }
  }
  
  public function __destruct() // always public
  {
    for ($a=0; $a < $this->modulecount; $a++) 
    {
      unset($this->module[$a]['instance']);
    }
  }
  
  /* PRIVATE FUNCTIONS */
  private function nonStaticAbout()
  {
    ?>
    <html>
    <head><title>..:: PI's PHP Tools ::..</title></head>
    <body>
    <fieldset>
    <legend><b>pTools General Informations</b></legend>
    <b>..:: PI's PHP Tools ::..</b> V<?php echo $this->getFullVersionString(); ?><br/>
    pTools is a PHP Functions library whose goal is to gather most common PHP functions and aims to become the core
    of all sites&scripts managed by PI.<br/>
    Contact <a href="mailto:changelog.pub.mdu@pionet.fr">PI</a> for information and/or support.<br/>
    </fieldset>
    <br/>
    <fieldset>
    <legend><b>Installed modules</b></legend>
    <?php
    for ($a=0; $a < $this->modulecount; $a++) 
    {
      $mod = $this->module[$a]['instance'];
      if ($mod->isInstalled())
      {
        // Bypass other directories not included ...
        echo "<b>".$this->module[$a]['name']." : </b> Version ".$mod->getVersion();
        $desc = $mod->getDescription();
        if ($desc != "") echo " - ".$desc;
        echo "<br/>";
      }
    }
    ?>
    </fieldset>
    <br/>
    <br/>
    <?php echo "<center><a href=\"javascript:history.back(1);\">Back</a></center>"; ?>
    </body>
    </html>
    <?php
    exit;  
  }

  private function nonStaticCheckVersion($ver, $build)
  {
   return (($this->getVersion() >= $ver) && ($this->getBuild() >= $build));
   // Note: Non-backward compatible changes will be checked there
   // by testing module versions ...
  }
  
  /* PUBLIC FUNCTIONS */
  public static function about()
  {
    $pt = pTools::getpTools();
    $pt->nonStaticAbout();
  }
  
  public function getVersion()
  {
    return PTOOLS_VERSION;
  }
  
  public function getBuild()
  {
    return PTOOLS_BUILD;
  }
 
  public function getFullVersionString()
  {
    return $this->getVersion().".".$this->getBuild().PTOOLS_CUSTOMNAME;
  }
  
  public function getModuleFromName($modulename)
  {
    // Search the module in all loaded modules
    for ($a=0; $a < $this->modulecount; $a++) 
    {
      if ($this->module[$a]['name'] == $modulename) 
      {
        return $this->module[$a]['instance'];
      }
    }
    return null;
  }
  
  public function loadJavascript()
  {
    for ($a=0; $a < $this->modulecount; $a++) 
    {
      $mod = $this->module[$a]['instance'];
      if ($mod->isInstalled())
      {
        $mod->loadJavascript($this->relativePathFromInclusion);
      }    
    }
  }
  
  public function getRelativePathFromInclusion()
  {
	return $this->relativePathFromInclusion;
  }
  
  /* shortcut */
  public static function checkVersion($ver, $build)
  {
    return pTools::getpTools()->nonStaticCheckVersion($ver, $build);
  }
  
  /* shortcut */
  public static function checkModuleVersion($moduleName, $version)
  {
    $mod = pTools::getpTools()->getModuleFromName($moduleName);
    if (is_null($mod)) return false;
    else return $mod->checkModuleVersion($version);
  }
  
  /* should be used */
  public static function getpTools($relativePathFromInclusion=".")
  {
    global $mainpToolsObject;
    if (is_null($mainpToolsObject)) 
    {
      $mainpToolsObject = new pTools($relativePathFromInclusion);
    }
    return $mainpToolsObject;
  }

  /* kept for backward compatibility */
  public static function initialize($relativePathFromInclusion=".")
  {
    pTools::getpTools($relativePathFromInclusion);
  }
}

// Special
if (isset($_GET['pToolsInfos'])) 
{
  pTools::about();
}?>