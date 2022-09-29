/* 
  ncNotDOMGetter
  Navigator-independant XMLHTTPRequest Wrapper to get not DOM (xml) content
*/
function ncNotDOMGetter(crossDomainFeature)
{
	if (crossDomainFeature==true) this.crossDomainFeature=true;
	else this.crossDomainFeature=false;

  this.XmlHttpRequestObj = null;
  this.asynchronous = false;
  this.getFile = ncNotDOMGetter_getFile;
  this.private_RequestCallback = ncNotDOMGetter_private_RequestCallback;
  this.private_processRequestResult = ncNotDOMGetter_private_processRequestResult;
  this.destroy = ncNotDOMGetter_destroy;
  this.abort = ncNotDOMGetter_abort;
  /* static functions: */
}

function ncNotDOMGetter_destroy()
{
	if (this.XmlHttpRequestObj)
	{
		this.XmlHttpRequestObj.destroy();
		delete this.XmlHttpRequestObj;
		this.XmlHttpRequestObj=null;
	}

	return null;
}

function ncNotDOMGetter_getFile(fileName, asynchronous, asynchronousCallback, POSTVariablesArray, HeadersArray)
{
  if (asynchronous === undefined) this.asynchronous=false;
  else this.asynchronous = asynchronous;
  if ((this.asynchronous) && (asynchronousCallback === undefined))
  {
    alert("ncNotDOMGetter::getFile ERROR : callback function is not defined, using asynchronous mode");
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
    var result = this.private_processRequestResult(this.XmlHttpRequestObj);
		this.XmlHttpRequestObj.destroy();
		delete this.XmlHttpRequestObj;
		this.XmlHttpRequestObj = null;
    return result;
  }
  else
	{
    return null; /* Result will be given in callback */
	}
}

function ncNotDOMGetter_abort()
{
  if (this.asynchronous == false)
  {
    alert("ncNotDOMGetter Trying to use abort() on synchronous DOM Parser");
    return;
  }
  if (this.XmlHttpRequestObj==null)
  {
    alert("ncNotDOMGetter Abort: No sub request object !");
    return;
  }
  this.XmlHttpRequestObj.abort();
}

function ncNotDOMGetter_private_processRequestResult(xmlHttpRequestObj)
{
	var ret = xmlHttpRequestObj.getResponseText();
  return ret;
}

function ncNotDOMGetter_private_RequestCallback(asynchronousCallback)
{
  var result = null;
  if (this.XmlHttpRequestObj.getReadyState() == 4)
  {
    if (this.XmlHttpRequestObj.getStatus() < 400)
		{
      result = this.private_processRequestResult(this.XmlHttpRequestObj);
		}
    asynchronousCallback(result);
		if (this.XmlHttpRequestObj)
		{
			this.XmlHttpRequestObj.destroy();
			delete this.XmlHttpRequestObj;
			this.XmlHttpRequestObj=null;
		}
  }
}

