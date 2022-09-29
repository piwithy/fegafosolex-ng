<?php
/* MAIN FUNCTIONs TO CALL: */
function makeHTTPRequest($url, $proxyurl="", $POSTPARAMnames=null, $POSTPARAMvalues=null, $POSTPARAMseparator=null, $POSTMethod="POST") 
{
  $rqhost="";
  $rqport="";
  $rqurl="";
  if ($proxyurl == "") 
  {
    $rqhost = uri_gethost($url);
    $rqport = uri_getport($url);
    if ($rqport == "") $rqport = uri_getportfromscheme(uri_getscheme($url));
    $rqurl = uri_getpatharguments($url);
  }
  else 
  {
    $rqhost=uri_gethost($proxyurl);
    $rqport=uri_getport($proxyurl);
    if ($rqport == "") $rqport = uri_getportfromscheme(uri_getscheme($proxyurl));
  }
  
  $HTTPReq = new HTTPRequest();
  if ($POSTPARAMnames !== null) $HTTPReq->miRequestLine->miszMethod = $POSTMethod;
  else $HTTPReq->miRequestLine->miszMethod = "GET";
  $HTTPReq->miRequestLine->miszRequestURI = $rqurl;
  $HTTPReq->miRequestLine->miszHTTPVersion = "HTTP/1.1";
  
  $HTTPReq->miHeaders[] = new HTTPHeader("Host", $rqhost);
  http_addCLIENTstandardheader($HTTPReq);
  //echo "http_makepostcontent ".$POSTPARAMseparator;
  $encodedData = http_makepostcontent($POSTPARAMnames, $POSTPARAMvalues, $POSTPARAMseparator, $POSTMethod);
  
  if ($HTTPReq->miRequestLine->miszMethod=="POST")
  {
    http_addPOSTheader($HTTPReq, $encodedData, $POSTPARAMseparator);
    $HTTPReq->mpBody = $encodedData;
  }
  else
  {
    // modify URI to add addition data
    if ($encodedData != "")
    {
      if (strpos($HTTPReq->miRequestLine->miszRequestURI, "?") === FALSE)
        $HTTPReq->miRequestLine->miszRequestURI .= "?";
      else
        $HTTPReq->miRequestLine->miszRequestURI .= "&";
      $HTTPReq->miRequestLine->miszRequestURI .= $encodedData;
    }
  }
  
  $HTTPReq->HACKHOST = $rqhost;
  $HTTPReq->HACKPORT = $rqport;
  
  return $HTTPReq;
}

function callHTTPRequest($HTTPReq)
{
  $s = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
  
  if (!socket_set_option($s, SOL_SOCKET, SO_RCVBUF, 8192))
    echo "set sockopt RCVBUF=8192 failed";
  if (!socket_set_option($s, SOL_SOCKET, SO_SNDBUF, 8192))
    echo "set sockopt SNDBUF=8192 failed";
  //if (!socket_set_option($s, SOL_SOCKET, SO_RCVTIMEO, 4))
    //echo "set sockopt RCVTIMEO failed";
  
  $result = @socket_connect($s, $HTTPReq->HACKHOST, $HTTPReq->HACKPORT);
  if (!$result) 
  {
    /*echo "Connect Error";*/
    return null;
  }

  $in = $HTTPReq->Dump();
  $out = "";
  
  //echo "SOCKET WRITE <pre>".$in."</pre><br/>\n";
  socket_write($s, $in, strlen($in));
  $out = "";
  while ($tmp = socket_read($s, 4096, PHP_BINARY_READ)) 
  {
    //echo "SOCKET READ <pre>".htmlspecialchars($tmp)."</pre><br/>\n";
    $out .= $tmp;
  }
  socket_close($s);
  
  /* PARSE THE RESPONSE NOW! */
  //echo "<pre>".$out."</pre>";
  $page = explode("\n", $out);
  
  $HTTPResp = new HTTPResponse();
  $rline = explode(" ", $page[0]);
  $HTTPResp->miResponseLine->miszHTTPVersion=$rline[0];
  $HTTPResp->miResponseLine->miResultNumber=$rline[1];
  $HTTPResp->miResponseLine->miszResultDescription=$rline[2];
  for ($a=3; $a < count($rline); $a++)
  {
    $HTTPResp->miResponseLine->miszResultDescription .= " ".$rline[3];
  }
  
  $a=1;
	$chunked = 0;
  while ($page[$a] != "") 
  {
    if ($page[$a] == "\r") 
		{
			$a++;
			break; // end of headers
		}
    $header = explode(":", $page[$a++]);
    $lastHeader = new HTTPHeader(trim($header[0]), trim($header[1]));
		$HTTPResp->miHeaders[] = $lastHeader;
		if (!strcasecmp($lastHeader->miszName, "transfer-encoding") && !strcasecmp($lastHeader->miszValue, "chunked"))
		{
			$chunked = 1;
		}
  }
	
	$chunked_readLen = 1;
	$chunked_end = 0;
  while (!$chunked_end && ($a < count($page)))
  {
		if ($chunked)
		{
			$str = $page[$a]."\n";
			while ($lenOfStr = strlen($str))
			{
				if ($chunked_readLen)
				{
					//echo "analyze len in XXX".$page[$a]."YYY<br/>\n";
					$len = hexdec($page[$a]);
					$chunked_readLen = 0;
					//echo "should read len (".$len.")<br/>\n";
					if ($len == 0)
						$chunked_end = 1;
					break; // ya plus de donnees de toute facon, j'dois passer a la suite
				}
				else
				{
					$minlen = min($lenOfStr, $len);
					$leftPart = substr($str, 0, $minlen);
					$rightPart = substr($str, $minlen);
					//echo "  concatData(".CHTML::toHTML($leftPart).")<br/>\n";
					$HTTPResp->mpBody .= $leftPart;
					$str = $rightPart;
					$len -= $minlen;
					//echo "  len left = ".$len."<br/>\n";
					pTools_assert($len >= 0, __FILE__, __LINE__, "len > 0");
					if ($len ==0)
					{
						$chunked_readLen = 1;
						break;
					}
				}
			}/*while data...*/
		}
		else
		{
			$HTTPResp->mpBody .= $page[$a]."\n";
		}
		$a++;
  }
  return $HTTPResp;
}

/*
 * Sub functions... 
 */
function http_addPOSTheader($HTTPReq, $POSTdata, $POSTPARAMseparator)
{
  if ($POSTdata != "")
  {
    if ($POSTPARAMseparator!=null)
    {
      $HTTPReq->miHeaders[] = new HTTPHeader("Content-Length", strlen($POSTdata));
      $HTTPReq->miHeaders[] = new HTTPHeader("Content-Type", "multipart/form-data; boundary=$POSTPARAMseparator");
    }
    else
    {
      $HTTPReq->miHeaders[] = new HTTPHeader("Content-Length", strlen($POSTdata));
      $HTTPReq->miHeaders[] = new HTTPHeader("Content-Type", "application/x-www-form-urlencoded");
    }
  }
}

function http_makepostcontent($POSTPARAMnames, $POSTPARAMvalues, $POSTPARAMseparator, $POSTMethod)
{
  $MultipartEncode=0; // else, RAW URL ENCODE
  if (($POSTPARAMseparator != null) && ($POSTMethod=="POST"))
    $MultipartEncode=1;

  $c="";
  if ($POSTPARAMnames != null) /* there is post data */ 
  {
    for ($a = 0; $a < count($POSTPARAMnames); $a++) 
    {
      if ($MultipartEncode)
      {
        // will be multipart/form-data
        $c .= "--".$POSTPARAMseparator."\r\n";
        $c .= "Content-Disposition: form-data; name=\"{$POSTPARAMnames[$a]}\"\r\n\r\n";
        $c .= $POSTPARAMvalues[$a];
        $c .= "\r\n";
      }
      else
      {
        // urlencoded
        if ($c != "") $c .= "&";
        $c .= $POSTPARAMnames[$a];
				if (isset($POSTPARAMvalues[$a]) && (!is_null($POSTPARAMvalues[$a])))
				{
					$c .= "=";
					$c .= $POSTPARAMvalues[$a];
				}
      }
    }
    if ($POSTPARAMseparator!=null)
      $c .= "--".$POSTPARAMseparator."--\r\n";
    /*else
      $c .= "\r\n";*/
  }
  return $c;
}

function http_addCLIENTstandardheader($HTTPReq)
{
  $HTTPReq->miHeaders[] = new HTTPHeader("User-Agent", "pTools_HTTP/1.02");
  $HTTPReq->miHeaders[] = new HTTPHeader("Accept", "*/*");
  $HTTPReq->miHeaders[] = new HTTPHeader("Connection", "close");
  // From Mozilla (test)
  //$HTTPReq->miHeaders[] = new HTTPHeader("Connection", "keep-alive"); // jsais pas gerer
  //$HTTPReq->miHeaders[] = new HTTPHeader("Accept-Language", "fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3");
  //$HTTPReq->miHeaders[] = new HTTPHeader("Accept", "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8");
  //$HTTPReq->miHeaders[] = new HTTPHeader("Accept-Encoding", "gzip,deflate"); 
}
?>