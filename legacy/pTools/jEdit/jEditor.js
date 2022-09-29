/* The editor will use parentObject and create all its need inside it.
   Initial content of parentObject should be the initial content */
var jEditorStyle = null;

function jEditor(parentObject)
{
  /* members */
  this.HTMLParentObject = parentObject;
  this.configDocument = null;
  this.jtext = "";
  this.HTMLButtonZone = null;
  this.HTMLEditZone = null;
  this.HTMLSelInfo = null;
  this.targetFormName = null;
  this.actions = new Array();
  /* functions */
  this.start = jEditor_start;
  this.parseConfigFile = jEditor_parseConfigFile;
  this.parseDOM = jEditor_parseDOM;
  this.render = jEditor_render;
  this.unrender = jEditor_unrender;
  this.onSelect = jEditor_onSelect;
  this.onFocus = jEditor_onFocus;
  this.onBlur = jEditor_onBlur;
  this.subDump = jEditor_subDump;
  
  this.computeSelection = jEditor_computeSelection;

  this.onButtonClick = jEditor_onButtonClick;
  this.applyActionOnSelectedText = jEditor_applyActionOnSelectedText;
  
  this.currentSelectionStartPos = 0;
  this.currentSelectionEndPos = 0;
}

function jEditor_start()
{
  /* On s'approprie la zone visee */
  if (this.HTMLParentObject==null)
  {
    alert("jEditor parentObject is null");
    return;
  }
  this.targetFormName = this.HTMLParentObject.getAttribute("name");
  if (this.targetFormName==null)
  {
    alert("jEditor Missing 'name' on parentObject");
    return;
  }
  this.HTMLParentObject.removeAttribute("name");
  
  this.jtext = this.HTMLParentObject.innerHTML;
  ncReplaceInnerHTML(this.HTMLParentObject,"");
  
  this.HTMLButtonZone = document.createElement("div");
  this.HTMLParentObject.appendChild(this.HTMLButtonZone);

  this.HTMLEditZone = document.createElement("div");
  this.HTMLEditZone.contentEditable = true;
  this.HTMLEditZone.insertBrOnReturn = false;
  this.HTMLParentObject.appendChild(this.HTMLEditZone);
  
  this.HTMLSelInfo = document.createElement("textArea");
  this.HTMLParentObject.appendChild(this.HTMLSelInfo);
  this.HTMLSelInfo.setAttribute("name", this.targetFormName);
  this.HTMLSelInfo.setAttribute("rows", 10);
  this.HTMLSelInfo.setAttribute("cols", 40);

  if (this.configDocument==null)
    this.parseConfigFile(jEditor_Path+"defaultConfig.xml");

  /* Render the stored text */
  this.HTMLEditZone.innerHTML = this.render(this.jtext);
  
  /* Start the editor events handler */
  var obj = this;
  //compatibleAttachEvent(this.HTMLEditZone, "focus", function (ev) { ev = obj.onFocus(ev) });
  compatibleAttachEvent(this.HTMLEditZone, "blur", function (ev) { ev = obj.onBlur(ev) });
  compatibleAttachEvent(this.HTMLEditZone, "click", function (ev) { obj.computeSelection(); });
  compatibleAttachEvent(this.HTMLEditZone, "keyup", function (ev) { obj.computeSelection(); });

  compatibleAttachEvent(this.HTMLEditZone.ownerDocument, "mouseup", function (ev) { obj.computeSelection(); });

}

function jEditor_parseConfigFile(xmlFile)
{
  var ret = 0;
  var liParser = new ncDOMParser();
  var liDOMDocument = liParser.parseFile(xmlFile);
  var errStr = ncDOMParser_getError(liDOMDocument);
  if (errStr) alert(errStr);
  else
  {
    ret = this.parseDOM(liDOMDocument);
  }
  delete liParser;
  return ret;
}

function jEditor_parseDOM(DOMDoc)
{
  var rootNode = DOMDoc.documentElement;
  if (ncNodeTag(rootNode) != "jEditor")
  {
    alert("jEditor::parseDOM XML Root node must be 'jEditor'");
    return 0;
  }
  else
  {
    for (var c=0; c < rootNode.childNodes.length; c++)
    {
      var child = rootNode.childNodes[c];
      if (ncNodeTag(child)==null)
        continue; /* not a node */
      if (ncNodeTag(child) == "action")
      {
        var newAction = new jAction();
        if (!newAction.parse(child, this.HTMLButtonZone))
        {
          delete newAction;
          return 0;
        }
        this.actions[this.actions.length] = newAction;

        if (newAction.buttonObject)
        {
          var obj=this;
          compatibleAttachEvent(newAction.buttonObject, "click", function (ev) { ev = obj.onButtonClick(ev) });
        }
      }
      else if (ncNodeTag(child) == "buttonZone")
      {
        this.HTMLButtonZone.className = child.getAttribute("className");
      }
      else if (ncNodeTag(child) == "editZone")
      {
        this.HTMLEditZone.className = child.getAttribute("className");
      }
      else if (ncNodeTag(child) == "style")
      {
        /* We check that we don't have already defined 'style' */
        if (jEditorStyle==null)
        {
          jEditorStyle = ncNodeDump(child.childNodes[0]);
          var nodeCSS = document.createElement("style");
          nodeCSS.setAttribute("type", "text/css");
          nodeCSS.innerHTML = jEditorStyle;
          this.HTMLParentObject.appendChild(nodeCSS);
        }
      }
      else
      {
        alert("jEditor::parseDOM Unknown node '"+ncNodeTag(child)+"'");
        return 0;
      }
    }
  }
  
  /* Here we are OK */
  this.configDocument = DOMDoc;
  return 1;
}

function jEditor_render(jtext)
{
  for (var a=0; a < this.actions.length; a++)
  {
    var act = this.actions[a];
    var expr = act.expression;
    var rgxstr = escapeRegEx("["+expr.PTEXT+"]")+"(.*?)"+escapeRegEx("[/"+expr.PTEXT+"]"); /* OUI ! Il faut mettre *? pour qu'il prenne le texte le plus court possible */
    var rpl = "<"+expr.EDITORHTML+">$1</"+expr.EDITORHTML+">";
    var regex = new RegExp(rgxstr, "g");
    jtext = jtext.replace(regex, rpl);
    delete regex;
  }
  return jtext;
}

function jEditor_unrender(jhtml)
{
  for (var a=0; a < this.actions.length; a++)
  {
    var act = this.actions[a];
    var expr = act.expression;
    var rgxstr = escapeRegEx("<"+expr.EDITORHTML+">")+"(.*?)"+escapeRegEx("</"+expr.EDITORHTML+">"); /* OUI ! Il faut mettre *? pour qu'il prenne le texte le plus court possible */
    var rpl = "["+expr.PTEXT+"]$1[/"+expr.PTEXT+"]";
    var regex = new RegExp(rgxstr, "g");
    jhtml = jhtml.replace(regex, rpl);
    delete regex;
  }
  return jhtml;
}

function jEditor_onSelect(ev)
{
}

function jEditor_onFocus(ev)
{
}

function jEditor_onBlur(ev)
{
  //this.currentSelectionStartPos=0;
  //this.currentSelectionEndPos=0;
}

function jEditor_onButtonClick(ev)
{
  this.computeSelection();
  ev = compatibleGetEvent(ev);
  var srcElement = compatibleEventGetSrcElement(ev);
  var actId = srcElement.getAttribute("actionid");

  /* Search the action: */
  for (var a=0; a < this.actions.length; a++)
  {
    var act = this.actions[a];
    if (act.actionID == actId)
    {
      /* found ! */
      this.applyActionOnSelectedText(act);
      return;
    }
  }
  alert("No action found linked with that button !");
}

function jEditor_subDump(node, rg)
{
  var txt="";
  var startPos=-1;
  var endPos=-1;
  var shiftBeforeStartPos = 0;
  var shiftBeforeEndPos = 0;
  for (var c=0; c < node.childNodes.length; c++)
  {
    var child = node.childNodes[c];
    if (child.nodeType==1)
    {
      /* Start TAG */
      var startTagDump = "<"+ncNodeTag(child)+">";
      if (startPos == -1)
      {
        shiftBeforeStartPos += startTagDump.length;
      }
      if (endPos == -1)
      {
        shiftBeforeEndPos += startTagDump.length;
      }
      txt += startTagDump;
      
      /* Sub content */
      var subArray = this.subDump(child, rg);
      if (subArray[1]!=-1)
      {
        startPos = subArray[1];
        //alert("shift="+shiftBeforeStartPos);
        startPos += shiftBeforeStartPos;
      }
      if (subArray[2]!=-1)
      {
        endPos = subArray[2];
        endPos += shiftBeforeEndPos;
      }
      
      if (startPos == -1)
      {
        shiftBeforeStartPos += subArray[0].length;
      }
      if (endPos == -1)
      {
        shiftBeforeEndPos += subArray[0].length;
      }
      txt += subArray[0];

      /* End TAG */
      if (ncNodeTag(child)!="BR")
      {
        var stopTagDump = "</"+ncNodeTag(child)+">";
        if (startPos == -1)
        {
          shiftBeforeStartPos += stopTagDump.length;
        }
        if (endPos == -1)
        {
          shiftBeforeEndPos += stopTagDump.length;
        }
        txt += stopTagDump;
      }
    }
    else
    {
      /* Certainly text. */
      var textNodeDump = ncNodeDump(child);
      txt += textNodeDump;
      if (child == rg.startContainer)
      {
        startPos = rg.startOffset;
        startPos += shiftBeforeStartPos;
      }
      else if (startPos == -1)
      {
        /* Je compte tous les objets que j'insere avant...*/
        shiftBeforeStartPos += textNodeDump.length;
      }
      
      if (child == rg.endContainer)
      {
        endPos = rg.endOffset;
        endPos += shiftBeforeEndPos;
      }
      else if (endPos == -1)
      {
        /* Je compte tous les objets que j'insere avant...*/
        shiftBeforeEndPos += textNodeDump.length;
      }
    }
  }
  
  return [txt, startPos, endPos];
}

function jEditor_computeSelection()
{
  /* First: 
  - Get all HTML code
  - Get start position of selection
  - Get stop position of selection */
  var rg = ncgetRange();
  this.HTMLSelInfo.innerHTML="";
  var resAr = this.subDump(this.HTMLEditZone, rg);
  
  var innerHTMLModified = this.HTMLEditZone.innerHTML.toLowerCase();
  var resArModified = resAr[0].toLowerCase();
  if (resArModified != innerHTMLModified)
  {
    alert("select text differs from inner HTML:\n" + resArModified+"\n"+innerHTMLModified);
  }
  this.HTMLSelInfo.innerHTML += resAr[1]+","+resAr[2]+"\n";
  this.HTMLSelInfo.innerHTML += this.HTMLEditZone.innerHTML;
  
  this.currentSelectionStartPos = resAr[1];
  this.currentSelectionEndPos = resAr[2];
  
  /* We have to detect for each button if the next action will be done or undone */
  for (var a=0; a < this.actions.length; a++)
  {
    var act = this.actions[a];
    if (act.IsEnabledAtPos(this.HTMLEditZone.innerHTML, this.currentSelectionStartPos))
    {
      if (act.buttonObject)
      {
        // we have a button : disable it
        if (act.buttonObject.value.indexOf('/')==-1) act.buttonObject.value = '/'+act.buttonObject.value;
      }
    }
    else
    {
      if (act.buttonObject)
      {
        // we have a button : enable it
        if (act.buttonObject.value.indexOf('/')!=-1) act.buttonObject.value = act.buttonObject.value.substring(1);
      }
    }
  }
}

function jEditor_applyActionOnSelectedText(act)
{
  var resultingText = act.Apply(this.HTMLEditZone.innerHTML, this.currentSelectionStartPos, this.currentSelectionEndPos);
  ncReplaceInnerHTML(this.HTMLEditZone, resultingText);
  this.computeSelection();
}
