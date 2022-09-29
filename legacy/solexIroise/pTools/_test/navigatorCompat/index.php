<?php
include("../../pTools/main.php");
pTools::initialize("../../pTools");
?>
<html>
<body>
<h1>Test du module "navigatorCompat", partie requetes AJAX</h1>
<?php
pTools::getpTools()->loadJavaScript();
echo "Cette page a été chargée le ".date("d/m/Y à H:i:s")."<br/>\n";
?>
Ce message ne changera jamais. Et pourtant vous allez effectuer des requetes vers le serveur ... cool nan ? <br/>
<h3>Test 1: ncXmlHttpRequest test GET 404 ['none' file]</h3>
  <div id="test1"></div>
  <script language="Javascript" type="text/javascript">
  var o = document.getElementById('test1');
  o.innerHTML = "Testing ...";

  // Send Request
  var request1 = new ncXmlHttpRequest();
  request1.setOnReadyStateChange(function() { test1cb(request1); });
  request1.open("GET", "none", true);
  request1.send();

  function test1cb(request)
  {
    if (request.getReadyState() == 4)
    {
      if (request.getStatus() == 404)
        o.innerHTML = "OK (404 '"+request.getStatusText()+"' Response got successfully)";
      else
        o.innerHTML = "KO (Got Status "+request.getStatus()+" '"+request.getStatusText()+"')";
    }
  }
  </script>
<h3>Test 2: ncXmlHttpRequest test GET 200 TEXT Data ['data/text.txt' file]</h3>
  <div id="test2"></div>
  <script language="Javascript" type="text/javascript">
  var o2 = document.getElementById('test2');
  o2.innerHTML = "Testing ...";

  // Send Request
  var request2 = new ncXmlHttpRequest();
  request2.setOnReadyStateChange(function() { test2cb(request2); });
  request2.open("GET", "data/text.txt", true);
  request2.send();

  function test2cb(request)
  {
    if (request.getReadyState() == 4)
    {
      if (request.getStatus() == 200)
      {
        o2.innerHTML = "OK (200 '"+request.getStatusText()+"' Response got successfully)<br/>";
        if (request.getResponseText()==null) o2.innerHTML += "KO (No response Text ?)<br/>";
        else o2.innerHTML += "OK (AS TEXT: <pre style='display: inline;'>"+htmlspecialchars(request.getResponseText())+"</pre>)<br/>";
        var xml = request.getResponseXML();
        if (xml!=null) o2.innerHTML += "KO (Interpreted as XML ???)<br/>";
        else o2.innerHTML += "OK (No interpretation as XML)<br/>";
      }
      else
        o2.innerHTML = "KO (Got Status "+request.getStatus()+" '"+request.getStatusText()+"')";
    }
  }
  </script>
<h3>Test 3: ncXmlHttpRequest test GET 200 XML Data ['data/xml_valid.xml' file]</h3>
  <div id="test3"></div>
  <script language="Javascript" type="text/javascript">
  var o3 = document.getElementById('test3');
  o3.innerHTML = "Testing ...";

  // Send Request
  var request3 = new ncXmlHttpRequest();
  request3.setOnReadyStateChange(function() { test3cb(request3); });
  request3.open("GET", "data/xml_valid.xml", true);
  request3.send();

  function test3cb(request)
  {
    if (request.getReadyState() == 4)
    {
      if (request.getStatus() == 200)
      {
        o3.innerHTML = "OK (200 '"+request.getStatusText()+"' Response got successfully)<br/>";
        if (request.getResponseText()==null) o3.innerHTML += "KO (No response Text ?)<br/>";
        else o3.innerHTML += "OK (AS TEXT: <pre style='display: inline;'>"+htmlspecialchars(request.getResponseText())+")</pre><br/>";
        var xml = request.getResponseXML();
        var errReason = ncDOMParser_getError(xml);
        if (errReason)
          o3.innerHTML += "KO ("+errReason+")<br/>";
        else 
          o3.innerHTML += "OK (XML root node is '"+ncNodeTag(xml.documentElement)+"')<br/>";
      }
      else
        o3.innerHTML = "KO (Got Status "+request.getStatus()+" '"+request.getStatusText()+"')";
    }
  }
  </script>
<h3>Test 4: ncXmlHttpRequest test GET 200 XML Data ['data/xml_invalid.xml' file]</h3>
  <div id="test4"></div>
  <script language="Javascript" type="text/javascript">
  var o4 = document.getElementById('test4');
  o4.innerHTML = "Testing ...";

  // Send Request
  var request4 = new ncXmlHttpRequest();
  request4.setOnReadyStateChange(function() { test4cb(request4); });
  request4.open("GET", "data/xml_invalid.xml", true);
  request4.send();

  function test4cb(request)
  {
    if (request.getReadyState() == 4)
    {
      if (request.getStatus() == 200)
      {
        o4.innerHTML = "OK (200 '"+request.getStatusText()+"' Response got successfully)<br/>";
        if (request.getResponseText()==null) o4.innerHTML += "KO (No response Text ?)<br/>";
        else o4.innerHTML += "OK (AS TEXT: <pre style='display: inline;'>"+htmlspecialchars(request.getResponseText())+")</pre><br/>";
        var xml = request.getResponseXML();
        var errReason = ncDOMParser_getError(xml);
        if (errReason)
          o4.innerHTML += "OK, we detected the error ("+errReason+")<br/>";
        else 
          o4.innerHTML += "KO, we haven't seen the error (XML root node is '"+ncNodeTag(xml.documentElement)+"')<br/>";
      }
      else
        o4.innerHTML = "KO (Got Status "+request.getStatus()+" '"+request.getStatusText()+"')";
    }
  }
  </script> 
  
<h3>Test 5: ncXmlHttpRequest test GET 200 XML Data ['data/xml_header.php' file]</h3>
  <div id="test5"></div>
  <script language="Javascript" type="text/javascript">
  var o5 = document.getElementById('test5');
  o5.innerHTML = "Testing ...";

  // Send Request
  var request5 = new ncXmlHttpRequest();
  request5.setOnReadyStateChange(function() { test5cb(request5); });
  request5.open("GET", "data/xml_header.php", true);
  request5.send();

  function test5cb(request)
  {
    if (request.getReadyState() == 4)
    {
      if (request.getStatus() == 200)
      {
        o5.innerHTML = "OK (200 '"+request.getStatusText()+"' Response got successfully)<br/>";
        if (request.getResponseText()==null) o5.innerHTML += "KO (No response Text ?)<br/>";
        else o5.innerHTML += "OK (AS TEXT: <pre style='display: inline;'>"+htmlspecialchars(request.getResponseText())+")</pre><br/>";
        var xml = request.getResponseXML();
        var errReason = ncDOMParser_getError(xml);
        if (errReason)
          o5.innerHTML += "KO ("+errReason+")<br/>";
        else 
          o5.innerHTML += "OK (XML root node is '"+ncNodeTag(xml.documentElement)+"')<br/>";
      }
      else
        o5.innerHTML = "KO (Got Status "+request.getStatus()+" '"+request.getStatusText()+"')";
    }
  }
  </script>
    
<h3>Test 6: ncXmlHttpRequest test GET 200 XML Data ['data/xml_noheader.php' file]</h3>
  <div id="test6"></div>
  <script language="Javascript" type="text/javascript">
  var o6 = document.getElementById('test6');
  o6.innerHTML = "Testing ...";

  // Send Request
  var request6 = new ncXmlHttpRequest();
  request6.setOnReadyStateChange(function() { test6cb(request6); });
  request6.open("GET", "data/xml_noheader.php", true);
  request6.send();

  function test6cb(request)
  {
    if (request.getReadyState() == 4)
    {
      if (request.getStatus() == 200)
      {
        o6.innerHTML = "OK (200 '"+request.getStatusText()+"' Response got successfully)<br/>";
        if (request.getResponseText()==null) o6.innerHTML += "KO (No response Text ?)<br/>";
        else o6.innerHTML += "OK (AS TEXT: <pre style='display: inline;'>"+htmlspecialchars(request.getResponseText())+"</pre>)<br/>";
        var xml = request.getResponseXML();
        var errReason = ncDOMParser_getError(xml);
        if (errReason)
          o6.innerHTML += "OK, error because it's not XML ("+errReason+")<br/>";
        else 
          o6.innerHTML += "KO (XML root node is '"+ncNodeTag(xml.documentElement)+"')<br/>";
      }
      else
        o6.innerHTML = "KO (Got Status "+request.getStatus()+" '"+request.getStatusText()+"')";
    }
  }
  </script>

<h3>Test 7: ncDOMParser test GET 200 XML Data ['data/xml_noheader.php' file]</h3>
  <div id="test7"></div>
  <script language="Javascript" type="text/javascript">
  var o7 = document.getElementById('test7');
  o7.innerHTML = "Testing ...";

  // Send Request
  var request7 = new ncDOMParser();
  request7.parseFile("data/xml_noheader.php", true, test7cb, null);

  function test7cb(DOMDocument)
  {
    var errReason = ncDOMParser_getError(DOMDocument);
    o7.innerHTML = "";
    if (errReason)
      o7.innerHTML += "KO ("+errReason+")<br/>";
    else 
      o7.innerHTML += "OK (XML root node is '"+ncNodeTag(DOMDocument.documentElement)+"')<br/>";
  }
  </script>
  
     
<h3>Test 8: ncXmlHttpRequest test GET 200 Text Data with echo</h3>
  <div id="test8"></div>
  <script language="Javascript" type="text/javascript">
  var o8 = document.getElementById('test8');
  o8.innerHTML = "Testing ...";

  // Send Request
  var request8 = new ncXmlHttpRequest();
  request8.setOnReadyStateChange(function() { test8cb(request8); });
  request8.open("GET", "data/text_echo.php?data=helloyou", true);
  request8.send();

  function test8cb(request)
  {
    if (request.getReadyState() == 4)
    {
      if (request.getStatus() == 200)
      {
        o8.innerHTML = "OK (200 '"+request.getStatusText()+"' Response got successfully)<br/>";
        if (request.getResponseText()==null) o8.innerHTML += "KO (No response Text ?)<br/>";
        else 
        {
          var rt = request.getResponseText();
          if (rt == "helloyou")
          {
            o8.innerHTML += "OK (AS TEXT: <pre style='display: inline;'>"+rt+"</pre>, good echo)<br/>";
          }
          else
          {
            o8.innerHTML += "KO (AS TEXT: <pre style='display: inline;'>"+rt+"</pre>, but wrong echo)<br/>";
          }
        }
        var xml = request.getResponseXML();
        if (xml!=null) o8.innerHTML += "KO (Interpreted as XML ???)<br/>";
        else o8.innerHTML += "OK (No interpretation as XML)<br/>";
      }
      else
        o8.innerHTML = "KO (Got Status "+request.getStatus()+" '"+request.getStatusText()+"')";
    }
  }
  </script>  
<h3>Test 9: ncDOMParser test POST 200 XML Data with echo</h3>
  <div id="test9"></div>
  <script language="Javascript" type="text/javascript">
  var o9 = document.getElementById('test9');
  o9.innerHTML = "Testing ...";

  // Send Request
  var request9 = new ncDOMParser();
  request9.parseFile("data/xml_echo.php", true, test9cb, Array(Array("data", "<myxmlnode value='customvalue'/>")));

  function test9cb(DOMDocument)
  {
    var errReason = ncDOMParser_getError(DOMDocument);
    o9.innerHTML = "";
    if (errReason)
      o9.innerHTML += "KO ("+errReason+")<br/>";
    else 
      o9.innerHTML += "OK (XML root node is '"+ncNodeTag(DOMDocument.documentElement)+"')<br/>";
  }
  </script> 
</body>
</html>
