/*
 * navigatorCompat.js
 * Fonctions standard traduites pour gerer la compatibilite entre les navigateurs
 */

/* object "Error" (throw Error) */
function compatibleGetErrorDesc(e)
{
  if (e.description)
    return e.description;
  if (e.message)
    return e.message;
  return e;
}

/*
 * Autres
 */
function initializeSelectBox(SelectBox)
{
  SelectBox.options.length=0;
  SelectBox.selectedIndex=-1;
}
 
function compatibleAddOption(SelectBox, OptionText, OptionValue)
{
	var elOptNew = document.createElement('option');
	elOptNew.value = "__temp__"; /* 'Unspecified error' bug fix in IE8/IE9, needs to have a temporary, untaken value */
	try
	{
		/* Firefox try ... */
    SelectBox.add(elOptNew, null); // standards compliant; doesn't work in IE8 (but seems to be in IE9)
  }
  catch(ex) 
  {
		/* IE try ... */
    SelectBox.add(elOptNew);
  }
	elOptNew.value = OptionValue;
	elOptNew.text = OptionText;
  
  /* snif... les select qui sont modifies APRES le .value ne sont pas remis a jour */
  dataIsland_updateElementFromIsland(SelectBox, false/*isinitial*/, false/*iscond*/, null/*condelem*/, null/*updhandler*/);
  return elOptNew;
}
 
function ncGetOuterHTML(node)
{
  return ncNodeDump(node);
}

function compatibleGetWindowSize() 
{
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) 
  {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } 
  else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) 
  {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } 
  else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) 
  {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  return [ myWidth, myHeight ];
}

function compatibleGetScrollXY() 
{
	var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) 
	{
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } 
	else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) 
	{
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } 
	else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) 
	{
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return [ scrOfX, scrOfY ];
}

function compatibleSetScrollXY(scrOfX, scrOfY)
{
	window.scrollTo(scrOfX, scrOfY);
}

function compatibleGetScrollWidthHeight()
{
  var scrOfX = 0, scrOfY = 0;
  if (document.documentElement && document.documentElement.scrollTop!=undefined) 
  {
    scrOfY = document.documentElement.scrollHeight;
    scrOfX = document.documentElement.scrollWidth;
  }
  else if (window.pageYOffset!=undefined) 
  {
      alert("wrong compatibleGetScrollWidthHeight not tested");
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  }
  else if (self.pageYOffset!=undefined) 
  {
  alert("wrong compatibleGetScrollWidthHeight not tested");
    scrOfY = self.pageYOffset;
    scrOfX = self.pageXOffset;
  }
  else 
  {
    scrOfY = document.body.scrollHeight;
    scrOfX = document.body.scrollWidth;
  }

  return [ scrOfX, scrOfY ];
}

function getObjectPosition(obj) 
{
  var curleft = 0;
  var curtop = 0;
  if (obj.offsetParent) 
  {
    do 
    {
      curleft += obj.offsetLeft;
      curtop += obj.offsetTop;
    } while (obj = obj.offsetParent);
  }
  return [curleft,curtop];
}

// selection objects will differ between browsers
function ncgetSelection () 
{
	if (document.selection) /* IE<=10*/
		return document.selection;
	if (window.getSelection) /*IE 11*/
		return window.getSelection;
	if (document.getSelection) /* ?? */
		return document.getSelection;
	alert('ncgetSelection unsupported');
  return null;
}

// range objects will differ between browsers
function ncgetRange () 
{
	var sel = ncgetSelection();
	if (sel.createRange)
		return sel.createRange(); /* IE */
	if (sel.getRangeAt)
		return sel.getRangeAt(0); /* Others */
	alert('ncgetRange unsupported');
  return null;
}

// abstract getting a parent container from a range
function ncparentContainer(range) 
{
	if (range.parentElement)
		return range.parentElement(); /* IE */
	if (range.commonAncestorContainer)
		return range.commonAncestorContainer; /* Others */
	alert('ncparentContainer unsupported');
  return null;
}

/* ncReplaceInnerHTML: goal is to avoid memory leaks when replacing old code */
function ncReplaceInnerHTML(obj, newcode)
{
  //createContextualFragment a creuser...

  /* First, free all objects using DOM */
  purge(obj, 1);
  /* then replace with ne new code */
  obj.innerHTML = newcode;
}

/* Thanks to http://javascript.crockford.com/memory/leak.html 
   This has been done for internet explorer memory leak bug work around
*/
function purge(myNode, isFirstNode) 
{
  /*if (myNode.text) CA MARCHE PAS BIEN, CA
    myNode.text = null; // text nodes... */

	var i, l, n;
  if (!isFirstNode) // pas touche au noeud parent
  {
    var myAttributes = myNode.attributes;
    if (myAttributes) 
    {
      for (i = myAttributes.length - 1; i >= 0; i--) 
      {
        n = myAttributes[i].name;
        var tp = typeof myNode[n];
        if ((tp === 'function') || (tp === 'object'))
        {
          try { delete myNode[n]; } catch (ex) { };
          try { myNode[n] = null; } catch (ex) { };
        }
        else if (tp === 'string')
        {
          // TENTATIVE de fix special "src" des images qui implique pour IE de garder l'image
          // en memoire (memory leak sur innerHTML = "<img .../>")
          try { myNode[n] = "about:blank"; } catch (ex) { };
        }
      }
    }
  }
	var myChilds = myNode.childNodes;
	if (myChilds) 
	{
    for (i = myChilds.length-1; i >= 0; i--) 
    {
      var mySubNode = myNode.childNodes[i];
      purge(mySubNode, 0);
      
      myNode.removeChild(mySubNode);
      delete mySubNode;
      mySubNode=null;
    }
	}
}

function setHTMLElementValue(HTMLElement, newValue, propagateChangeCallback)
{
	if (HTMLElement.tagName == "SPAN") HTMLElement.innerHTML = newValue;
	else if (HTMLElement.tagName == "INPUT") 
	{
		if (HTMLElement.getAttribute("type") == "radio")
		{
			if (HTMLElement.getAttribute("value")==newValue) HTMLElement.checked=true;
			else HTMLElement.checked=false;
		}
		else if (HTMLElement.getAttribute("type") == "checkbox")
		{
			if (HTMLElement.getAttribute("value")==newValue) HTMLElement.checked=true;
			else HTMLElement.checked=false;
		}
		else
		{
			/* default is to use "value" as input type="text" */
			HTMLElement.value = newValue;
		}
	}
	else if (HTMLElement.tagName == "SELECT") 
	{
		HTMLElement.value = newValue;
	}
	else if (HTMLElement.tagName == "TEXTAREA") 
	{
		HTMLElement.value = newValue;
	}
	else 
		alert(HTMLElement.tagName+" is not supported by setHTMLElementValue");
	/* In case of using dataislands, we need/want to fire change event: */
	if (propagateChangeCallback==null)
    propagateChangeCallback=1;
	if (propagateChangeCallback==1)
	{
		if (dataIsland_IsSupported())
		{
			// on doit mettre a jour la data island:
			if (afterupdate_IsSupported())
			{
				compatibleFireEvent(HTMLElement, "afterupdate");
			}
		}
		// mais dans tous les cas on doit triggerer le onchange, car on a non seulement la dataisland dans le cas non supporte
		//    mais aussi si nous meme on a mis qqch sur le onchange, du script specific hors data island, quoi
    compatibleFireEvent(HTMLElement, "change");
  }
}

function getHTMLElementValue(HTMLElement)
{
	if (HTMLElement.tagName == "SPAN") val = HTMLElement.innerHTML;
	else if (HTMLElement.tagName == "INPUT") 
	{
		if (HTMLElement.getAttribute("type") == "radio")
		{
			if (HTMLElement.checked) val=HTMLElement.getAttribute("value");
		}
		else if (HTMLElement.getAttribute("type") == "checkbox")
		{
      if (HTMLElement.checked) 
				val = HTMLElement.getAttribute("value");
      else 
      {
        if (HTMLElement.getAttribute("value") && (HTMLElement.getAttribute("value")=="1"))
          val="0";
        else
          val=null;
      }
		}
		else
		{
			/* default is to use "value" as input type="text" */
			val = HTMLElement.value;
		}
	}
	else if (HTMLElement.tagName == "SELECT") 
	{
		var selIndex = HTMLElement.selectedIndex;
		if (selIndex == -1)
      val="";
    else
      val = HTMLElement.options[selIndex].value;
		// apparemment ca marche pas sur les VIEUX IE ???? val = HTMLElement.value;
	}
	else 
		alert(HTMLElement.tagName+" is not supported by getHTMLElementValue");
	return val;
}

function getStyle(oElm, strCssRule)
{
	// IE9 Chrome OK,  IE8 et moins a verifier
	var strValue = "";
	if(document.defaultView && document.defaultView.getComputedStyle)
	{
			strValue = document.defaultView.getComputedStyle(oElm, "").getPropertyValue(strCssRule);
	}
	else if(oElm.currentStyle)
	{
			strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
					return p1.toUpperCase();
			});
			strValue = oElm.currentStyle[strCssRule];
	}
	return strValue;
}

function applySameColumnWidth(ObjectWithTRRef, ObjectWithTRApply)
{
	// IE9 Chrome OK,  IE8 et moins a verifier
	var allTRs = ObjectWithTRRef.getElementsByTagName("TR"); // pas compatibleSelectNodes sur un doc HTML
	var allTRsOfHeader = ObjectWithTRApply.getElementsByTagName("TR"); // pas compatibleSelectNodes sur un doc HTML
	if (allTRs.length)
	{
		var allTDs = allTRs[0].getElementsByTagName("TD"); // pas compatibleSelectNodes sur un doc HTML
		var allTDsOfHeader = allTRsOfHeader[0].getElementsByTagName("TD"); // pas compatibleSelectNodes sur un doc HTML
		if (allTDsOfHeader.length==0)
			allTDsOfHeader = allTRsOfHeader[0].getElementsByTagName("TH"); // pas compatibleSelectNodes sur un doc HTML

		if (allTDs.length != allTDsOfHeader.length)
		{
			alert("applySameColumnWidth: Not same number of columns !");
			return;
		}
		for (var a=0; a<allTDs.length; a++)
		{
			var obj = allTDs[a];
			var liPadLeft = parseInt(getStyle(obj, "padding-left"), 10);
			var liPadRight = parseInt(getStyle(obj, "padding-right"), 10);
			var liWidth = obj.clientWidth-liPadLeft-liPadRight;
			allTDsOfHeader[a].style.width = liWidth+"px";
		}
	}
}
