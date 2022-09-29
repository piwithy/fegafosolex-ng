<?php
/* This is core/system */
function pTools_Assert($test, $f="?.?", $l="&lt;Unknown&gt;", $message="")
{
  if (!$test) {
    $out = "<b>pTools Assertion Failed</b> in ";
    $out .= $f;
    $out .= " at line $l<br/>";
    $out .= $message;
    die($out);
  }
}

function pTools_getAbsoluteServerPath()
{
  $thisfile = preg_replace("/\\\\/","/", __FILE__);
  $path = substr($thisfile, 0, strrpos($thisfile,"/"))."/";
  return $path;
}

?>