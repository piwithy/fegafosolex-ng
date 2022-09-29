<?php
require("xmlattribute.php");

class xmlnode
{
  private $cName;
  private $cAttributeList;
  private $cChildrenList;
  private $cInsideCharDataBeforeChilds;
  private $cOutsideCharDataAfterClosingTag;
  private $attribute_iterator;
  private $children_iterator;
  
  // protected for XmlParser
  public function __construct($aName="", $databeforechilds="", $dataafterclosingtag="")
  {
    $this->cName = $aName;
    $this->cInsideCharDataBeforeChilds = $databeforechilds;
    $this->cOutsideCharDataAfterClosingTag = $dataafterclosingtag;
		$this->cAttributeList = Array();
		$this->cChildrenList = Array();
  }
  
  public function parse($string, &$posend, $aParser)
  {
    //var $attnamelist;
    //var $attvaluelist;
    $lonely = false;
    //var $epos;
    if (!$aParser->x->isSorEmptyTag($string, $this->cName, $attnamelist, $attvaluelist, $lonely, $epos)) 
    {
      echo "ERRERR[".htmlspecialchars($string)."]";
      if ($this->cName == "") 
      {
        $aParser->AddErrorMessage("Unterminated element start '".$this->cName."' on line ".$aParser->x->GetCurrentLine());
      }
      else 
      {
        $aParser->AddErrorMessage("No element found on line ".$aParser->x->GetCurrentLine());
      }
      unset($attnamelist);
      unset($attvaluelist);
      return false;
    }
    unset($this->cAttributeList); // reset
		$this->cAttributeList = Array();
    $maincount = count($this->cAttributeList);
    $i = 0;
    $j = 0;
    while (isset($attnamelist[$i]))
		{
      $a = new xmlattribute($attnamelist[$i]);
      if (!is_null($attvaluelist[$j])) 
			{
        $a->setValue($attvaluelist[$j]);
      }
      $this->cAttributeList[$maincount++] = $a;
      $i++; $j++;
    }
    
    if (!$lonely) 
		{
      unset($this->cChildrenList); // reset
			$this->cChildrenList = Array();
      $this->cInsideCharDataBeforeChilds="";
      $this->cOutsideCharDataAfterClosingTag="";
      $data ="";
      if ($aParser->x->isCharData($epos, $data, $epos)) {
        $this->cInsideCharDataBeforeChilds = $data;
      }
      while (1) 
			{
        $recycled="";
        if ($aParser->x->isSorEmptyTag($epos, $recycled, $recycled, $recycled, $recycled, $endpos)) { // endpos on ne veut pas perdre l'info
          // childs !
          $n = new xmlnode();
          $r = $n->parse($epos, $epos, $aParser);
          if (!$r) {
            unset($n);
            return false;
          }
          $count = count($this->cChildrenList);
          $this->cChildrenList[$count] = $n;
        }
        else if ($aParser->x->isETag($epos, $endTag, $epos)) 
        {
          if ($endTag == $this->cName) 
          {
            // ok
            break;
          } 
          else 
          {
            $aParser->AddErrorMessage("Missing closing tag for element '".$this->cName."' at line ".$aParser->x->getCurrentLine());
            return false;
          }
        }
        else if ($aParser->x->isReference($epos, $epos)) {
          // m'en fou !
        }
        else if ($aParser->x->isCDSect($epos, $recycled, $epos)) {
          // m'en fou !
        }
        /*else if (x.isPI(epos, NULL, &epos)) {
          // m'en fou
        }*/
        else if ($aParser->x->isComment($epos, $epos)) {
          // m'en fou

        }
        else if ($aParser->x->isCharData($epos, $data, $epos)) 
        {
          $child = xmlnode::getLastNode();
          if (!$child) {
            // on les droppe
          }
          else {
            pTools_Assert($child->cOutsideCharDataAfterClosingTag == "", __FILE__, __LINE__); // sinon, perte memoire
            $child->cOutsideCharDataAfterClosingTag = $data;
          }
        }
        else 
        {
          while ($aParser->x->isS($epos[0])) 
            $epos = substr($epos,1);
          // oups
          if (strlen($epos)) 
          {
            $s = strlen($epos);
            if ($s > 128) $s = 128;
            $liPartial = substr($epos, 0, $s);
            $liPartial = str_replace('\n','',$liPartial);
            $aParser->AddErrorMessage("Syntax error near: ".$liPartial);
            echo "?";
          }
          else 
          {
          echo "!";
            $aParser->AddErrorMessage("Missing closing tag for element '".$this->cName."' at line ".$aParser->x->getCurrentLine());
          }
          echo "ici";
          return false;
        }
      }
		}
    $posend = $epos;
    return true;
  }
  
  // public
  public function duplicate()
  {
    $n = new xmlnode();
    
    $n->cName = $this->cName;
    $n->cInsideCharDataBeforeChilds = $this->cInsideCharDataBeforeChilds;
    $n->cOutsideCharDataAfterClosingTag = $this->cOutsideCharDataAfterClosingTag;
    $a = $this->getFirstAttribute();
    while (!is_null($a)) {
      $count = count($n->cAttributeList);
      $n->cAttributeList[$count] = $a->duplicate();
      $a = $this->getNextAttribute();
    }
    
    $c = $this->getFirstNode();
    while (!is_null($c)) {
      $count = count($n->cChildrenList);
      $n->cChildrenList[$count] = $c->duplicate();
      $c = $this->getNextNode();
    }
    return $n;
  }
  
  public function __destruct()
  {
    unset($this->cAttributeList);
    unset($this->cChildrenList);
  }
  
	public function dump(&$buffer)
	{
    $buffer .= "<";
    $buffer .= $this->cName;

    $a = xmlnode::getFirstAttribute();
    while ($a) {
      $buffer .= " ";
      $buffer .= $a->dump();
      $a = xmlnode::getNextAttribute();
    }
    if (count($this->cChildrenList)) 
		{
      $buffer .= ">";
      $buffer .= $this->cInsideCharDataBeforeChilds;
      $n = xmlnode::getFirstNode();
      while ($n) {
        $n->dump($buffer);
        $n = xmlnode::getNextNode();
      }
      $buffer .= "</";
      $buffer .= $this->cName;
      $buffer .= ">";
    }
    else if ($this->cInsideCharDataBeforeChilds != "") 
		{
      $buffer .= ">";
      $buffer .= $this->cInsideCharDataBeforeChilds;
      $buffer .= "</";
      $buffer .= $this->cName;
      $buffer .= ">";
    }
    else {
      $buffer .= "/>";
    }
    $buffer .= $this->cOutsideCharDataAfterClosingTag;
	}
 
	public function getName()
	{
    return $this->cName;
	}
 
	public function getFirstNode()
	{
    $this->children_iterator = 0;
		if (count($this->cChildrenList)==0)
			return null;
    return $this->cChildrenList[$this->children_iterator];
	}
 
	public function getNextNode()
	{
    $this->children_iterator++;
    if ($this->children_iterator >= count($this->cChildrenList))
      return NULL;
    return $this->cChildrenList[$this->children_iterator];
	}
 
	public function getLastNode()
	{
    return $this->cChildrenList[count($this->cChildrenList)-1];
	}
 
	public function getFirstAttribute()
	{
    $this->attribute_iterator = 0;
    return $this->cAttributeList[$this->attribute_iterator];
	}
 
	public function getNextAttribute()
	{
    $this->attribute_iterator++;
    if ($this->attribute_iterator >= count($this->cAttributeList)) {
      return NULL;
    }
    return $this->cAttributeList[$this->attribute_iterator];
	}
 
	public function addNode($aChildNode)
	{
    $c = count($this->cChildrenList);
    $this->cChildrenList[$c] = $aChildNode;
	}
 
	public function getUserAttributeValue($aAttName)
	{
    $a = xmlnode::getFirstAttribute();
    while ($a) {
      if ($a->getName() == $aAttName) {
        return $a->getUserValue();
      }
      $a = xmlnode::getNextAttribute();
    }
    return NULL;
	}
 
	public function setUserAttribute($aAttName, $aAttUserValue)
	{
    $a = xmlnode::getFirstAttribute();
    while ($a) {
      if ($a->getName() == $aAttName) {
        // exists, just sets
        $a->setUserValue($aAttUserValue);
        return;
      }
      $a = xmlnode::getNextAttribute();
    }
    // Create the attribute
    
    $a = new xmlattribute($aAttName);
    $a->setUserValue($aAttUserValue);
    $c = count($this->cAttributeList);
    $this->cAttributeList[$c] = $a;
  }
 
	public function getNodeCount()
	{
    return count($this->cChildrenList);
	}
 
}
?>