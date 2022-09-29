<?php
require("../commonSkinInclude.php");
pTools::initialize("../pTools/");
CHTML::prolog_XHTML("utf-8");
echo "<head>\n";
pTools::getpTools()->loadJavascript();
CHTML::head_stylesheet_default();
CHTML::head_stylesheet("commonStyle.css");
CHTML::head_stylesheet("icons.css");
CHTML::head_stylesheet("viewer.css");
echo "</head>\n";
?>
<Title>Les courses de Solex en Direct ! </Title>
<?php
echo "<body>\n";
echo "<table style='width: 100%;' class='bannerTable'><tbody><tr>";
echo "\t<td style='width: 55px;'><img width='50' src='logo.png'/></td>";
echo "\t<td style='vertical-align: middle;'><a href='http://solexiroise.fr'>SOLEXIROISE.FR</a></td>";
echo "\t<td style='width: 55px;'><img width='50' src='http://www.fegaf.fr/files/images/logo_fegaf_reduit.png'/></td>";
echo "\t<td style='vertical-align: middle;'><a href='http://www.fegaf.fr'>WWW.FEGAF.FR</a></td>";

$displayType = "full";
if (isset($_GET['displayType']))
	$displayType = $_GET['displayType'];

if (isset($_GET['race']))
{
	echo "<td style='text-align:right'>Affichage: <select id='dtype' onChange='changeDisplayType();'>";
	echo "<option value=''></option>";
	echo "<option value='full'";
	if ($displayType=="full") echo " selected";
	echo ">Intégral</option>";
	echo "<option value='paged'";
	if ($displayType=="paged") echo " selected";
	echo ">Paginé</option></select></td>";
}
echo "</tr></tbody></table>";

if (isset($_GET['race']))
{
	?>
	<script type="text/javascript">
	function changeDisplayType()
	{
		var val = getHTMLElementValue(document.getElementById('dtype'));
		var liNewLoc = "index.php?race=<?php echo $_GET['race'] ;?>&displayType="+val;
		<?php if (isset($_GET['url'])) echo "liNewLoc += \"&url=\"+encodeURIComponent(\"".$_GET['url']."\");\n"; ?>
		document.location=liNewLoc;
	}
	</script>
	<?php
	echo "<h1><span id='title'></span></h1>";
}
else
{
	common_raceSelectorHTML();
}
?>
<div id="problemDiv" style="display: none;">
	<center>
	<br/>
	<h2>Nous sommes désolés</h2>
	La connexion réseau vers l'ordinateur de stockage des données semble momentanément interrompue.<br/>
	<br/>
	Veuillez patienter jusqu'au rétablissement de la connexion ...<br/>
	</center>
</div>
<div id='dispDiv'></div>
<script type="text/javascript">
var DISPTYPE = "<?php echo $displayType; ?>"; // paged OR full
var header_height = 140;
var line_height = 35;
function evaluateNumPerPage()
{
	var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
	var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;
	var sizeLeft = y-header_height;
	return Math.floor(sizeLeft/line_height);
}

/*var tmptest = document.createElement("div");
document.body.appendChild(tmptest);
tmptest.style.position="absolute";
tmptest.style.top = header_height+"px";
tmptest.style.height = line_height+"px";
tmptest.style.width = "200px";
tmptest.style.border = "1px solid red";
tmptest.innerHTML = "xsqxsqxsqxsqxsqx";*/

var xmldata=null;
var totalPageCount = 1;
var numPerPage = 20;
var paged_curPage = 0;
function refreshDatas()
{
	xmldata = null;
	var nP = new ncNotDOMGetter(false);
	var liXml = nP.getFile("<?php echo $remoteScript; ?>?get=<?php if (isset($_GET['race'])) echo $_GET['race']; ?>", false, null/*cbk*/, null/*var*/, null/*hdr*/);
	if (liXml==null)
	{
	}
	else if (liXml.indexOf('<?xml')==-1)
	{
		alert(liXml);
	}
	else
	{
		delete nP;
		nP=null;
		nP = new ncDOMParser();
		xmldata = nP.parseBuffer(liXml);
	}

	var dv = document.getElementById('dispDiv');
	
	if (xmldata)
	{
		document.getElementById('problemDiv').style.display="none";
		ncReplaceInnerHTML(dv, "");
		/* go!!! */
		var fsXmlData = xmldata.documentElement;
		var liTitle = fsXmlData.getAttribute("plateau") + "-" + fsXmlData.getAttribute("race");
		var liEventName = fsXmlData.getAttribute("eventName");
		if (liEventName!="")
			liTitle = liEventName + " : " + liTitle;
		setHTMLElementValue(document.getElementById('title'), liTitle);
		document.title = liTitle;
		
		var ttl =  document.createElement("div");
		dv.appendChild(ttl);
		ttl.innerHTML = "Classement du "+fsXmlData.getAttribute("timegen")+"<div id='moreTitle' style='display: inline;'></div><br/><br/>";
		
		var resultNodes = compatibleSelectNodes(fsXmlData, ".//result");
		
		if (DISPTYPE == "paged")
		{
			numPerPage = evaluateNumPerPage();
			totalPageCount = Math.ceil(resultNodes.length / numPerPage);
			//alert("perpage="+numPerPage+" , so "+totalPageCount+" pages");
			paged_curPage=0;
			document.getElementById('moreTitle').innerHTML = " - Page 1 / "+totalPageCount;
		}
		else
		{
			numPerPage = resultNodes.length;
		}

		var r=0;
		for (var nPage=0; nPage < totalPageCount; nPage++)
		{
			var tbl = document.createElement("table");
			dv.appendChild(tbl);
			tbl.setAttribute("id", "tbl"+nPage);
			if (nPage >= 1)
				tbl.style.display="none";
			tbl.className = "moderntable";
			var hdr = "<tr><th>Tend</th><th>Rang</th><th>#</th><th>Equipe</th><th>Tours</th>";
			hdr += "<th>Meilleurs Temps</th><th>Dernier Temps</th>";
			hdr += "<th>Ecart Précédent</th><th>Ecart premier</th></tr>";
			tbl.innerHTML = hdr;
			for (var rec=0; rec < numPerPage; rec++)
			{
				var ch = resultNodes[r];

				createResultLine(tbl, ch);				

				r++;
				if (r >= resultNodes.length)
					break;
			}
			if (r >= resultNodes.length)
				break;
		}
		
		if (DISPTYPE == "full")
			setTimeout(refreshDatas, 4000);
		else if (DISPTYPE == "paged")
			setTimeout(nextPagePlease, 20000);
	}
	else
	{
		/*perte de connexion*/
		document.getElementById('problemDiv').style.display="";
		ncReplaceInnerHTML(dv, "");//sinon reste bloque sur la derniere page... celle ou ceux qui ne roulent pas... bof...
		setTimeout(refreshDatas, 4000);
	}
}

function nextPagePlease()
{
	paged_curPage++;
	if (paged_curPage >= totalPageCount)
		refreshDatas();
	else
	{
		var liOldId = 'tbl'+(paged_curPage-1);
		document.getElementById(liOldId).style.display="none";
		document.getElementById('tbl'+paged_curPage).style.display="";
		document.getElementById('moreTitle').innerHTML = " - Page "+(paged_curPage+1)+" / "+totalPageCount;
		setTimeout(nextPagePlease, 20000);
	}
}
</script>
<?php common_scriptFunctions(); ?>

<?php if (isset($_GET['race'])) { ?>
	<script type="text/javascript">
	refreshDatas();
	</script>
<?php } ?>
</body>
</html>
