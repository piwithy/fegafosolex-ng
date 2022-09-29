<?php
global $CHTML_CHOSEN_CHARSET;

class CHTML
{
  public static function toHTML($txt, $lineFeedProcessingType="html")
  {
    global $CHTML_CHOSEN_CHARSET;
    $hsc = htmlspecialchars($txt, ENT_COMPAT, $CHTML_CHOSEN_CHARSET);
		
		if ($lineFeedProcessingType=="")
			return $hsc;															 // no processing at all: textarea
		
		if ($lineFeedProcessingType=="html")
			return str_replace("\n", "<br/>\n", $hsc); // free text inside html page (DEFAULT)

		if ($lineFeedProcessingType=="value")
			return str_replace("\n", "\\n", $hsc); 		 // inside a 'value' of input box, or alert box in javascript

		pTools_assert(0, __FILE__, __LINE__, "CHTML::toHTML bad 'lineFeedProcessingType' parameter");
		return $hsc;
  }
  
	/* ATTENTION 
   * "windows-1252" (ou "ansi" mais le nommage n'est pas standard) car est le défaut utilisé par Windows, OS que j'utilise.
     Langues européennes utilisables: Français, Anglais, Allemand, Espagnol, Italien, Finlandais, Danois, Hollandais, ....
     Européennes non reconnues: Hongrois, Tchèque
   * ISO-8859-1 ne contient pas le symbole "euro", et doit etre normalement remplacé par ISO-8859-15. 
     Mais tous les éditeurs ne ne supportent pas l'ISO-8859-15 (notepad2) ou pas facilement accessible dans les menus (notepad++)
     A noter cependant que sous HTML 5.0, le ISO-8859-1 est interprété comme windows-1252 (cf Wikipedia) et est donc un jeu
     de caracteres international reconnu, et supportant le symbole "euro".
   * UTF-8 devrait normalement etre choisi car dans ce cas on peut gérer tous les caractères, dont les asiatiques ...
  */
  public static function prolog_XHTML($encoding="windows-1252", $withHTTPHeader=1)
  {
		global $CHTML_CHOSEN_CHARSET;
    /* Defaults for PI's Web site is XHTML 1.0 Transitional */
    if ($withHTTPHeader)
      header("Content-Type: text/html; charset={$encoding}");
		$CHTML_CHOSEN_CHARSET = $encoding;
    echo "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>\n";
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    echo "<html lang=\"fr\" dir=\"ltr\" xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  }
  
  public static function head_types($charset="windows-1252")
  {
    /* Should be deprecated if using XHTML because given in <?xml ?> prolog, and
       type text/css given at each head_stylesheet PHP function (see below) 
       */
    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset={$charset}\" />\n";
    echo "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n";
  }
  
  public static function head_nocache()
  {
    /* http-equiv seems to be linked to header of protocol HTTP/1.1 . Does apache intercept this ???? */
    echo "<meta http-equiv=\"cache-control\" content=\"no-cache\" />\n";
    echo "<meta http-equiv=\"pragma\" content=\"no-cache\" />\n"; /* not really used (to check */
    echo "<meta http-equiv=\"expires\" content=\"0\" />\n"; /* most supported RFC 2068 (HTTP 1.1) */
  }
  
  public static function head_description($desc)
  {
    echo "<meta name=\"description\" content=\"".CHTML::toHTML($desc, "value")."\" />\n";
  }

  public static function head_keywords($kw)
  {
    echo "<meta name=\"keywords\" content=\"".CHTML::toHTML($kw, "value")."\" />\n";
  }
  
  public static function head_stylesheet_default()
  {
    $pToolsRelativePath = pTools::getpTools()->getRelativePathFromInclusion();
    echo "<link rel=\"stylesheet\" href=\"".CHTML::toHTML($pToolsRelativePath, "value")."html/pToolsDefault.css\" type=\"text/css\" />\n";
    echo "<link rel=\"stylesheet\" href=\"".CHTML::toHTML($pToolsRelativePath, "value")."html/pToolsClassicStyles.css\" type=\"text/css\" />\n";
  }
  
  public static function head_stylesheet($css)
  {
    echo "<link rel=\"stylesheet\" href=\"".CHTML::toHTML($css, "value")."\" type=\"text/css\" />\n";
  }
  
  public static function head_script($src)
  {
    echo "<script type=\"text/javascript\" src=\"".CHTML::toHTML($src, "value")."\"></script>\n";
  }
  
  public static function javascript($code)
  {
    echo "<script type=\"text/javascript\">".$code."</script>\n";
  }
  
  public static function javascript_redir($url, $timeoutInSeconds=0)
  {
		if ($timeoutInSeconds==0)
			self::javascript("document.location=\"{$url}\";");
		else
		{
			self::javascript("setTimeout(function() { document.location=\"{$url}\"; }, ".($timeoutInSeconds * 1000).");");
		}
  }
}
?>