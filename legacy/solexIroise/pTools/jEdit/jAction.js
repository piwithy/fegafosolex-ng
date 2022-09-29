function jAction()
{
  /* members */
  this.expression = null;
  this.actionID = 0;
  this.buttonObject = null;
  
  /* functions */
  this.parse = jAction_parse;
  this.IsEnabledAtPos = jAction_IsEnabledAtPos;
  this.Apply = jAction_Apply;
  this.removeActionOnThisText = jAction_removeActionOnThisText;
  this.removeUselessSequences = jAction_removeUselessSequences;
  this.roundSelectionLeft = jAction_roundSelectionLeft;
  this.roundSelectionRight = jAction_roundSelectionRight;
}

function jAction_parse(actionNode, htmlbuttonzone)
{
  var actId = actionNode.getAttribute("id");
  if (actId==null)
  {
    alert("jAction::parse Action ID is null");
    return 0;
  }
  
  this.actionID=actId;
  
  var currentExpression = null;
  for (var c=0; c < actionNode.childNodes.length; c++)
  {
    var child = actionNode.childNodes[c];
    if (ncNodeTag(child)==null)
      continue; /* not a node */
      
    if (ncNodeTag(child)=="button")
    {
      this.buttonObject = document.createElement("input");
      this.buttonObject.setAttribute("type", "button");
      if (child.getAttribute("value")&&child.getAttribute("value")!="") this.buttonObject.setAttribute("value", child.getAttribute("value"));
      if (child.getAttribute("className")&&child.getAttribute("className")!="") this.buttonObject.className = child.getAttribute("className");
      this.buttonObject.setAttribute("actionid", this.actionID);
      htmlbuttonzone.appendChild(this.buttonObject);
    }
    else if (ncNodeTag(child)=="expression")
    {
      currentExpression = new jExpression();
      currentExpression.parse(child);
    }
    else
    {
      alert("jAction::parse Unknown node '"+ncNodeTag(child)+"'");
      return;
    }
  }
  this.expression=currentExpression;
  
  return 1;
}

function jAction_IsEnabledAtPos(txt, startPos, useLarge)
{
  /* Get HTML expression before text */
  var startTk = this.expression.getStartToken();
  var endTk = this.expression.getEndToken();
  
  var startPos2 = (useLarge==1) ? this.roundSelectionLeft(txt, startPos) : this.roundSelectionRight(txt, startPos);
  
  var active = false;
  for (var i=0; i < Math.min(txt.length, startPos2); i++)
  {
    var startCompare = txt.substring(i, i+startTk.length);
    if (startCompare.toUpperCase() == startTk.toUpperCase())
    {
      active=true;
    }
    var stopCompare = txt.substring(i, i+endTk.length);
    if (stopCompare.toUpperCase() == endTk.toUpperCase())
    {
      active=false;
    }
  }
  return active;
}

function jAction_roundSelectionLeft(txt, leftpos)
{
  var insideTag=false;
  var recomp = leftpos;
  for (var i=leftpos-1; i>0; i--)
  {
    var car = txt.charAt(i);
    if (!insideTag)
    {
      if (car == ">")
      {
        insideTag=true;
      }
      else
        break;
    }
    if (insideTag)
    {
      if (car == "<")
      {
        insideTag=false;
      }
    }
    recomp=i;
  }
  return recomp;
}

function jAction_roundSelectionRight(txt, rightpos)
{
  var insideTag=false;
  var recomp = rightpos;
  for (var i=rightpos; i>0; i++)
  {
    recomp=i;
    var car = txt.charAt(i);
    if (!insideTag)
    {
      if (car == "<")
      {
        insideTag=true;
      }
      else
        break;
    }
    if (insideTag)
    {
      if (car == ">")
      {
        insideTag=false;
      }
    }
  }
  return recomp;
}

function jAction_removeActionOnThisText(txt, startTk, endTk)
{
  while (1)
  {
    var tmp = txt.toLowerCase();
    var startTagAtPos = tmp.indexOf(startTk.toLowerCase());
    if (startTagAtPos==-1)
      break;
    var endTagAtPos = tmp.indexOf(endTk.toLowerCase());
    if (endTagAtPos==-1)
      break;
    if (startTagAtPos < endTagAtPos)
    {
      var txtLeft = txt.substring(0, startTagAtPos);
      var txtRight = txt.substring(endTagAtPos+endTk.length);
      var txtMiddle = txt.substring(startTagAtPos+startTk.length, endTagAtPos);
      txt = txtLeft + txtMiddle + txtRight;
    }
    else
      break;
  }

  return txt;
}

function jAction_removeUselessSequences(txt, startTk, endTk)
{
  /* Removes consecutives tags <b></b> and </b><b> */
  var toFind1 = startTk.toLowerCase()+endTk.toLowerCase();
  var toFind2 = startTk.toLowerCase()+endTk.toLowerCase();
  while (1)
  {
    var tmp = txt.toLowerCase();
    var startAndEndTagAtPos1 = tmp.indexOf(toFind1);
    var startAndEndTagAtPos2 = tmp.indexOf(toFind2);
    
    var p = Math.max(startAndEndTagAtPos1,startAndEndTagAtPos2);
    if (p!=-1)
    {
      var txtLeft = txt.substring(0, p);
      var txtRight = txt.substring(p+toFind.length);
      txt = txtLeft + txtRight;
    }
    else
      break;
    
  }
  /* Removes overtagging  <b>Text<b>double bold</b> continuing</b>*/
  while (1)
  {
    var tmp = txt.toLowerCase();
    var startTagAtPos = tmp.indexOf(startTk.toLowerCase());
    var startTagAtPos2 = (startTagAtPos!=-1)?tmp.indexOf(startTk.toLowerCase(),startTagAtPos+1):-1;
    if (startTagAtPos2==-1)
      break;
      
    var endTagAtPos = tmp.indexOf(endTk.toLowerCase());
    if (endTagAtPos==-1)
      break;
    if (startTagAtPos2 < endTagAtPos)
    {
      var txtLeft = txt.substring(0, startTagAtPos2);
      var txtRight = txt.substring(endTagAtPos+endTk.length);
      var txtMiddle = txt.substring(startTagAtPos2+startTk.length, endTagAtPos);
      txt = txtLeft + txtMiddle + txtRight;
    }
    else
      break;
  }
  
  return txt;
}

function jAction_Apply(txt, startPos, endPos)
{
  var startTk = this.expression.getStartToken();
  var endTk = this.expression.getEndToken();
  var resultingText = "";
  
  if (startPos == endPos)
  {
    /* just add the tag and a dummy text */
    var startPos2 = this.roundSelectionLeft(txt, startPos);
    var txtLeft = txt.substring(0, startPos2);
    var txtRight = txt.substring(endPos2);
    resultingText = txtLeft + startTk + "texte" + endTk + txtRight;
    return resultingText;
  }
  
  var libAddTags = (!this.IsEnabledAtPos(txt, startPos, 0/*use most inside*/));
  var largePosEnabled = this.IsEnabledAtPos(txt, startPos, 1/*use most outside*/);
  
  /* Dans tous les cas, que j'ajoute ou que je retire des balises, le texte sélectionné n'a plus à contenir les balises
     Ex: Je met en gras du texte gras, j'enleve le gras d'un texte en gras */
  var startPos2 = this.roundSelectionLeft(txt, startPos);
  var endPos2 = this.roundSelectionRight(txt, endPos);
  var txtLeft = txt.substring(0, startPos2);
  var txtRight = txt.substring(endPos2);
  var txtMiddle = txt.substring(startPos2, endPos2);
  var txt2 = this.removeActionOnThisText(txtMiddle, startTk, endTk);
  var lenDiff = txtMiddle.length - txt2.length;
  endPos2 -= lenDiff;
  
  if (!libAddTags)
  {
    /* left part */
    var allText = txtLeft;
    /* close tag if an already open one has been previously detected */
    if (largePosEnabled)
    {
      allText += endTk;
    }
    /* center part */
    var tmp = txt2.toLowerCase();
    var posStartTk = tmp.indexOf(startTk.toLowerCase());
    var posEndTk = tmp.indexOf(endTk.toLowerCase());
    var txt3 = txt2;
    if (posEndTk!=-1)
    {
      /* on vire un tag de fin inutile desormais */
      txt3 = txt2.substring(0, posEndTk) + txt2.substring(posEndTk+endTk.length);
    }
    if (posStartTk!=-1)
    {
      /* on vire un tag de debut inutile desormais */
      txt3 = txt2.substring(0, posStartTk) + txt2.substring(posStartTk+startTk.length);
    }
    allText+=txt3;
    /* open tag if a previous was open in selected text */
    if ((posStartTk!=-1)||(largePosEnabled && (posEndTk==-1)))
    {
      allText+= startTk;
    }
    /* Right part */
    allText += txtRight;

    resultingText = this.removeUselessSequences(allText, startTk, endTk);
  }
  else
  {
    /* We must clean sequences of useless close/open tags */
    resultingText = this.removeUselessSequences(txtLeft + startTk + txt2 + endTk + txtRight, startTk, endTk);
  }
  return resultingText;
}