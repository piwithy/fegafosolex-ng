function trim(myString)
{
  return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
} 

function htmlspecialchars(chin) 
{
   var chout = chin;
   chout = chout.replace(/&/g,"&amp;");
   chout = chout.replace(/\"/g,"&quot;");
   chout = chout.replace(/\'/g,"&#039;");
   chout = chout.replace(/</g,"&lt;");
   chout = chout.replace(/>/g,"&gt;");
   
   return chout;
}

function unhtmlspecialchars(chin)
{
   var chout = chin;
   chout = chout.replace(/&amp;/g,"&");
   chout = chout.replace(/&quot;/g,"\"");
   chout = chout.replace(/&#039;/g,"\'");
   chout = chout.replace(/&lt;/g,"<");
   chout = chout.replace(/&gt;/g,">");
   
   return chout;
}

function isDigit(ch)
{
  return ((ch > 47)&&(ch < 58));
}

function escapeRegEx(chin)
{
   var chout = chin;
   chout = chout.replace(/\\/g,"\\\\");
   chout = chout.replace(/\./g,"\\.");
   chout = chout.replace(/\$/g,"\\$");
   chout = chout.replace(/\[/g,"\\[");
   chout = chout.replace(/\]/g,"\\]");
   chout = chout.replace(/\(/g,"\\(");
   chout = chout.replace(/\)/g,"\\)");
   chout = chout.replace(/\{/g,"\\{");
   chout = chout.replace(/\}/g,"\\}");
   chout = chout.replace(/\^/g,"\\^");
   chout = chout.replace(/\?/g,"\\?");
   chout = chout.replace(/\*/g,"\\*");
   chout = chout.replace(/\+/g,"\\+");
   chout = chout.replace(/\-/g,"\\-");
   return chout;
}

function getCookie(name)
{
	var cookiestring = document.cookie;
	//alert("doccookie is "+cookiestring);
	var cookies = cookiestring.split(';');
	for (var a=0; a < cookies.length; a++)
	{
		var cknv = trim(cookies[a]).split('=');
		if (cknv[0] == name)
		{
			//alert("found cookie "+name);
			return cknv[1];
		}
		//alert("not found cookie '"+name+"' vs '"+cknv[0]+"'");
	}
	return null;
}

function setCookie(name, val, exdays)
{
	//alert("attempt to set '"+name+"'='"+val+"'");
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);		
	var c_value=escape(val) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie = name + "=" + c_value; // apparement c'est un faux '=' ca gere la concatenation tout seul
	//alert("new document cookie is now "+document.cookie);
}

function isKnownDomain(mailDomain)
{
	/* <listV1> 03/06/2015 basée sur le GIEL du Rock'n Solex 2015 */
	if (mailDomain == "gmail.com")
		return true;
	if (mailDomain == "orange.fr")
		return true;
	if (mailDomain == "wanadoo.fr")
		return true;
	if (mailDomain == "hotmail.fr")
		return true;
	if (mailDomain == "icloud.fr")
		return true;
	if (mailDomain == "laposte.net")
		return true;
	if (mailDomain == "yahoo.fr")
		return true;
	if (mailDomain == "gmx.fr")
		return true;
	if (mailDomain == "insa-rennes.fr")
		return true;
	if (mailDomain == "voila.fr")
		return true;
	if (mailDomain == "live.fr")
		return true;
	if (mailDomain == "univ-rennes1.fr")
		return true;
	if (mailDomain == "neuf.fr")
		return true;
	if (mailDomain == "sfr.fr") //ajout perso
		return true;
	if (mailDomain == "le-roi.fr")
		return true;
	if (mailDomain == "insa-rouen.fr")
		return true;
	if (mailDomain == "club-internet.fr")
		return true;
	if (mailDomain == "free.fr")
		return true;
	if (mailDomain == "nordnet.fr")
		return true;
	if (mailDomain == "dbmail.com")
		return true;
	if (mailDomain == "cg35.fr")
		return true;
	if (mailDomain == "facebook.com")
		return true;
	if (mailDomain == "infonie.fr")
		return true;
	if (mailDomain == "hotmail.com")
		return true;
	if (mailDomain == "live.com")
		return true;
	if (mailDomain == "cegetel.net")
		return true;
	if (mailDomain == "aliceadsl.fr")
		return true;
	if (mailDomain == "enib.fr")
		return true;
	if (mailDomain == "xelium.fr")
		return true;
	if (mailDomain == "ville-cesson-sevigne.fr")
		return true;
	if (mailDomain == "msn.com")
		return true;
	if (mailDomain == "antoineleroux.fr")
		return true;
	if (mailDomain == "franciaflex.com")
		return true;
	if (mailDomain == "viparis.com")
		return true;
	if (mailDomain == "pearce.fr")
		return true;
	// mes domaines a moi:
	if (mailDomain == "pionet.fr")
		return true;
	if (mailDomain == "breizhsolex.com")
		return true;
	if (mailDomain == "breizhmob.com")
		return true;
	if (mailDomain == "fegaf.fr")
		return true;
	if (mailDomain == "rocknsolex.fr")
		return true;
	/* </listV1> */
	/* <listV2> 03/06/2015 breizhsolex.com */
	if (mailDomain == "cheerful.com")
		return true;
	if (mailDomain == "aol.com")
		return true;
	if (mailDomain == "saint-gobain.com")
		return true;
	if (mailDomain == "location-santec.fr")
		return true;
	if (mailDomain == "bslsecurite.fr")
		return true;
	if (mailDomain == "rocketmail.com")
		return true;
	if (mailDomain == "ensic.inpl-nancy.fr")
		return true;
	if (mailDomain == "bedeco.fr")
		return true;
	if (mailDomain == "fbxi.com")
		return true;
	if (mailDomain == "rage.fr")
		return true;
	if (mailDomain == "email.it")
		return true;
	if (mailDomain == "solexverhuurtexel.nl")
		return true;
	if (mailDomain == "hcme.com")
		return true;
	if (mailDomain == "bbox.fr")
		return true;
	if (mailDomain == "noos.fr")
		return true;
	if (mailDomain == "web.de")
		return true;
	if (mailDomain == "outlook.com")
		return true;
	if (mailDomain == "tipmat.com")
		return true;
	/* </listV2> */
	return false;
}
