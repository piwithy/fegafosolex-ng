/* 
  ncDOMParser
  Navigator-independant XML Parser
*/
function ncDOMParser(crossDomainFeature)
{
	if (crossDomainFeature==true) this.crossDomainFeature=true;
	else this.crossDomainFeature=false;

  this.XmlHttpRequestObj = null;
  this.asynchronous = false;
  this.parseFile = ncDOMParser_parseFile;
  this.parseBuffer = ncDOMParser_parseBuffer;
  this.private_RequestCallback = ncDOMParser_private_RequestCallback;
  this.private_processRequestResult = ncDOMParser_private_processRequestResult;
  this.abort = ncDOMParser_abort;
	this.destroy = ncDOMParser_destroy;
  /* static functions: */
  // ncDOMParser_getError(DOMDocument) // return null if no error, detailed message if error
  // ncDOMParser_createDocument()
}

function ncDOMParser_destroy()
{
	if (this.XmlHttpRequestObj)
	{
		this.XmlHttpRequestObj.destroy();
		delete this.XmlHttpRequestObj;
		this.XmlHttpRequestObj=null;
	}

	return null;
}

function ncDOMParser_parseFile(fileName, asynchronous, asynchronousCallback, POSTVariablesArray, HeadersArray)
{
  if (asynchronous === undefined) this.asynchronous=false;
  else this.asynchronous = asynchronous;
  if ((this.asynchronous) && (asynchronousCallback === undefined))
  {
    alert("ncDOMParse::parseFile ERROR : callback function is not defined, using asynchronous mode");
    return null;
  }
	var fileNameStandardized = fileName;
	fileNameStandardized = fileNameStandardized.replace(/\\/g, "/");
	
	if (this.XmlHttpRequestObj)
	{
		this.XmlHttpRequestObj.destroy();
		delete this.XmlHttpRequestObj;
		this.XmlHttpRequestObj=null;
	}
	
  this.XmlHttpRequestObj = new ncXmlHttpRequest(this.crossDomainFeature);
  
  var thisForFunc = this;
  if (this.asynchronous) 
		this.XmlHttpRequestObj.setOnReadyStateChange(function() { thisForFunc.private_RequestCallback(asynchronousCallback); });
  
  if (HeadersArray)
  {
    this.XmlHttpRequestObj.addHeadersFromArray(HeadersArray);
  }
  
  var params="";
  if (POSTVariablesArray)
  {
    this.XmlHttpRequestObj.open("POST", fileNameStandardized, this.asynchronous);
    params = this.XmlHttpRequestObj.makeContentFromPOST(POSTVariablesArray);
  }
  else
  {
    this.XmlHttpRequestObj.open("GET", fileNameStandardized, this.asynchronous);
  }
  try 
  { // Fix chrome 'network errors' if host not responding
    this.XmlHttpRequestObj.send(params);
  } 
  catch(err)
  {
    // meme process que pour une erreur 404 (cf ci dessous)
    this.XmlHttpRequestObj.destroy();
    delete this.XmlHttpRequestObj;
    this.XmlHttpRequestObj = null;
    return null;
  }
  
  if (!this.asynchronous)
  {
		if (this.XmlHttpRequestObj.getStatus() >= 400) /* error, we don't want 404 page generated from serveur as XML input... because it's HTML */
		{
		  this.XmlHttpRequestObj.destroy();
		  delete this.XmlHttpRequestObj;
			this.XmlHttpRequestObj = null;
			return null;
		}
    /* Immediately process the result */
    var DOMDocument = this.private_processRequestResult(this.XmlHttpRequestObj);
		this.XmlHttpRequestObj.destroy();
		delete this.XmlHttpRequestObj;
		this.XmlHttpRequestObj = null;
    return DOMDocument;
  }
  else
	{
    return null; /* Result will be given in callback */
	}
}

function ncDOMParser_abort()
{
  if (this.asynchronous == false)
  {
    alert("ncDOMParser Trying to use abort() on synchronous DOM Parser");
    return;
  }
  if (this.XmlHttpRequestObj==null)
  {
    alert("ncDOMParser Abort: No sub request object !");
    return;
  }
  this.XmlHttpRequestObj.abort();
}

function ncDOMParser_private_processRequestResult(xmlHttpRequestObj)
{
	/* MDU: on n'utilise pas getResponseXML() car on est pas sur de la version de MSXML utilisee !
  ... or Content-Type was not set to "text/xml" by server */
	var xmlBuffer = xmlHttpRequestObj.getResponseText();
  return this.parseBuffer(xmlBuffer);
}

function ncDOMParser_private_RequestCallback(asynchronousCallback)
{
  var DOMDocument = null;
  if (this.XmlHttpRequestObj.getReadyState() == 4)
  {
    if (this.XmlHttpRequestObj.getStatus() < 400)
		{
      DOMDocument = this.private_processRequestResult(this.XmlHttpRequestObj);
		}
    asynchronousCallback(DOMDocument);
		if (this.XmlHttpRequestObj)
		{
			this.XmlHttpRequestObj.destroy();
			delete this.XmlHttpRequestObj;
			this.XmlHttpRequestObj=null;
		}
  }
}

function ncDOMParser_createDocument() /* static */
{
	var obj = null;
  
  var MSXMLnames = null;
	var stringversion=navigator.appVersion;
	var MSIEpos=stringversion.indexOf('MSIE');
	if (MSIEpos != -1)
	{
    /* IE <= 10 */
    MSIEpos+=4;
    var IEVersion=parseFloat(stringversion.substring(MSIEpos));
    if (IEVersion == 10)
    {
      MSXMLnames = [
          "Msxml2.DOMDocument.6.0"
      ];
    }
    else
    {
      MSXMLnames = [
          "Msxml2.DOMDocument.3.0", // supports dataIslands
          "Msxml2.DOMDocument",
          "Microsoft.XMLDOM"
      ];
    }
  }
  else
  {
    /* Chrome/Firefox ou IE >= 11 
      On en profite pour etre moderne, et en plus ca nous pose des soucis pour la detection du support des dataIslands */
    MSXMLnames = [
        "Msxml2.DOMDocument.6.0"
    ];
  }
  
  for(var i in MSXMLnames)
  {
    try
    {
      obj = new ActiveXObject(MSXMLnames[i]);
      if (obj)
      {
        obj.async=false; /* default is async=false */
        return obj;
      }
    }
    catch(e)
    {
    }
  }
		
	if (document.implementation.createDocument)
	{
    /* attention, reconnu par IE9 mais ne renvoie pas un objet correct (renvoit un IE DOM et non pas un MSXML DOM)
       et cet objet ne gere pas le XSLT... */
		obj = document.implementation.createDocument("", "", null);
    if (obj)
    {
      obj.async = false; /* default is async=false */
      return obj;
    }
	}

	alert("ncDOMParser ERROR: No DOMDocument object is supported by your internet browser !");
	return null;
}

function ncDOMParser_parseBuffer(xmlBuffer)
{
  var DOMDocument = ncDOMParser_createDocument();
	try
	{
		/* IE */
  	DOMDocument.loadXML(xmlBuffer);
  	return DOMDocument;
	}
	catch (e)
	{
	}
	delete DOMDocument;

	if (window.DOMParser) /* attention, existe sous IE9 mais le DOMDocument ne propose pas selectSingleNode etc...*/
  {
    /* firefox style...*/
    var oParser = new DOMParser();
    DOMDocument = oParser.parseFromString(xmlBuffer, "text/xml");
    oParser=null;
    return DOMDocument;
  }
  alert("ncDOMParser ERROR: No DOMDocument object is supported by your internet browser !");
	return null;
}

function ncDOMParser_getError(DOMDocument)
{
  if (DOMDocument == null)
    return "Unknown error (1). May be not existing file ?";
  if (DOMDocument.parseError && (DOMDocument.parseError.reason != ""))
  {
    /* IE Style of error */
    return "XML Error: " + DOMDocument.parseError.reason + "\n" + 
            "At line " + DOMDocument.parseError.line + ": " +
            "Source: " + DOMDocument.parseError.srcText;
  }
  if (DOMDocument.documentElement == null)
    return "Unknown error (2). May be not existing file ?";

  /* getElementsByTagName: fix for Chrome because parsererror is not the root node*/
  var errors = DOMDocument.getElementsByTagName("parsererror");
  if (errors.length)
  {
    /* Firefox Style of error */
    var errStr = ncNodeDump(errors[0]);
    return errStr;
  }
  return null;
}