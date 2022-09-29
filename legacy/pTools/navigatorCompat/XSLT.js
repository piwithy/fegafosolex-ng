/*
 * XSLT : XSL Transformations
 */
function compatibleTransformNode(node, oXslDom)
{
  if (node==null) return null;
  
  try
  {
    /* FIREFOX/CHROME */
    var oProcessor = new XSLTProcessor();
    oProcessor.importStylesheet(oXslDom);
    
    // MDU : N'est pas sense marcher avec Chrome ? : Dire pourquoi car la maintenant je constate que ca marche
    var oResultDom = oProcessor.transformToDocument(node);
    var sResult = ncNodeDump(oResultDom.documentElement);
	
    // marche avec Chrome:
    //var fragment = oProcessor.transformToFragment(node, document); // NE RENVOIT PAS DU XML MAIS DE L'HTML SOUS CHROME (pas de closing tags)
    //var sResult = ncNodeDump(fragment);
    
    if (sResult.indexOf("<transformiix:result") > -1) {
        sResult = sResult.substring(sResult.indexOf(">") + 1, 
                                    sResult.lastIndexOf("<"));
    }
    delete oProcessor;
    return sResult;      
  }
  catch (e)
  {
    /* IE */
    var liResult = "";
    try
    {
      liResult = node.transformNode(oXslDom);
    }
    catch (e)
    {
      //alert(compatibleGetErrorDesc(e));
    }
    return liResult;
  }
}

function makeNoNamespaceExpression(expression)
{
  var arr = expression.split('/');
  correctEXPR="";
	for (var i=0; i < arr.length; i++)
	{
		var elem = arr[i];
		if (elem != "")
		{
      var elemNoSelector = elem;
      var posSelector = elemNoSelector.indexOf('[');
      if (posSelector!=-1)
        elemNoSelector = elemNoSelector.substring(0, posSelector);
			if ((elemNoSelector != ".") && (elemNoSelector != '*') && (elemNoSelector != "..") && (elemNoSelector.indexOf(':')==-1))
			{
				var andPart = "";
				if (posSelector!=-1)
				{
					var afterSelection = elem.substring(posSelector);
					elem=elem.substring(0, posSelector);
					andPart = "and ("+afterSelection.substring(1, afterSelection.length-1)+")";
				}
				correctEXPR += "*[local-name()='"+elem+"'";
				if (andPart!="")
					correctEXPR += andPart;
				correctEXPR += "]";
			}
			else
			{
				// ya deja un namespace de specifie
				correctEXPR += elem;
			}
			if (i < arr.length-1) correctEXPR+="/";
		}
		else
		{
			correctEXPR += "/";
		}
	}
  //alert("(2) So we changed expression to: "+correctEXPR);
	return correctEXPR;
}

// selectSingleNode / selectNodes  NOTA
// - expression est une expression XPath
// - ATTENTION l'expression "//node" veut dire chercher les nodes qui n'ont PAS de namespace
//	 Normalement il faut //*[local-name()='node']. Mais comme historiquement on a utilisé //node
//	 Alors ajout du parametre par defaut (namespaceProcessing=null ou namespaceProcessing=0) qui transforme //node en //*[local-name()='node']
//	 mais ce n'est pas standard. L'appel standard devrait etre compatibleSelectSingleNode(node, "//*[local-name()='node']", 1)
function compatibleSelectSingleNode(nodeOrDoc, expression, namespaceProcessing)
{
  if (nodeOrDoc==null) return null;
	if (namespaceProcessing==null) namespaceProcessing=0;
	
  var node = (nodeOrDoc.documentElement) ? nodeOrDoc.documentElement : nodeOrDoc;
  var doc = node.ownerDocument == null ? node : node.ownerDocument;

	var exprUsed = expression;
	if (!namespaceProcessing)
		exprUsed = makeNoNamespaceExpression(expression);

	try 
	{
		/* FIREFOX/CHROME */
    var oEvaluator = new XPathEvaluator();
    var oResult = null;
    try 
    {
      oResult = oEvaluator.evaluate(exprUsed, node, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null);
    }
    catch (e)
    {
      /* a priori: expression non valide */
      delete oEvaluator;
      return null;
    }
    //alert(oResult.singleNodeValue);
    if (oResult.singleNodeValue!=null && oResult.singleNodeValue.documentElement)
    {
      // document: ce n'est pas un noeud. on retourne NULL
      delete oEvaluator;
      return null;
    }
    else
    {
      // noeud ou null
      delete oEvaluator;
      return oResult.singleNodeValue; 
    }		
	}		
	catch (e)
	{
	}
  
	/* MSXML 6.0 now handles namespaces like firefox: */
	doc.setProperty("SelectionLanguage", "XPath");
	return nodeOrDoc.selectSingleNode(exprUsed);
}

function compatibleSelectNodes(nodeOrDoc, expression, namespaceProcessing)
{
  if (nodeOrDoc==null) return null;
	if (namespaceProcessing==null) namespaceProcessing=0;
	
  var node = (nodeOrDoc.documentElement) ? nodeOrDoc.documentElement : nodeOrDoc;
  var doc = node.ownerDocument == null ? node : node.ownerDocument;
  
	var exprUsed = expression;
	if (!namespaceProcessing)
		exprUsed = makeNoNamespaceExpression(expression);
	
	try 
	{
		/* FIREFOX/CHROME */
    var oEvaluator = new XPathEvaluator();
		
    var oResult = null;
    try 
    {
      oResult = oEvaluator.evaluate(exprUsed, node, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);    
    }
    catch (e)
    {
      /* a priori: expression non valide */
      delete oEvaluator;
      return null;
    }
  
    var aNodes = new Array;
    if (oResult != null) 
    {
      var oElement = oResult.iterateNext();
      while(oElement) 
      {
        aNodes.push(oElement);
        oElement = oResult.iterateNext();
      }
    }
    delete oEvaluator;
    return aNodes;  	
	}	
	catch (e)
	{
	}
	
	/* MSXML 6.0 now handles namespaces like firefox: */
	doc.setProperty("SelectionLanguage", "XPath");
  return nodeOrDoc.selectNodes(exprUsed);
}