/*
 * Fonctions DOM
 */
function ncNodeTag(theNode)
{
  var result = null;

  if (theNode && theNode.nodeName && (theNode.nodeName.indexOf('#') == -1)) result = theNode.nodeName; /* Standard compliant, uppercase in HTML, original case in XML */
  else if (theNode && theNode.baseName) result = theNode.baseName; //IE XML
  else if (theNode && theNode.localName) result = theNode.localName; //Firefox
  
  if (result == "") result = null;
  return result;
}

function ncNodeDump(DOMNodeSource)
{
  var result = "";
  if (DOMNodeSource == null) result = "";
  else if (DOMNodeSource.xml) result = DOMNodeSource.xml; //IE
  else 
  {
    var libOk=0;
    try
    {
      var serializer = new XMLSerializer();
      result = serializer.serializeToString(DOMNodeSource);
      delete serializer;
      libOk=1;
    }
    catch (e)
    {
    }
    if (!libOk)
    {
      try
      {
        // on se la tente en HTML... mais c'est vraiment pour le debug...
        result = DOMNodeSource.outerHTML;
      }
      catch (e)
      {
        alert("NodeDump error, unknown object");
      }
    }
  }
  return result;
}

function ncCloneDocument(DOMDocSource)
{
	if (DOMDocSource==null)
		return null;
	/* on tente la maniere "standard" */
	var docCopy = DOMDocSource.cloneNode(true);
	if (docCopy)
		return docCopy;
	/* sans doutes Chrome ?? 
	  Pas trouve de solution plus elegante que de dumper et reparser :-( */
	var liParser = new ncDOMParser();
	var doc = liParser.parseBuffer(DOMDump(DOMDocSource));
	delete liParser;
	return doc;
}

function ncCreateContextualFragment(obj, code)
{
  /* defines the function for IE */
  if ((typeof Range !== "undefined") && !Range.prototype.createContextualFragment)
  {
    Range.prototype.createContextualFragment = function(html)
    {
        var frag = document.createDocumentFragment(), 
        div = document.createElement("div");
        frag.appendChild(div);
        div.outerHTML = html;
        return frag;
    };
  }
  /* now it is defined...*/
  return obj.createContextualFragment(code);
}

function ncReplaceNodeAttributes(dstNode, srcNode)
{
  /* on commence par enlever tout les attributs du noeud destination: */
  while (dstNode.attributes.length)
  {
    dstNode.removeAttribute(dstNode.attributes[0].nodeName);
  }
  /* et on rajoute ensuite les nouveaux attributs: */
  for (var a=0; a < srcNode.attributes.length; a++)
  {
    var att = srcNode.attributes[a];
    dstNode.setAttribute(att.nodeName, att.nodeValue);
  }
}
