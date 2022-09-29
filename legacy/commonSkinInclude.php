<?php
/* Initialisation des constantes et outils ... */
$url = "http://live.solexiroise.fr/";
if (isset($_GET['url']))
	$url = $_GET['url'];
$remoteScript = $url . "fegafosolexRequest.php";
require("pTools/main.php");

function common_raceSelectorHTML()
{
	global $remoteScript;
	echo "<center><br/><h2>Sélectionner une course</h2><br/><select id='raceSelector'></select>&nbsp; ";
	echo "<input type='button' onClick=\"navRace();\" value='OK'/>";
	echo "</center>";

	/* ask JS to fill raceSelector */
	?>
	<script type="text/javascript">
	function navRace()
	{
		var theSel = document.getElementById('raceSelector');
		var theRace = getHTMLElementValue(theSel);
		document.location='?race='+theRace;
	}
	
	function asynchronouslyFill()
	{
		var nP = new ncNotDOMGetter(false);
		// 1. on voit s'il y a une course recente:
		var activeRace = nP.getFile("<?php echo $remoteScript; ?>?ls_lastmod", false, null/*cbk*/, null/*var*/, null/*hdr*/);
		if (activeRace.indexOf('<')!=-1) activeRace="";
		// 2. On obtient la liste des courses
		var html = nP.getFile("<?php echo $remoteScript; ?>?ls", false, null/*cbk*/, null/*var*/, null/*hdr*/);
		var files = html.split('\n');
		var theSel = document.getElementById('raceSelector');
		var a;
		compatibleAddOption(theSel, "(Course Active)", "lastmod");
		for (a=0; a < files.length; a++)
		{
			var el = files[a].split('|');
			if ((el[1]!="DIR") && (el[0].indexOf('.xml')!=-1))
			{
				var splitExt = el[0].split('.xml');
				if (el[0]==activeRace) splitExt[0] += " (active)";
				compatibleAddOption(theSel, splitExt[0], el[0]);
			}
		}
	}
	// pas envie de faire de requete asynchrone ca fait un code plus gros. 
	// mais chrome me mets un warning alors je tente de respecter:
	setTimeout(asynchronouslyFill,10);
	</script>
	<?php
}

function common_scriptFunctions()
{
	?><script type="text/javascript">
	function createResultLine(tbl, result)
	{
		var theTr = document.createElement("tr");
		tbl.appendChild(theTr);
		
		var theTd;
		theTd = document.createElement("td");
		theTd.className = "td_tendance";
		theTr.appendChild(theTd);
		if (result.getAttribute("tendance")==0)
			theTd.innerHTML = "<img src='stable.png' />";
		else if (result.getAttribute("tendance")==1)
			theTd.innerHTML = "<img src='down.png' />";
		else if (result.getAttribute("tendance")==-1) /* je perd un rang donc je suis meilleurs donc UP dans la liste */
			theTd.innerHTML = "<img src='up.png' />";

		theTd = document.createElement("td");
		theTd.className = "td_rank";
		theTr.appendChild(theTd);
		theTd.innerHTML = result.getAttribute("rang");

		theTd = document.createElement("td");
		theTr.appendChild(theTd);
		theTd.innerHTML = result.getAttribute("teamNumber");

		var cat = result.getAttribute("teamCategory");
		if (cat == "Origine")
			theTd.className = "cat_origine";
		else if ((cat == "Prototype")||(cat == "Proto"))
			theTd.className = "cat_prototype";
		else if ((cat == "Promotion")||(cat == "Promo"))
			theTd.className = "cat_promotion";
		else if ((cat == "Origine Amélioré")||(cat == "OA")||(cat == "Origine Ameliore"))
			theTd.className = "cat_oa";
		else if ((cat == "Electrique")||(cat == "Elec"))
			theTd.className = "cat_electrique";
		else if ((cat == "Super Prototype")||(cat == "SP"))
			theTd.className = "cat_sp";
		else
			theTd.className = "cat_unknown";
		
		theTd = document.createElement("td");
		theTd.className = "td_teamName";
		theTr.appendChild(theTd);
		theTd.innerHTML = result.getAttribute("teamName");
		
		theTd = document.createElement("td");
		theTd.className = "td_lap";
		theTr.appendChild(theTd);
		theTd.innerHTML = result.getAttribute("tours");
		
		theTd = document.createElement("td");
		theTd.className = "td_bestTime";
		theTr.appendChild(theTd);
		theTd.innerHTML = result.getAttribute("bestTime");
		var bestTimeLap = parseInt(result.getAttribute("bestTimeLap"),10);
		if (bestTimeLap != 0)
			theTd.innerHTML += " &nbsp;("+bestTimeLap+")";

		theTd = document.createElement("td");
		theTd.className = "td_lastTime";
		theTr.appendChild(theTd);
		theTd.innerHTML = result.getAttribute("lastTime");
		
		theTd = document.createElement("td");
		theTd.className = "td_ecartPrev";
		theTr.appendChild(theTd);
		theTd.innerHTML = result.getAttribute("ecartPrev");
		
		theTd = document.createElement("td");
		theTd.className = "td_ecartFirst";
		theTr.appendChild(theTd);
		theTd.innerHTML = result.getAttribute("ecartFirst");
	}
	</script>
	<?php
}
?>