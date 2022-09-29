<?php
// These class are C++ 'structures' translated from 'INWES' project:
//HTTPRequestLine: GET / HTTP/1.0
class HTTPRequestLine
{
  public $miszMethod;
  public $miszRequestURI;
  public $miszHTTPVersion;
  public function __construct()
  {
    $this->miszMethod="";
    $this->miszRequestURI="";
    $this->miszHTTPVersion="";
  }
  public function __destruct()
  {
  }
  public function Dump()
  {
    return $this->miszMethod." ".$this->miszRequestURI." ".$this->miszHTTPVersion;
  }
}

//HTTPResponseLine: HTTP/1.0 200 OK
class HTTPResponseLine
{
  public $miszHTTPVersion;
  public $miResultNumber;
  public $miszResultDescription;
  public function __construct()
  {
    $this->miszHTTPVersion="";
    $this->miResultNumber=500; /* FATAL ERROR */
    $this->miszResultDescription="";
  }
  public function __destruct()
  {
  }
  public function Dump()
  {
    return $this->miszHTTPVersion." ".$this->miResultNumber." ".$this->miszResultDescription;
  }
}

// HTTPHEADER:	location: 127.0.0.1
class HTTPHeader
{
  public $miszName;
  public $miszValue;
  public function __construct($name="",$value="")
  {
    $this->miszName=$name;
    $this->miszValue=$value;
  }
  public function __destruct()
  {
  }
  public function Dump()
  {
    return $this->miszName.": ".$this->miszValue;
  }
}

// exception car je n'ai pas de classe 'Headers'
function http_dumpHeaders($headers)
{
  $h="";
  for ($a=0; $a < count($headers); $a++)
  {
    $h .= $headers[$a]->Dump()."\r\n";
  }
  return $h;
}

//HTTPRequest: main request object
class HTTPRequest
{
  public $miRequestLine;
  public $miHeaders;
  public $mpBody;
  public function __construct()
  {
    $this->miRequestLine = new HTTPRequestLine();
    $this->miHeaders = Array();
    $this->mpBody = "";
  }
  public function __destruct()
  {
    unset($this->miRequestLine);
    for ($a=0; $a < count($this->miHeaders); $a++)
    {
      unset($this->miHeaders[$a]);
    }
    unset($this->miHeaders);
  }
  public function Dump()
  {
    $d = $this->miRequestLine->Dump()."\r\n";
    $d .= http_dumpHeaders($this->miHeaders);
    $d .= "\r\n"; // END HEADER
    $d .= $this->mpBody."\r\n";
    return $d;
  }
}

//HTTPResponse: main responce object
class HTTPResponse
{
  public $miResponseLine;
  public $miHeaders;
  public $mpBody;
  public $mibEndOfResponse;
  public function __construct()
  {
    $this->miResponseLine = new HTTPResponseLine();
    $this->miHeaders = Array();
    $this->mpBody = "";
    $this->mibEndOfResponse = false;
  }
  public function __destruct()
  {
    unset($this->miResponseLine);
    for ($a=0; $a < count($this->miHeaders); $a++)
    {
      unset($this->miHeaders[$a]);
    }
    unset($this->miHeaders);
  }
  public function Dump()
  {
    $d = $this->miResponseLine->Dump()."\r\n";
    $d .= http_dumpHeaders($this->miHeaders);
    $d .= "\r\n"; // END HEADER
    $d .= $this->mpBody."\r\n";
    return $d;
  }
}

class HTTPCookie
{
  public $miszName;
  public $miszValue;
  public $minMaxAge;
  public $createTime;
  public $extraOptions;
  public function __construct()
  {
    $this->miszName="";
    $this->miszValue="";
    $this->minMaxAge=0;
    $this->createTime=time();
    $this->extraOptions="";
  }
  public function __destruct()
  {
  }
  public function parseHTTPHeaderValue($val)
  {
    $opts = explode(";", $val);
    for ($a=0; $a < count($opts); $a++)
    {
      $opt = trim($opts[$a]);
      $optv = explode("=", $opt);
      if ($a == 0)
      {
        $this->miszName = $optv[0];
        $this->miszValue = $optv[1];
      }
      else if (!strcasecmp($optv[0],"Max-Age"))
      {
        $this->minMaxAge = $optv[1];
      }
      else
      {
        if ($this->extraOptions != "") $this->extraOptions .= ";";
        $this->extraOptions .= $opt;
      }
    }
  }
}
?>