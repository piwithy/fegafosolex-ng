/*
 * Data Island implementation for firefox/chrome and IE>=10 navigators
 * (This was normally IE<10 specific)
 */

function dataIsland_IsSupported()
{
	var stringversion=navigator.appVersion;
	var MSIEpos=stringversion.indexOf('MSIE');
	if (MSIEpos == -1)
	{
    /* Firefox, Chrome, ou IE11 */
    return false;
	}
	/* IE < 10 . On doit juste filtrer la version 10 */
	MSIEpos+=4;
	var IEVersion=parseFloat(stringversion.substring(MSIEpos));
	if (IEVersion < 10)
	{
    return true;
	}
	else
	  return false;
}

function afterupdate_IsSupported()
{
	var stringversion=navigator.appVersion;
	var MSIEpos=stringversion.indexOf('MSIE');
	if (MSIEpos == -1)
	{
    /* Firefox, Chrome, ou IE11 */
    return false;
	}
	/* IE < 9 . On doit juste filtrer la version 9 */
	MSIEpos+=4;
	var IEVersion=parseFloat(stringversion.substring(MSIEpos));
	if (IEVersion < 9)
	{
    return true;
	}
	else
	  return false;
}

/* Create and manipulate the data island: */
function dataIsland_get(islandNameWithoutSharp)
{
	var IslandHTMLElement = document.getElementById(islandNameWithoutSharp);
	var Island = null;
	if (dataIsland_IsSupported())
		Island = IslandHTMLElement.documentElement; // IE8
	else
		Island = IslandHTMLElement.JSObject;
	if (Island==null)
		alert("ERROR: Island #" + islandNameWithoutSharp + " not found !");
	return Island;
}

function dataIsland_createInHtml(htmlBody, islandName)
{
  var newIsland = document.createElement("xml");
  htmlBody.appendChild(newIsland);
  newIsland.setAttribute("id", islandName);
  newIsland.JSObject = null; // on va stocker tout l'arbre XML la dedans, car sous FF/Chrome on perd la casse des attributs si on insere direct dans le HTML...
}
function dataIsland_updateIslandFromExternalDocument(islandNode, theDoc)
{
  if (dataIsland_IsSupported())
    islandNode.documentElement = theDoc.documentElement; // ca vraiment CRADE!
  else
  {
    islandNode.JSObject = theDoc.documentElement.cloneNode(true);
  }
}
function dataIsland_updateExternalNodeFromIsland(islandNode, theExternalNode)
{
  if (dataIsland_IsSupported())
  {
    islandNode = islandNode.documentElement; // ARCHI CRADE
    var parentNode = theExternalNode.parentNode;
    var newNode = islandNode.cloneNode(true);
    parentNode.replaceChild(newNode, theExternalNode); // MDU j'utilise ceci car sinon je ne peux pas faire save sur le noeud le plus haut (cf scheduling)
  }
  else
  {
    theExternalNode.parentNode.replaceChild(islandNode.JSObject.cloneNode(true), theExternalNode);
  }
}

/* Initialize function */
function dataIsland_start(rootElement, HTMLElementUpdateHandler)
{
  /* juste pour ie ... */
  if (HTMLElementUpdateHandler) /* si on en veut, c'est un parametre facultatif */
  {
    var dataSrc = null;
    try
    {
      dataSrc = rootElement.getAttribute("datasrc");
    }
    catch (e)
    {
      /* a cause d'un fucking bug d'ie8 qui ne veut pas ca sur le noeud <OPTION> (alors que c'est bien un nodeType 1) */
    }
    
    if (dataSrc) /* optim; les elements normaux (sans attributs datasrc) n'interviennent pas dans les data islands */
    {
      if (afterupdate_IsSupported())
      {
        //alert("attach 'afterupdate' event to "+ncNodeDump(rootElement));
        compatibleAttachEvent(rootElement,"afterupdate",HTMLElementUpdateHandler); /*ie9, ie8 and less */
      }
      else
      {
        //alert("attach 'change' event to "+ncNodeDump(rootElement));
        if (rootElement.getAttribute("disabled") != null)
        {
          // Gros probleme, sinon l'evenement 'change', lorsqu'appele via javascript, ne sera pas propage
          // et donc l'island ne sera pas modifiee... j'ai eu du mal a comprendre lorsque c'est arrivé,
          // donc je met un alert visible dès qu'on a ce cas pourri, histoire de ne pas le balayer sous le tapis
          alert("WARNING, 'disabled' attribute has been set on HTMLElement. Data Island won't work on "+ncNodeDump(rootElement));
        }
        if (!dataIsland_IsSupported())
          compatibleAttachEvent(rootElement, 'change', dataIsland_private_OnChangeHTMLElement);
        compatibleAttachEvent(rootElement,"change",HTMLElementUpdateHandler);      /* recent navigators and chrome/firefox */
      }
    }
  }

	/* l'element courant */
	if (!dataIsland_IsSupported())
    dataIsland_updateElementFromIsland(rootElement, false/*cond*/, null/*condelem*/);
   
  /* et les fils */
	for(var i = 0; i < rootElement.childNodes.length; i++) 
	{
    var child = rootElement.childNodes[i];
    if (child.nodeType != 1)
			continue;
			
		dataIsland_start(child, HTMLElementUpdateHandler);
  }  
}

/*
 * PRIVATE FUNCTIONS
 */

/* When we change a HTML element, we need to change other elements values */
function dataIsland_private_updateAllHtmlElementsOnOtherChange(rootElement, condElement)
{
	/* l'element courant */
	dataIsland_updateElementFromIsland(rootElement, true/*iscond*/, condElement);
   
  /* et les fils */
	for(var i = 0; i < rootElement.childNodes.length; i++) 
	{
    var child = rootElement.childNodes[i];
    if (child.nodeType != 1)
			continue;
			
		dataIsland_private_updateAllHtmlElementsOnOtherChange(child, condElement);
  }  
}

// celle la n'est pas privee, utilisee par compatibleAddOption
function dataIsland_updateElementFromIsland(HTMLElement, isCondUpdate, condElement)
{
	var dataSrc = HTMLElement.getAttribute("datasrc");
  var dataFld = HTMLElement.getAttribute("datafld");
	if (dataSrc && dataFld)
	{
		if (isCondUpdate)
		{
			/* Only update if same dataSrc and dataFld */
			var dataSrc0 = condElement.getAttribute("datasrc");
			var dataFld0 = condElement.getAttribute("datafld");
			if (!(dataSrc0 && dataFld0))
				return;
			if (dataSrc0 != dataSrc)
				return;
			if (dataFld0 != dataFld)
				return;
		}
		/* Get Island */
		var IdentifierWithoutSharp = dataSrc.substring(1);
		var Island = dataIsland_get(IdentifierWithoutSharp);
		if (Island)
		{
			/* Get the Island Value */
			var IslandValue = Island.getAttribute(dataFld);
			setHTMLElementValue(HTMLElement, IslandValue, 0/*no change callback, else enter a loop...*/);
		}
	}
}

function dataIsland_private_updateValueFromHTMLElement(HTMLElement)
{
	var dataSrc = HTMLElement.getAttribute("datasrc");
  var dataFld = HTMLElement.getAttribute("datafld");
	if (dataSrc && dataFld)
	{
		/* Get Island */
		var IdentifierWithoutSharp = dataSrc.substring(1);
		var Island = dataIsland_get(IdentifierWithoutSharp);
		if (Island)
		{
      var val = getHTMLElementValue(HTMLElement);
      Island.setAttribute(dataFld, val);
		}
	}
}

function dataIsland_private_OnChangeHTMLElement(evt)
{
	var element = compatibleEventGetSrcElement(evt);
	// A Change Island
	dataIsland_private_updateValueFromHTMLElement(element);
	// B Search back and update the elements which depends on that data only
	// (Example: changing "name" of a subchannel, also updates "title" of the page)
	dataIsland_private_updateAllHtmlElementsOnOtherChange(document.body, element);
}