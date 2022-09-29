function compatibleGetClientX(evt)
{
	var x = compatibleGetClientXWithoutScrollBar(evt);
  if (window.pageXOffset)
  {
    return x + window.pageXOffset;
  }
  else if (document.documentElement)
	{
		return x + document.documentElement.scrollLeft;
	}
	else if (document.body)
	{
		return x + document.body.scrollLeft;
	}
  alert("error compatibleGetClientX");
  return 0;
}

function compatibleGetClientY(evt)
{
	var y = compatibleGetClientYWithoutScrollBar(evt);
  if (window.pageYOffset)
  {
    return y + window.pageYOffset;
  }
  else if (document.documentElement)
	{
		return y + document.documentElement.scrollTop;
	}
	else if (document.body)
	{
		return y + document.body.scrollTop;
	}
  alert("error compatibleGetClientY");
  return 0;
}

function compatibleGetClientXWithoutScrollBar(evt)
{
  if (evt.pageX)
  {
    return evt.pageX + window.pageXOffset;
  }
  else if (evt.clientX)
  {
    if (document.documentElement)
    {
      return evt.clientX + document.documentElement.scrollLeft;
    }
    else if (document.body)
    {
      return evt.clientX + document.body.scrollLeft;
    }
  }
  //alert("error compatibleGetClientXWithoutScrollBar"); // ca arrive sur les bords de fenetre !!
  return 0;
}

function compatibleGetClientYWithoutScrollBar(evt)
{
  if (evt.pageY)
  {
    return evt.pageY;
  }
  else if (evt.clientY)
  {
    return evt.clientY;
  }
  //alert("error compatibleGetClientYWithoutScrollBar"); // ca arrive sur les bordes de fenetre !!
  return 0;
}

function compatibleGetEvent(evt)
{
  return evt || window.event;
}

function compatibleEventGetKeyCode(evt)
{
  return (evt.keyCode) ? (evt.keyCode) : (evt.which);
}

function compatibleEventGetSrcElement(evt)
{
  return (evt.srcElement) ? (evt.srcElement) : (evt.target);
}

function compatibleStopBubble(evt, doNotPreventDefault)
{
	if (doNotPreventDefault==null) doNotPreventDefault=true;
  if (evt.stopPropagation) 
  {
    evt.stopPropagation();
		if (doNotPreventDefault)
			evt.preventDefault();
  }
  else
  {
    /* vieux IE */
    //evt.cancelBubble is supported by IE - this will kill the bubbling process.
    evt.cancelBubble = true;
    evt.returnValue = false;
  }
}

function compatibleAttachEvent(el, event, func)
{
  if (el.addEventListener) 
  {
    el.addEventListener(event, func, false);
  }
  /*else if (el.attachEvent)    // MDU REMOVED (http://mlntn.com/2008/02/25/javascript-internet-explorers-attachevent-breaks-this/)
  {
    el.attachEvent('on'+event, func);
  }*/
  else 
  {
    el['on'+event] = func;
  }
}

function compatibleDetachEvent(el, event, func)
{
  if (el.removeEventListener) 
  {
    el.removeEventListener(event, func, false);
  }
  /*else if (el.attachEvent)    // MDU REMOVED (http://mlntn.com/2008/02/25/javascript-internet-explorers-attachevent-breaks-this/)
  {
    el.detachEvent('on'+event, func);
  }*/
  else 
  {
    el['on'+event] = null;
  }	
}

function compatibleFireEvent(el, event)
{
  if (document.createEvent && el.dispatchEvent)
  {
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent(event, true/*bubbles*/, true/*cancelable*/);
    el.dispatchEvent(evt);
  }
  else if (document.createEventObject)
  {
    var evt = document.createEventObject();
    el.fireEvent("on"+event, evt);
  }
  else
  {
    alert("Unsupported navigator for fire event method");
  }
}