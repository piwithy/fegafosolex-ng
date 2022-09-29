/* 
  ncXmlHttpRequest
  Navigator-independant wrapper of classical "XmlHttpRequest"
*/
function ncXmlHttpRequest(crossDomainFeature)
{
  this.navigatorXmlHttpRequest = null;
	if (crossDomainFeature==true) this.crossDomainFeature=true;
	else this.crossDomainFeature=false;
	
  /* taken from http://fr.wikipedia.org/wiki/XMLHttpRequest */
  if (window.XMLHttpRequest)
	{
		try
		{
			this.navigatorXmlHttpRequest = new XMLHttpRequest();
			if (this.crossDomainFeature)
			{
				if ("withCredentials" in this.navigatorXmlHttpRequest) 
				{
					/* ok */
				}
				else if (typeof XDomainRequest != "undefined") 
				{
					/*try IE */
					delete this.navigatorXmlHttpRequest;
					this.navigatorXmlHttpRequest =  new XDomainRequest();
				}
			}
		}
		catch(e)
		{
			// probleme avec IE8.
		}
	}
	
  if (this.navigatorXmlHttpRequest == null)
  {
    var names = [
        "Msxml2.XMLHTTP.6.0",
        "Msxml2.XMLHTTP.3.0",
        "Msxml2.XMLHTTP",
        "Microsoft.XMLHTTP"
    ];
    for(var i in names)
    {
        try
        {
          this.navigatorXmlHttpRequest = new ActiveXObject(names[i]);
          break;
        }
        catch(e)
        {
        }
    }
  }
  
  if (this.navigatorXmlHttpRequest == null)
    alert("ncXmlHttpRequest ERROR: No XmlHttpRequest object is supported by your internet browser !");
    
  /* Implemented features */
  this.open = ncXmlHttpRequest_open;
  this.send = ncXmlHttpRequest_send;
  this.setOnReadyStateChange = ncXmlHttpRequest_setOnReadyStateChange;
  this.setRequestHeader = ncXmlHttpRequest_setRequestHeader;
  this.getResponseText = ncXmlHttpRequest_getResponseText;
  this.getResponseXML = ncXmlHttpRequest_getResponseXML;
  this.getReadyState = ncXmlHttpRequest_getReadyState;
  this.getStatus = ncXmlHttpRequest_getStatus;
  this.getStatusText = ncXmlHttpRequest_getStatusText;
  /* pTools specific */
  this.makeContentFromPOST = ncXmlHttpRequest_makeContentFromPOST;
  this.addHeadersFromArray = ncXmlHttpRequest_addHeadersFromArray;
	this.destroy = ncXmlHttpRequest_destroy;
}

function ncXmlHttpRequest_destroy()
{
	if (this.navigatorXmlHttpRequest)
	{
		try
		{
			this.navigatorXmlHttpRequest.onreadystatechange = null;
		}
		catch(e)
		{
			// fail sometimes on IE8
		}
		delete this.navigatorXmlHttpRequest;
		this.navigatorXmlHttpRequest = null;
	}
	return null;
}

function ncXmlHttpRequest_open(sMethod, sUrl, bAsync, sUser, sPassword)
{
  if (this.navigatorXmlHttpRequest)
	{
    this.navigatorXmlHttpRequest.open(sMethod, sUrl, bAsync, sUser, sPassword);
		if (this.crossDomainFeature)
			this.navigatorXmlHttpRequest.withCredentials=true;
	}
  else
    alert("ncXmlHttpRequest ERROR: OPEN Failed. No XmlHttpRequest object");
}

function ncXmlHttpRequest_send(varBody)
{
  if (this.navigatorXmlHttpRequest)
    this.navigatorXmlHttpRequest.send(varBody);
  else
    alert("ncXmlHttpRequest ERROR: SEND Failed. No XmlHttpRequest object");
}

function ncXmlHttpRequest_setOnReadyStateChange(fFunction)
{
  if (this.navigatorXmlHttpRequest)
    this.navigatorXmlHttpRequest.onreadystatechange = fFunction;
  else
    alert("ncXmlHttpRequest ERROR: SETONREADYSTATECHANGE Failed. No XmlHttpRequest object");
}

function ncXmlHttpRequest_setRequestHeader(sName, sValue)
{
  if (this.navigatorXmlHttpRequest)
    this.navigatorXmlHttpRequest.setRequestHeader(sName, sValue);
  else
    alert("ncXmlHttpRequest ERROR: SETREQUESTHEADER Failed. No XmlHttpRequest object");
}

function ncXmlHttpRequest_getResponseText()
{
  if (this.navigatorXmlHttpRequest)
    return this.navigatorXmlHttpRequest.responseText;
  else
    return null;
}

function ncXmlHttpRequest_getResponseXML() /* Wants Content-Type "text/xml" from server to work */
{
  if (this.navigatorXmlHttpRequest)
  {
    var XMLResponse = this.navigatorXmlHttpRequest.responseXML;
    if (XMLResponse && XMLResponse.documentElement)  /* Won't return empty XML Document, always NULL in case of error */
      return XMLResponse;
    else
      return null;
  }
  else
    return null;
}

function ncXmlHttpRequest_getReadyState()
{
  if (this.navigatorXmlHttpRequest)
    return this.navigatorXmlHttpRequest.readyState;
  else
    return 0;
}

function ncXmlHttpRequest_getStatus()
{
  if (this.navigatorXmlHttpRequest)
    return this.navigatorXmlHttpRequest.status;
  else
    return 501;
}

function ncXmlHttpRequest_getStatusText()
{
  if (this.navigatorXmlHttpRequest)
    return this.navigatorXmlHttpRequest.statusText;
  else
    return "Not Implemented";
}

function ncXmlHttpRequest_makeContentFromPOST(POSTVariablesArray)
{
  var params = "";
  for (var POSTV = 0; POSTV < POSTVariablesArray.length; POSTV++)
  {
    var n = POSTVariablesArray[POSTV][0];
    var c = POSTVariablesArray[POSTV][1];
    if (params!= "") params += "&";
    params += encodeURIComponent(n)+ "="+encodeURIComponent(c);
  }
  if (params.length)
  {
    this.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    this.setRequestHeader("Content-length", params.length);
  }
  return params;
}

function ncXmlHttpRequest_addHeadersFromArray(HeadersArray)
{
  for (var HV = 0; HV < HeadersArray.length; HV++)
  {
    var n = HeadersArray[HV][0];
    var c = HeadersArray[HV][1];
    this.setRequestHeader(n,c);
  }
}