function TableEditor(tableObject, numColumns/*max, detected par la suite*/, cellChangeCallback, onRowCreateCallback, onCellFocus)
{
	this.tableObject = tableObject;
	this.numColumns = numColumns;
	this.detectedColumnsOnStart = 0;
	this.createEditorLine = TableEditor_createEditorLine;
	this.removeEditorLine = TableEditor_removeEditorLine;
	this.removeTR = TableEditor_removeTR;
	this.removeCOL = TableEditor_removeCOL;
	this.removeCOLFromTD = TableEditor_removeCOLFromTD;
	this.onchangeEditorCell = TableEditor_onChangeEditorCell;
	this.cellChangeCallback = cellChangeCallback;
	this.onRowCreateCallback = onRowCreateCallback;
	this.onCellFocus = onCellFocus;
	this.transformCell = TableEditor_transformCell;
	this.untransformCell = TableEditor_untransformCell;
	this.createInputInTd = TableEditor_createInputInTd;
	this.destroyInputFromTd = TableEditor_destroyInputFromTd;
	this.setCellType = TableEditor_setCellType;
	this.start = TableEditor_start;
	this.stop = TableEditor_stop;
	this.autoRedimAll = TableEditor_autoRedimAll;

	//private	
	this.privateDetachINPUTEvents = TableEditor_privateDetachINPUTEvents;
	
	this.cellTypes = new Array();
	for (var c=0; c < numColumns; c++)
	{
		this.cellTypes[c] = "text:";
	}
}

function TableEditor_start()
{
	// On transforme le tableau qui etait 'normal' en tableau 'edite'
	var TRs = this.tableObject.getElementsByTagName("TR");
	for (var r=0; r < TRs.length; r++)
	{
		var theRow = TRs[r];
		var TDs = theRow.getElementsByTagName("TD");
		var THs = theRow.getElementsByTagName("TH");
		if (TDs.length) /* sinon j'ai sans doutes des TR, ligne de titre ignoree */
		{
		  for (var c=0; c < this.numColumns; c++)
		  {
				if (TDs[c]==null)
					break;
			  this.transformCell(c, TDs[c]);
		  }
			this.detectedColumnsOnStart=c;
	  }
		else if (THs.length && (this.detectedColumnsOnStart==0))
		{
		  for (var c=0; c < this.numColumns; c++)
		  {
				if (THs[c]==null)
					break;
		  }
			this.detectedColumnsOnStart=c;
		}
		if (TDs.length && this.onRowCreateCallback)
			this.onRowCreateCallback(theRow);
	}

	// And create initial editor line
	this.createEditorLine();
	this.autoRedimAll();
}

function TableEditor_stop()
{
	// remove editor line
	this.removeEditorLine();
	// Et on retransforme le tableau en normal...
	var TRs = this.tableObject.getElementsByTagName("TR");
	for (var r=0; r < TRs.length; r++)
	{
		var theRow = TRs[r];
		var TDs = theRow.getElementsByTagName("TD");
		for (var c=0; c < TDs.length; c++)
		{
			this.untransformCell(c, TDs[c]);
		}	
	}	
}

function TableEditor_setCellType(cellIndexInRow, cellTypeString)
{
	this.cellTypes[cellIndexInRow] = cellTypeString;
}

function TableEditor_transformCell(cellIndexInRow, cellObject)
{
	/* from <td>content</td> TO <td><input type="text".../></td> */
	var oldContent = cellObject.innerHTML;
	cellObject.innerHTML = "";
	this.createInputInTd(cellIndexInRow, cellObject, oldContent);
}

function TableEditor_untransformCell(cellIndexInRow, cellObject)
{
	/* from <td><input type="text".../></td> TO <td>content</td>*/
	this.destroyInputFromTd(cellIndexInRow, cellObject);
}

function TableEditor_destroyInputFromTd(cellIndexInRow, cellObject)
{
	var cellType = this.cellTypes[cellIndexInRow];
	if (cellType == "none")
	{
		/* j'fais rien */
	}
	else if (cellType.indexOf("text:")!=-1)
	{
		var tmpINPUTs = cellObject.getElementsByTagName("INPUT");
		var tmpINPUTVal = getHTMLElementValue(tmpINPUTs[0]);
		cellObject.innerHTML = tmpINPUTVal;
	}	
	else if (cellType.indexOf("textarea:")!=-1)
	{
		var tmpINPUTs = cellObject.getElementsByTagName("TEXTAREA");
		var tmpINPUTVal = getHTMLElementValue(tmpINPUTs[0]);
		cellObject.innerHTML = tmpINPUTVal;
	}	
	else if (cellType.indexOf("select:")!=-1)
	{
		var tmpINPUTs = cellObject.getElementsByTagName("SELECT");
		var tmpINPUTVal = getHTMLElementValue(tmpINPUTs[0]);
		cellObject.innerHTML = tmpINPUTVal;
	}
}

function TableEditor_createInputInTd(cellIndexInRow, cellObject, initialContent)
{
	var cellType = this.cellTypes[cellIndexInRow];
	if (cellType == "none")
	{
		cellObject.innerHTML = initialContent;
	}
	else if (cellType.indexOf("text:")!=-1)
	{
		// Syntaxe: text:DEFAULTVAL
		var defVal = cellType.substring(5);
		var tmpINPUT = document.createElement("input");
		cellObject.appendChild(tmpINPUT);
		tmpINPUT.setAttribute("type", "text");
		var obj = this;
		compatibleAttachEvent(tmpINPUT, "change", TableEditor_onChangeEditorCell);
		if (this.cellChangeCallback)
			compatibleAttachEvent(tmpINPUT, "change", this.cellChangeCallback);
		if (this.onCellFocus)
			compatibleAttachEvent(tmpINPUT, "focus", this.onCellFocus);
		tmpINPUT.tableEditor = this;
		if (initialContent!="")
			setHTMLElementValue(tmpINPUT, initialContent, 0);
		else
			setHTMLElementValue(tmpINPUT, defVal, 0);
	}
	else if (cellType.indexOf("textarea:")!=-1)
	{
		var defVal = cellType.substring(9);
		var tmpINPUT = document.createElement("textarea");
		cellObject.appendChild(tmpINPUT);
		tmpINPUT.setAttribute("rows", "5");
		tmpINPUT.setAttribute("cols", "40");
		var obj = this;
		compatibleAttachEvent(tmpINPUT, "change", TableEditor_onChangeEditorCell);
		if (this.cellChangeCallback)
			compatibleAttachEvent(tmpINPUT, "change", this.cellChangeCallback);
		if (this.onCellFocus)
			compatibleAttachEvent(tmpINPUT, "focus", this.onCellFocus);
		tmpINPUT.tableEditor = this;
		
		/* reverses CHTML::toHTML(xxx, "html") */
		initialContent = initialContent.replace(/\<br\/\>/g, "");
		initialContent = initialContent.replace(/\<br\>/g, "");
		initialContent = initialContent.replace(/\r/g, "");
		initialContent = initialContent.replace(/\n\n/g, "\n"); /* j'ai pas compris pourquoi je me retrouve avec DEUX sauts a la ligne (chrome) */

		if (initialContent!="")
			tmpINPUT.innerHTML=initialContent;
		else
			tmpINPUT.innerHTML=defVal;
	}
	else if (cellType.indexOf("select:")!=-1)
	{
		// Syntaxe: select:DEFAULTVAL;option_value,option_text;option_value,option_text;...
		var allOpts = cellType.substring(7).split(';');
		var tmpINPUT = document.createElement("select");
		cellObject.appendChild(tmpINPUT);
		var defVal = allOpts[0];
		for (var o=1; o < allOpts.length; o++)
		{
			var onv = trim(allOpts[o]).split(',');
			if (onv[0]!="")
			{
				compatibleAddOption(tmpINPUT, onv[1], onv[0]);
			}
		}
		// NON: cf call de removeEditorLine uniquement base sur les INPUTs et pas SELECTs   compatibleAddOption(tmpINPUT, "", ""); // needed to empty the line
		if (initialContent!="")
			setHTMLElementValue(tmpINPUT, initialContent, 0);
		else
		{
			setHTMLElementValue(tmpINPUT, defVal, 0);
		}
	}
}

function TableEditor_createEditorLine()
{
	var TBODYs = this.tableObject.getElementsByTagName("TBODY");
	var TBODY = TBODYs[0];
	var tmpTR = document.createElement("tr");
	TBODY.appendChild(tmpTR);
	for (var a=0; a < this.detectedColumnsOnStart; a++)
	{
		var tmpTD = document.createElement("td");
		tmpTR.appendChild(tmpTD);
		this.createInputInTd(a, tmpTD, "");
	}
	if (this.onRowCreateCallback)
		this.onRowCreateCallback(tmpTR);
}

function TableEditor_removeEditorLine()
{
	var TRs = this.tableObject.getElementsByTagName("TR");
	var lastTR = TRs[TRs.length-1];
	this.removeTR(lastTR);
}

function TableEditor_removeTR(theTR)
{
	var INPUTs = theTR.getElementsByTagName("INPUT");
	for (var a=0; a < INPUTs.length; a++)
	{
		var tmpINPUT = INPUTs[a];
		this.privateDetachINPUTEvents(tmpINPUT);
	}
	theTR.parentNode.removeChild(theTR);
}

function TableEditor_privateDetachINPUTEvents(theInput)
{
	compatibleDetachEvent(theInput, "change", TableEditor_onChangeEditorCell);
	if (this.cellChangeCallback)
		compatibleDetachEvent(theInput, "change", this.cellChangeCallback);
	if (this.onCellFocus)
		compatibleDetachEvent(theInput, "focus", this.onCellFocus);
	theInput.tableEditor = null;
}

function TableEditor_removeCOLFromTD(TDObj)
{
	var tr = TDObj.parentNode;
	var TDs = tr.getElementsByTagName(ncNodeTag(TDObj)); // compatible TD/TH
	for (c=0;c<TDs.length;c++)
	{
		if (TDs[c] == TDObj)
		{
			this.removeCOL(c);
			break;
		}
	}
}

function TableEditor_removeCOL(colIndex0)
{
	var TRs = this.tableObject.getElementsByTagName("TR");
	for (var r=0; r < TRs.length; r++)
	{
		var TDs = TRs[r].getElementsByTagName("TD");
		var c;
		for (c=0;c<TDs.length;c++)
		{
			if (c==colIndex0)
			{
				var INPUTs = TDs[c].getElementsByTagName("INPUT");
				for (var i=0; i<INPUTs.length; i++)
					this.privateDetachINPUTEvents(INPUTs[i]);
				TDs[c].parentNode.removeChild(TDs[c]);
				break;
			}
		}
		var THs = TRs[r].getElementsByTagName("TH");
		for (c=0;c<THs.length;c++)
		{
			if (c==colIndex0)
			{
				var INPUTs = THs[c].getElementsByTagName("INPUT");
				for (var i=0; i<INPUTs.length; i++)
					this.privateDetachINPUTEvents(INPUTs[i]);
				THs[c].parentNode.removeChild(THs[c]);
				break;
			}
		}
	}
}

function TableEditor_autoRedimAll()
{
	/* auto redim: */
	var TRs = this.tableObject.getElementsByTagName("TR");
	var maxSz = new Array();
	for (var r=0;r<TRs.length;r++)
	{
		var TDs = TRs[r].getElementsByTagName("TD");
		for (var c=0;c<TDs.length;c++)
		{
			if (maxSz[c]==null)
				maxSz[c]=0;
			var inps = TDs[c].getElementsByTagName("INPUT");
			if (inps.length>1)
				alert("more inps than 1???");
			for (var i=0;i<inps.length; i++)
			{
				var sz = getHTMLElementValue(inps[i]);
				if (sz.length > maxSz[c])
					maxSz[c]=sz.length;
			}
		}
	}
	for (var c=0; c<maxSz.length;c++)
	{
		for (var r=0;r<TRs.length;r++)
		{
			var TDs = TRs[r].getElementsByTagName("TD");
			for (var c2=0;c2<TDs.length;c2++)
			{
				var inps = TDs[c].getElementsByTagName("INPUT");
				if (inps.length>0)
					inps[0].setAttribute("size", maxSz[c]);
			}
		}		
	}
}

function TableEditor_onChangeEditorCell(evt)
{
	// Une cellule a change...
	var evt = compatibleGetEvent(evt);
	var srcElement = compatibleEventGetSrcElement(evt);
	
	var obj = srcElement.tableEditor;
	
	// Si une des cellules de la derniere ligne est occupee, on en cree une autre
	var TRs = obj.tableObject.getElementsByTagName("TR");
	var lastTR = TRs[TRs.length-1];
	var INPUTs = lastTR.getElementsByTagName("INPUT"); /* ne voit pas les "SELECT" : c'est parfait ! */
	var libOneCellOfLastTRHasData = false;
	for (var a=0; a < INPUTs.length; a++)
	{
		if (INPUTs[a].value != "")
		{
			libOneCellOfLastTRHasData = true;
			break;
		}
	}
	if (libOneCellOfLastTRHasData)
	{
		obj.createEditorLine();
	}
	
	// Si toutes les cellules des DEUX dernieres lignes sont vides, on vire une ligne d'edition
	// (bah oui, je dois toujours garder une ligne vide)
	while (1)
	{
		var beforeLastTR = (TRs.length>=2) ? TRs[TRs.length-2] : null;
		if (beforeLastTR)
		{
			var INPUTS2 = beforeLastTR.getElementsByTagName("INPUT"); /* ne voit pas les "SELECT" : c'est parfait ! */
			var libOneCellHasData2 = false;
			for (var a=0; a < INPUTS2.length; a++)
			{
				if (INPUTS2[a].value != "")
				{
					libOneCellHasData2 = true;
					break;
				}
			}
			if (!libOneCellOfLastTRHasData && !libOneCellHasData2)
			{
				obj.removeEditorLine();
				continue; /* ca marche, car TRs est apparemment mis a jour. cool. valable IE et Chrome */
			}
		}
		break;
	}
	
	obj.autoRedimAll();
}