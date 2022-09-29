<?php
header("Access-Control-Allow-Origin: *");
/* Pour la mise en resultat automatique de FeGaFoSolex ... 
 En gros on donne acces a un repertoire ou on push des fichiers html ... */
$OUTDIR = "./data/";
/* A savoir : d'autres parametres non utilisés ici sont transmis depuis Fegafosolex 1.20
	'eventname' : nom de votre évènement  ("Festival du bout du monde")
	'login' : login eventuel pour autoriser le depot et l'acces aux fichiers
	'password : password associé au login */
$LOGIN="";
$PASSWORD="";
@include("_loginPassword.php"); // LOGIN ET MOT DE PASSE DEDANS, SI LE FICHIER EXISTE.
	
/* Fonctions internes: */
function getLastModFile()
{
	global $OUTDIR;
  $rid = opendir($OUTDIR);
	$lastmod=0;
	$lastfile="";
  while ($f = readdir($rid))
  {
		$isDir = is_dir($OUTDIR.$f);
		if ($isDir)
			continue;
		$tt = filemtime($OUTDIR.$f);
		if ($tt>$lastmod)
		{
			$lastmod=$tt;
			$lastfile=$f;
		}
	}
	return $lastfile;
}

function validateLoginPassword()
{
	global $LOGIN;
	global $PASSWORD;
	if (!isset($_REQUEST['login']))
		return false;
	if ($_REQUEST['login'] != $LOGIN)
		return false;
	if (!isset($_REQUEST['password']))
		return false;
	if ($_REQUEST['password'] != md5($PASSWORD))
		return false;
  return true;
}

/* Fonctions externes: (en fonction de l'url) */
if (isset($_REQUEST['put']))
{
	if (!validateLoginPassword())
		echo "<b>Bad Login/password</b><br/>";
	else if (file_exists($_FILES['fichier']['tmp_name']))
  {
    $newFile = $OUTDIR.$_FILES['fichier']['name'];
    @unlink($newFile);
    move_uploaded_file($_FILES['fichier']['tmp_name'], $newFile);
  }
}
else if (isset($_REQUEST['ls']))
{
	// renvoi la liste de fichiers
	// on se permet un petit tri par nom...
	$arr = Array();
  $rid = opendir($OUTDIR);
  while ($f = readdir($rid))
  {
		$arr[] = $f;
  }
  closedir($rid);
	sort($arr, SORT_STRING|SORT_FLAG_CASE);
	// et on dump la sortie
	for ($x=0;$x<count($arr);$x++)
	{
		$f = $arr[$x];
		echo $f;
		$isDir = is_dir($OUTDIR.$f);
		echo "|";
		if ($isDir) echo "DIR";
		else echo filesize($OUTDIR.$f);
		echo "\n";
	}	
}
else if (isset($_REQUEST['rm']))
{
	if (!validateLoginPassword())
		echo "<b>Bad Login/password</b><br/>";
	else
	{
		$delFile = $_REQUEST['fichier'];
		if ((strpos($delFile, '/')===FALSE))
		{
			$delFile = $OUTDIR.$delFile;
			@unlink($delFile);
		}
		else
			echo "<b>Security error</b><br/>";
	}
}
else if (isset($_REQUEST['get']))
{
  $theFile = $_REQUEST['get'];
	if ($theFile=="lastmod")
		$theFile = getLastModFile();
  if ((strpos($theFile, '/')===FALSE))
	{
		$theFile = $OUTDIR.$theFile;
    readfile($theFile);
	}
	else
		echo "<b>Security error</b><br/>";
}
else if (isset($_REQUEST['ls_lastmod']))
{
	// On veut "la derniere course active" => dernier fichier modifie
	echo getLastModFile();
}
else
{
  echo "<b>Unexpected request</b><br/>";
}
?>