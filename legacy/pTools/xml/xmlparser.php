<?php
class xmlparser
{
  private $cRootNode;
  private $cErrorMessages;
  private $cWarningMessages;
  
  public $x;
  
  public function AddErrorMessage($msg)
  {
    $this->cErrorMessages[count($this->cErrorMessages)] = $msg;
  }
  
  public function AddWarningMessage($msg)
  {
    $this->cWarningMessages[count($this->cErrorMessages)] = $msg;
  }
  
  public function __construct()
  {
    $this->cRootNode = NULL;
    $this->cErrorMessages = array();
    $this->cWarningMessages = array();
    $this->x = new xmlfunc();
  }
  
  public function __destruct()
  {
    if ($this->cRootNode) unset($this->cRootNode);
    unset($this->cErrorMessages);
    unset($this->cWarningMessages);
    unset($x);
  }
  
  public function getRootNode()
  {
    return $this->cRootNode;
  }
  
  public function parsefile($file)
  {
    $f = file($file);
    $buffer = "";
    for ($a=0; $a < count($f); $a++) 
    {
      $buffer .= $f[$a];
    }
    return $this->parsebuffer($buffer);
  }
  
  public function parsebuffer($buffer)
  {
    $this->x->reset();
    unset($this->cErrorMessages);
    unset($this->cWarningMessages);
    $this->cErrorMessages = array();
    $this->cWarningMessages = array();
    if ($this->cRootNode) unset($this->cRootNode);
    $this->cRootNode = new xmlnode();
    // http://www.w3.org/TR/REC-xml/#dt-wellformed
    $this->x->isProlog($buffer, $buffer);
    $parseRetCode = $this->cRootNode->parse($buffer, $buffer, $this);
    if (!$parseRetCode)
    {
      unset($this->cRootNode);
      $this->cRootNode = NULL;
    }
    else
    {
      $pos = $buffer;
			if ($this->x->isMisc($pos, $pos))
      {
        $this->AddWarningMessage("Ignored data after closing root element \"".$this->cRootNode->GetName()."\"");
      }
    }
    return $parseRetCode;
  }
  
  public function printErrors()
  {
    for ($a=0; $a < count($this->cErrorMessages); $a++)
    {
      echo "Error: ". $this->cErrorMessages[$a]."<br/>\n";
    }
  }
  
  public function printWarnings()
  {
    for ($a=0; $a < count($this->cWarningMessages); $a++)
    {
      echo "Error: ". $this->cWarningMessages[$a]."<br/>\n";
    }
  }
}
?>