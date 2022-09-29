function HTMLTable(cellCallback)
{
  this.mTABLE = null;
  this.mTHEAD = null;
  this.mTBODY = null;
  this.mCurrentTR = null;
  this.cellCallback = cellCallback;
  
  /* features */
  this.createInto = HTMLTable_createInto;
  this.attachTo = HTMLTable_attachTo;
  this.addHead = HTMLTable_addHead;
  this.addBody = HTMLTable_addBody;
  this.addRow = HTMLTable_addRow;
  this.addCell = HTMLTable_addCell;
  this.addRowsFromXML = HTMLTable_addRowsFromXML;
  this.addRowFromXML = HTMLTable_addRowFromXML;
  this.privateCellCallback = HTMLTable_privateCellCallback;
}

function HTMLTable_createInto(HTMLObj, tableClassName)
{
  var tbl = document.createElement("table");
  if (tableClassName != null)
    tbl.className = tableClassName;
  HTMLObj.appendChild(tbl);
  return this.attachTo(tbl);
}

function HTMLTable_attachTo(HTMLObj)
{
  this.mTABLE = HTMLObj;
  return this.mTABLE;
}

function HTMLTable_addHead(headClassName)
{
  if (this.mTABLE == null)
    alert("TABLE not attached while adding HEAD");
  if (this.mTHEAD)
    alert("HEAD already attached");
  var thead = document.createElement("thead");
  this.mTABLE.appendChild(thead);
  if (headClassName != null)
    thead.className = headClassName;
  this.mTHEAD = thead;
  return this.mTHEAD;
}

function HTMLTable_addBody(bodyClassName)
{
  if (this.mTABLE == null)
    alert("TABLE not attached while adding BODY");
  if (this.mTBODY)
    alert("BODY already attached");
  var tbody = document.createElement("tbody");
  this.mTABLE.appendChild(tbody);
  if (bodyClassName != null)
    tbody.className = bodyClassName;
  this.mTBODY = tbody;
  return this.mTBODY;
}

/* inhead: boolean */
function HTMLTable_addRow(inHead, rowClassName)
{
  var container = null;
  if ((inHead != null) && (inHead))
  {
    /* create head if none */
    if (this.mTHEAD==null)
      this.addHead();
    container=this.mTHEAD;
  }
  else
  {
    /* create body if none */
    if (this.mTBODY==null)
      this.addBody();
    container=this.mTBODY;
  }
  var lineFeed = null;
  lineFeed = document.createTextNode("\t");
  container.appendChild(lineFeed);
  var tr = document.createElement("tr");
  container.appendChild(tr);
  lineFeed = document.createTextNode("\n");
  container.appendChild(lineFeed);
  if (rowClassName != null)
    tr.className = rowClassName;
  this.mCurrentTR = tr;
  return this.mCurrentTR;
}

function HTMLTable_addCell(content, cellClassName, advancedStyle)
{
  if (this.mCurrentTR==null)
    alert("Missing ROW before addCell");
  var tagName="td";
  if (this.mCurrentTR.parentNode == this.mTHEAD)
    tagName="th";

  var lineFeed = null;
  lineFeed = document.createTextNode("\t\t");
  this.mCurrentTR.appendChild(lineFeed);
  var cell = document.createElement(tagName);
  this.mCurrentTR.appendChild(cell);
  lineFeed = document.createTextNode("\n");
  this.mCurrentTR.appendChild(lineFeed);
  
  if (content != null)
    cell.innerHTML = content;
  if (cellClassName != null)
    cell.className = cellClassName;
  if (advancedStyle != null)
  {
    /* 2 calls for browser compat */
    cell.setAttribute("style", advancedStyle);
    cell.style.cssText = advancedStyle;
  }
    
  if (this.cellCallback)
    this.cellCallback(this, cell, "create", null);
  
  var obj = this;
  compatibleAttachEvent(cell, "mouseover", function (evt) { obj.privateCellCallback("mouseover", evt); });
  compatibleAttachEvent(cell, "mouseout", function (evt) { obj.privateCellCallback("mouseout", evt); });
  compatibleAttachEvent(cell, "click", function (evt) { obj.privateCellCallback( "click", evt); });
  return cell;
}

function HTMLTable_privateCellCallback(evtType, evt)
{
  evt = compatibleGetEvent(evt);
  if (this.cellCallback)
  {
    /* Get the CELL */
    var obj = compatibleEventGetSrcElement(evt);
    while (obj && (ncNodeTag(obj)!="TD") && (ncNodeTag(obj)!="TH"))
      obj= obj.parentNode;

    this.cellCallback(this, obj, evtType, evt);
  }
}

function HTMLTable_addRowsFromXML(XMLRootNode)
{
  if ((XMLRootNode==null) || (XMLRootNode.childNodes==null))
    return;
  for (var a=0; a < XMLRootNode.childNodes.length; a++)
  {
    var subNode = XMLRootNode.childNodes[a];
    if (ncNodeTag(subNode) == "ROW")
    {
      this.addRowFromXML(subNode);
    }
  }
}

function HTMLTable_addRowFromXML(XMLRowNode)
{
  if ((XMLRowNode==null) || (XMLRowNode.childNodes==null))
    return;
  if (ncNodeTag(XMLRowNode) != "ROW")
    return;
  this.addRow();
  for (var a=0; a < XMLRowNode.childNodes.length; a++)
  {
    var subNode = XMLRowNode.childNodes[a];
    var nt = ncNodeTag(subNode);
    if (nt == "CELL")
    {
      this.addCell(unhtmlspecialchars(subNode.innerHTML));
    }
  }
}