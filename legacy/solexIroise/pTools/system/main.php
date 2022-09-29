<?php
require("CSemaphore.php");
class pTools_system extends pToolsModule
{
  private static $systemCharset;
  
  public function __construct()
  {
    self::$systemCharset = "windows-1252";
  }
  
  public function __destruct()
  {
  }

  public function getDescription()
  {
    return "\"Background OS onto PHP runs\" utility. Features : semaphore, utf8ToSystem (filesystem charset conversion)";
  }
  
  public function getVersion()
  {
    return "1.00";
  }
  
  public static function setSystemCharset($charset)
  {
    self::$systemCharset = $charset;
  }
  public static function getSystemCharset()
  {
    return self::$systemCharset;
  }
}

function utf8ToSystem($x)
{
  /* D'un point de vue CHARSET, les fonctions PHP (readdir...) renvoient tel quel ce que le systeme lui donne:
   Sous windows, du windows-1252
   Sous mon serveur linux ??? */
  return iconv("utf-8", pTools_system::getSystemCharset(), $x);
}
function systemToUtf8($x)
{
  /* D'un point de vue CHARSET, les fonctions PHP (readdir...) renvoient tel quel ce que le systeme lui donne:
   Sous windows, du windows-1252
   Sous mon serveur linux ??? */
  return iconv(pTools_system::getSystemCharset(), "utf-8", $x);
}
?>