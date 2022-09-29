<?php
class xmlattribute
{
	private $cAttName;
	private $cAttValue;
	private $cAttUserValue;

  public function __construct($aname="")
  {
    $this->cAttName=$aname;
    $this->cAttValue="";
    $this->cAttUserValue="";
  }
  
  public function __destruct()
  {
  }

  public function duplicate()
  {
    $a = new xmlattribute($this->cAttName);
    $a->cAttValue = $this->cAttValue;
    $a->cAttUserValue = $this->cAttUserValue;
    return $a;
  }

  public function getName()
  {
    return $this->cAttName;
  }

  public function setValue($a)
  {
    $this->cAttValue = $a;
    $this->cAttUserValue = "";
  }

  public function getValue()
  {
    return $this->cAttValue;
  }

  public function setUserValue($a)
  {
    xmlfunc::userToXml($a, $this->cAttValue);
    $this->cAttUserValue = "";
  }

  public function getUserValue()
  {
    xmlfunc::xmlToUser($this->cAttValue, $this->cAttUserValue);
    return $this->cAttUserValue;
  }

  public function dump()
  {
    return xmlattribute::getName()."=\"".xmlattribute::getValue()."\"";
  }
}
?>