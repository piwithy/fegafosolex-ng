<?php
class xmlfunc
{
  private $cLastError;
  private $cinCurrentLine;
  
  private function setLastError($msg)
  {
    $this->cLastError = $msg;
  }
  
  public function __construct()
  {
    xmlfunc::reset();
  }
  
  public function __destruct()
  {
  }
  
  public function reset()
  {
    $this->cLastError = "";
    $this->cinCurrentLine = 1;
  }
  
  public function getLastError()
  {
    return $this->cLastError;
  }
  
  public function getCurrentLine()
  {
    return $this->cinCurrentLine;
  }

  // http://www.w3.org/TR/xml-stylesheet/
  public function isStyleSheetPI($string, &$endpos)
  {
    if (strlen($string) < strlen("<?xml-stylesheet?>")) return false;
    $pos = substr($string, strlen("<?xml-stylesheet"));
    // pas implemente
    $pos2 = strstr($pos, "?>");
    if (!$pos2) return false;
    $endpos = substr($pos2,2);
    return true;
  }


  // http://www.w3.org/TR/REC-xml/#NT-XMLDecl
  public function isXMLDecl($string, &$endpos)
  {
    if (strlen($string) < strlen("<?xml?>")) return false;
    if (strstr($string, "<?xml") != $string) return false;
    $pos = substr($string, strlen("<?xml"));
    //var $epos;
    if (!xmlfunc::isVersionInfo($pos, $epos)) return false;
    xmlfunc::isEncodingDecl($epos, $epos); // facultative.
    xmlfunc::isSDDecl($epos, $epos); // facultative
    if (xmlfunc::isS($epos[0])) $epos=substr($epos,1);
    if (strstr($epos, "?>") != $epos) return false;
    $endpos = substr($epos,2);
    return true;
  }

  // http://www.w3.org/TR/REC-xml/#NT-doctypedecl
  public function isdoctypedecl($string, &$endpos)
  {
    // Partially implemented
    if (strlen($string) < strlen("<!DOCTYPE>")) return false;
    if (strstr($string, "<!DOCTYPE") == $string) {
      // Search the end
      $pos = strstr($string, ">");
      if (!$pos) return false;
      $endpos=substr($pos,1);
      return true;
    }
    return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-VersionInfo
  public function isVersionInfo($string, &$endpos)
  {
    $pos = $string;
    if (!xmlfunc::isS($pos[0])) return false;
    $pos=substr($pos,1);
    if (strstr($pos, "version") != $pos) return false;
    $pos = substr($pos,strlen("version"));
    if (!xmlfunc::isEq($pos, $pos)) return false;
    if ($pos[0] == '"') {
      $pos=substr($pos,1);
      if (xmlfunc::isVersionNum($pos, $pos)) {
        if ($pos[0] == '"') {
          $endpos = substr($pos,1);
          return true;
        }
      }
      return false;
    }
    else if ($pos[0] == '\'') 
		{
      $pos=substr($pos,1);
      if (xmlfunc::isVersionNum($pos, $pos)) 
			{
        if ($pos[0] == '\'') 
				{
          $endpos = substr($pos,1);
          return true;
        }
      }
      return false;
    }
    return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-VersionNum
  public function isVersionNum($string, &$endpos)
  {
    if (strstr($string, "1.0") == $string) {
      $endpos = substr($string,strlen("1.0"));
      return true;
    }
    else return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-EncodingDecl
  public function isEncodingDecl($string, &$endpos)
  {
    $pos = $string;
    if (!xmlfunc::isS($pos[0])) return false;
    $pos = substr($pos,1);
    if (strstr($pos, "encoding") != $pos) return false;
    $pos = substr($pos, strlen("encoding"));
    if (!xmlfunc::isEq($pos, $pos)) return false;
    if ($pos[0] == '"') {
      $pos = substr($pos,1);
      if (xmlfunc::isEncName($pos, $pos)) {
        if ($pos[0] == '"') {
          $endpos = substr($pos,1);
          return true;
        }
      }
      return false;
    }
    else if ($pos[0] == '\'') {
      $pos = substr($pos,1);
      if (xmlfunc::isEncName($pos, $pos)) {
        if ($pos[0] == '\'') {
          $endpos = substr($pos,1);
          return true;
        }
      }
      return false;
    }
    return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-EncName
  public function isLatinCharacter($c)
  {
    return (($c >= 'A') && ($c <= 'Z')
      || ($c >= 'a') && ($c <= 'z'));
  }

  public function isEncName($string, &$endpos)
  {
    $pos = $string;
    if (!xmlfunc::isLatinCharacter($pos[0])) return false;
    $pos = substr($pos,1);
    while (xmlfunc::isLatinCharacter($pos[0]) || xmlfunc::isDigit($pos[0]) 
      || ($pos[0] == '.') || ($pos[0] == '_') || ($pos[0] == '-')) {
      $pos = substr($pos,1);
    }
    $endpos = $pos;
    return true;
  }

  /*// http://www.w3.org/TR/REC-xml/#NT-SDDecl
  public function xmlfunc::isYesNo(const char* string, char** endpos)
  {
    if (strstr(string, "yes") == string) {
      if (endpos) *endpos = (char*) string+strlen("yes");
      return true;
    }
    if (strstr(string, "no") == string) {
      if (endpos) *endpos = (char*) string+strlen("no");
      return true;
    }
    return false;
  }*/
  public function isSDDecl($string, &$endpos)
  {
    $pos = $string;
    if (!xmlfunc::isS($pos[0])) return false;
    $pos = substr($pos,1);
    if (strstr($pos, "standalone") != $pos) return false;
    $pos = substr($pos,strlen("standalone"));
    if (!xmlfunc::isEq($pos, $pos)) return false;
    if ($pos[0] == '"') {
      $pos = substr($pos,1);
      if (!xmlfunc::isYesNo($pos, $pos)) return false;
      if ($pos[0] == '"') {
        $endpos = substr($pos,1);
        return true;
      }
    }
    else if ($pos[0] == '\'') {
      $pos = substr($pos,1);
      if (!xmlfunc::isYesNo($pos, $pos)) return false;
      if ($pos[0] == '\'') {
        $endpos = substr($pos,1);
        return true;
      }
    }
    return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-prolog
  public function isProlog($string, &$endpos)
  {
    $pos = $string;
    xmlfunc::isXMLDecl($pos, $pos);
    while (xmlfunc::isMisc($pos, $pos));
    if (xmlfunc::isdoctypedecl($pos, $pos) || xmlfunc::isStyleSheetPI($pos, $pos)) {
      while (xmlfunc::isMisc($pos, $pos));
    }
    $endpos = $pos;
    return true;
  }

  // http://www.w3.org/TR/REC-xml/#NT-Misc
  public function isMisc($string, &$endpos)
  {
    if (xmlfunc::isComment($string, $endpos) /*|| isPI($string, $endpos)*/) return true;
    else if (xmlfunc::isS($string[0])) {
      $endpos = substr($string,1);
      return true;
    }
    else return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-S
  public function isS($c)
  {
    $c = ord($c);
    if ($c == 0x0a) $this->cinCurrentLine++;
    return (($c == 0x20) || ($c == 0x09) || ($c == 0x0d) || ($c == 0x0a));
  }

  // http://www.w3.org/TR/REC-xml/#NT-BaseChar
  public function isBaseChar($c)
  {
    $c = ord($c);
    return (	(($c >= 0x41) && ($c <= 0x5a))
            ||(($c >= 0x61) && ($c <= 0x7a))
            ||(($c >= 0xc0) && ($c <= 0xd6))
            ||(($c >= 0xd8) && ($c <= 0xf6))
            ||(($c >= 0xf8) && ($c <= 0xff)));
  }

  // http://www.w3.org/TR/REC-xml/#NT-Ideographic
  /*public function xmlfunc::isIdeographic(int c)
  {
    // Unicode support required
    return false;
  }*/

  // http://www.w3.org/TR/REC-xml/#NT-Digit
  public function isDigit($c)
  {
    $c=ord($c);
    return (	(($c >= 0x30) && ($c <= 0x39))	);
  }

  // http://www.w3.org/TR/REC-xml/#NT-Letter
  public function isLetter($c) 
  {
    return (xmlfunc::isBaseChar($c)/* || isIdeographic(c)*/);
  }

  // http://www.w3.org/TR/REC-xml/#NT-Name
  public function isName($string, &$endpos)
  {
    if (strlen($string) == 0) return false;
    if (xmlfunc::isLetter($string[0]) || ($string[0] == '_') || ($string[0] == ':')) {
      $pos = substr($string,1);
      while ($pos[0] && xmlfunc::isNameChar($pos[0])) {
        $pos = substr($pos, 1);
      }
      $endpos = $pos;
      return true;
    }
    return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-NameChar
  public function isNameChar($c)
  {
    return (xmlfunc::isLetter($c) || xmlfunc::isDigit($c) 
      || ($c == '.') || ($c == '-') 
      || ($c == '_') || ($c == ':') 
      /*|| isCombiningChar(c)*/ || xmlfunc::isExtender($c));
  }

  // http://www.w3.org/TR/REC-xml/#NT-Extender
  public function isExtender($c)
  {
    $c = ord($c);
    return ($c == 0xb7);
  }

  // http://www.w3.org/TR/REC-xml/#NT-Eq
  public function isEq($string, &$endpos)
  {
    $foundeq = false;
    $pos = $string;
    if (xmlfunc::isS($pos[0])) $pos=substr($pos,1);
    if ($pos[0] == '=') {
      $foundeq = true;
      $pos=substr($pos,1);
    }
    if (xmlfunc::isS($pos[0])) $pos=substr($pos,1);
    $endpos = $pos;
    return $foundeq;
  }

  // http://www.w3.org/TR/REC-xml/#NT-CharRef
  public function isHex($c)
  {
    return (xmlfunc::isDigit($c) 
      || (($c >= 'a') && ($c <= 'f'))
      || (($c >= 'A') && ($c <= 'F')));
  }

  public function isCharRef($string, &$endpos)
  {
    if (strlen($string) < strlen("&#;")) return false;
    if ($string[0] != '&') return false;
    if ($string[1] != '#') return false;
    
    if ($string[2] != 'x') {
      // DEC
      $pos=susbtr($string,2);
      while (xmlfunc::isDigit($pos[0])) {
        $pos = substr($pos,1);
      }
      if ($pos[0] != ';') return false;
      $endpos = substr($pos,1);
    }
    else {
      // HEX
      $pos=susbtr($string,3);
      while (xmlfunc::isHex($pos[0])) {
        $pos = substr($pos,1);
      }
      if ($pos[0] != ';') return false;
      $endpos = substr($pos,1);
    }
    return true;
  }

  // http://www.w3.org/TR/REC-xml/#NT-EntityRef
  public function isEntityRef($string, &$endpos)
  {
    if ($string[0] != "&") return false;
    //var $epos;
    if (!xmlfunc::isName(substr($string,1), $epos)) return false;
    if ($epos[0] != ";") return false;

    $endpos = substr($epos,1);
    return true;
  }

  // http://www.w3.org/TR/REC-xml/#NT-Reference
  public function isReference($string, &$endpos)
  {
    return (xmlfunc::isEntityRef($string, $endpos) || xmlfunc::isCharRef($string, $endpos));
  }

  // http://www.w3.org/TR/REC-xml/#NT-AttValue
  public function isAttValue($string, &$attValue, &$endpos)
  {
    $pos = $string;
    //var $epos;
    if ($pos[0] == '"') {
      $pos = substr($pos, 1);
      while (1) {
        if (xmlfunc::isReference($pos, $epos)) {
          $pos = $epos;
        }
        else if (($pos[0] != '<') && ($pos[0] != '&') && ($pos[0] != '"')) {
          $pos = substr($pos, 1);
        }
        else
          break;
      }
      if ($pos[0] != '"') return false;
      //if (attValue) {
        $attValue = substr($string, 1, strlen($string)-strlen($pos)-1);
      //}
      $endpos = substr($pos, 1);
      return true;
    }
    else if ($pos[0] == '\'') {
      $pos = $substr($pos, 1);
      while (1) {
        if (xmlfunc::isReference($pos, $epos)) {
          $pos = $epos;
        }
        else if (($pos[0] != '<') && ($pos[0] != '&') && ($pos[0] != '\'')) {
          $pos = $substr($pos, 1);
        }
        else
          break;
      }
      if ($pos[0] != '\'') return false;
      //if (attValue) {
        $attvalue = substr($string, 1, strlen($string)-strlen($pos)-1);
      //}
      $endpos = $substr($pos, 1);
      return true;
    }
    else {
      return false;
    }
  }

  // http://www.w3.org/TR/REC-xml/#NT-Attribute
  public function isAttribute($string, &$attName, &$attValue, &$endpos)
  {
    //var $epos;
    if (!xmlfunc::isName($string, $epos)) {
      return false;
    }
    //if ($attName) {
      $attName = substr($string, 0, strlen($string)-strlen($epos));
    //}
    if (!xmlfunc::isEq($epos, $epos)) {
      return false;
    }
    if (!xmlfunc::isAttValue($epos, $attValue, $epos)) {
      return false;
    }
    $endpos = $epos;
    return true;
  }

  // http://www.w3.org/TR/REC-xml/#NT-STag
  public function isSorEmptyTag($string, &$tagname, &$attnamelist, &$attvallist, &$lonely, &$endpos)
  {
    if (strlen($string) < strlen("<x>")) return false;
    $pos = $string;
    
    if ($pos[0] != '<') return false;
    // var $epos;
    if (!xmlfunc::isName(substr($pos, 1), $epos)) return false;
    //if ($tagname) {
      $tagname = substr($pos, 1, strlen($pos)-strlen($epos)-1);
    //}
    $pos = $epos;
    
    //var $n;
    //var $v;
    while (1) {
      while (xmlfunc::isS($pos[0])) {
        $pos = substr($pos,1);
      }

      if (xmlfunc::isAttribute($pos, $n, $v, $epos)) {
        //if ($attnamelist) {
          $c = count($attnamelist);
          $attnamelist[$c] = $n;
        //}
        //if ($attvallist) {
          $c = count($attvallist);
          $attvallist[$c] = $v;
        //}
        $pos = $epos;
      }
      else {
        break;
      }
    }
    if (xmlfunc::isS($pos[0])) $pos = substr($pos,1);
    
    if ($pos[0] == '/') {
      /*if ($lonely)*/ $lonely=true;
      $pos = substr($pos,1);
    }
    else {
      /*if ($lonely)*/ $lonely=false;
    }
    if ($pos[0] != '>') return false;
    /*if ($endpos)*/ $endpos = substr($pos,1);
    return true;
  }

  /*public function xmlfunc::isSTag(const char* string, char* tagname, mylist<char>* attnamelist, mylist<char>* attvallist, char** endpos)
  {
    // bon hein, on va pas recoder alors que c'est presque semblable
    bool lonely = false;
    int r = xmlfunc::isSorEmptyTag(string, tagname, attnamelist, attvallist, &lonely, endpos);
    return !lonely;
  }*/

  // http://www.w3.org/TR/REC-xml/#NT-ETag
  public function isETag($string, &$tagname, &$endpos)
  {
    if (strlen($string) < strlen("</x>")) return false;
    $pos = $string;
    if ($pos[0] != '<') return false;
    if ($pos[1] != '/') return false;
    //var $epos;
    if (!xmlfunc::isName(substr($pos,2), $epos)) return false;
    //if (tagname) {
      $tagname = substr($pos, 2, strlen($pos)-strlen($epos)-2);
    //}
    $pos = $epos;
    if (xmlfunc::isS($pos[0])) $pos = substr($pos,1);
    if ($pos[0] != '>') return false;
    $endpos = substr($pos,1);
    return true;
  }

  /*// http://www.w3.org/TR/REC-xml/#NT-EmptyElemTag
  public function xmlfunc::isEmptyElemTag(const char* string, char* tagname, mylist<char>* attnamelist, mylist<char>* attvallist, char** endpos)
  {
    // bon hein, on va pas recoder alors que c'est presque semblable
    bool lonely = false;
    int r = xmlfunc::isSorEmptyTag(string, tagname, attnamelist, attvallist, &lonely, endpos);
    return lonely;
  }*/

  // http://www.w3.org/TR/REC-xml/#NT-CharData
  public function isCharData($string, &$data, &$endpos)
  {
    $pos = $string;
    if ($pos[0] == '<') return false;
    while ($pos[0] && ($pos[0] != '<') && ($pos[0] != '&')) {
      if ((strlen($pos) >= 3) && (strstr($pos, "]]>") == $pos)) {
        // marqueur trouve !
        return false;
      }
      if (ord($pos[0]) == 0x0a) $this->cinCurrentLine++;
      $pos = substr($pos,1);
    }
    if ($pos[0] == '<') {
      // on est a la fin !
      //if (data) {
        $data = substr($string, 0, strlen($string)-strlen($pos));
      //}
      $endpos = $pos;
      return true;
    }
    // certainement un '&' ... interdit 
    return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-CDStart
  public function isCDStart($string, &$endpos)
  {
    if (strstr($string, "<![CDATA[") == $string) 
		{
      $endpos = substr($string,strlen("<![CDATA["));
    }
    return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-CData
  public function isCData($string, &$cdata, &$endpos)
  {
    $pos = $string;
    $pos2 = strstr($string, "]]>");
    if ($pos2) {
      // todo: count lines
      // --> cinCurrentLine
      //if (cdata) {
        $cdata = substr($string, 0, strlen($string)-strlen($pos2));
      //}
      $endpos = $pos2;
      return true;
    }
    else return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-CDEnd
  public function isCDEnd($string, &$endpos)
  {
    if (strstr($string, "]]>") == $string) {
      $endpos = $string+strlen("]]>");
    }
    return false;
  }

  // http://www.w3.org/TR/REC-xml/#NT-CDSect
  public function isCDSect($string, &$cdata, &$endpos)
  {
    //var $epos;
    if (!xmlfunc::isCDStart($string, $epos)) return false;
    if (!xmlfunc::isCData($epos, $cdata, $epos)) return false;
    if (!xmlfunc::isCDEnd($epos, $epos)) return false;
    $endpos = $epos;
    return true;
  }

  // http://www.w3.org/TR/REC-xml/#NT-Comment
  public function isComment($string, &$endpos)
  {
    if (strlen($string) < strlen("<!---->")) return false;
    if (strstr($string, "<!--") != $string) return false;
    $pos = substr($string,strlen("<!--"));
    $pos2 = strstr($pos, "--");
    if (!$pos2) return false;
    if (strlen($pos2) < 3) return false;
    if ($pos2[2] != '>') return false;
    $endpos = substr($pos2,3);
    return true;
  }

  public static function userToXml($in_string, &$out_string)
  {
    $out_string="";
    $read_pos = $in_string;
    $write_pos = $out_string;
    while (strlen($read_pos)) {
      /*if ($read_pos[0] == ' ') {
        $write_pos .= "&nbsp;";
      }
      else*/ if ($read_pos[0] == '\'') {
        $write_pos .= "&apos;";
      }
      else if ($read_pos[0] == '"') {
        $write_pos .= "&quot;";
      }
      else if ($read_pos[0] == '&') {
        $write_pos .= "&amp;";
      }
      else {
        $write_pos .= $read_pos[0];
      }
      $read_pos = substr($read_pos, 1);
    }
    
    $out_string = $write_pos;
    return true;
  }

  public static function xmlToUser($in_string, &$out_string)
  {
    $f = new xmlfunc;
    $out_string = "";

    $read_pos = $in_string; // var $epos;
    $write_pos = $out_string;
    while (strlen($read_pos)) 
    {
      if ($f->isEntityRef($read_pos, $epos)) 
      {
        $charref = substr($read_pos, 0, strlen($read_pos)-strlen($epos));
        /*if (!strcmp($charref, "&nbsp;")) {
          $write_pos .= ' ';
        }
        else*/ if (!strcmp($charref, "&apos;")) {
          $write_pos .= '\'';
        }
        else if (!strcmp($charref, "&quot;")) {
          $write_pos .= '"';
        }
        else if (!strcmp($charref, "&amp;")) {
          $write_pos .= '&';
        }
        else 
        {
          $f->setLastError("xmltouser error (not correct caracter reference)");
          pTools_Assert(0, __FILE__, __LINE__, "0"); // error !
          $out_string = $in_string;
          unset($f);
          return false;
        }
        $read_pos = $epos;
      }
      else {
        $write_pos .= $read_pos[0];
        $read_pos = substr($read_pos, 1);
      }
    }
    unset($f);
    $out_string = $write_pos;
    return true;
  }

}
?>