<?php
function uri_getscheme($uri) 
{
  $pos = strpos($uri, ":");
  if ($pos) 
  {
    if (substr($uri, $pos+1,1) == "/") 
    {
      // Okay, real scheme used
      return substr($uri, 0, $pos);
    }
    else return "";
  }
  else return "";
}

function uri_gethost($uri) 
{
  $scheme = uri_getscheme($uri);
  if ($scheme != "") $tmphost = substr($uri, strlen($scheme)+1);
  else $tmphost = $uri;
  
  $pos=0;
  while ($tmphost[$pos] == "/") $pos++;
  $tmphost = substr($tmphost, $pos);
  $tmphost = strtok($tmphost, ":");
  $tmphost = strtok($tmphost, "/");
  $tmphost = strtok($tmphost, "?");
  $tmphost = strtok($tmphost, "#");
  return $tmphost;
}

function uri_getport($uri) 
{
  $pos = strpos($uri, ":");
  if ($pos === false) return "";
  
  $tmpport  = substr($uri, $pos+1);
  if ($tmpport[0] == "/") {
    $pos = strpos($tmpport, ":");
    if ($pos === false) return "";
    $tmpport  = substr($tmpport, $pos+1);
  }
  
  // No scheme, but PORT
  $tmpport = strtok($tmpport, "/");
  $tmpport = strtok($tmpport, "?");
  $tmpport = strtok($tmpport, "#");  
  
  return $tmpport;
}

function uri_getportfromscheme($scheme) 
{
  return getservbyname($scheme, 'tcp');
}

function uri_getpatharguments($uri) 
{
  $pos = strpos($uri, "/");
  if ($pos === false) 
		return "";
  $tmppath = substr($uri, $pos-1);
  
  if ($tmppath[0] == ":") 
	{
    // this is scheme !!
    $tmppath = substr($tmppath, 3); // at least 3 caracters (://)
    $pos = strpos($tmppath, "/");
    if ($pos === false) return "/";
    $tmppath = substr($tmppath, $pos);
  }
  else 
	{
    $tmppath = substr($tmppath, 1);
  }
  
  if ($tmppath[strlen($tmppath)-1] == '/') 
	{
    $tmppath = substr($tmppath, 0, strlen($tmppath)-1);
  }
  return $tmppath;
}
?>