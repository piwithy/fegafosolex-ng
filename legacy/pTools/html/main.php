<?php
pTools_Assert(!get_magic_quotes_gpc(), __FILE__, __LINE__, "As a replacement of old 'string' module, 'html' module now ENSURE THAT !get_magic_quotes_gpc() MUST NOT BE ENABLED. This is a buggy false-security blocking stuff. Whatever, this is disabled in PHP6.");

require("CHTML.php");
class pTools_HTML extends pToolsModule
{
  public function __construct()
  {
  }
  
  public function __destruct()
  {
  }

  public function getDescription()
  {
    return "HTML Generation common functions. Table Editor.";
  }
  
  public function getVersion()
  {
    return "1.23";
  }

  public function loadJavascript($relativePathFromInclusion)
  {
    CHTML::head_script($relativePathFromInclusion."html/CHTMLTable.js");
		CHTML::head_script($relativePathFromInclusion."html/TableEditor.js");
	}
}
?>