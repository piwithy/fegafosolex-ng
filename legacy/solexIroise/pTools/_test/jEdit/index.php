<?php
include("../../pTools/main.php");
pTools::initialize("../../pTools");

$initialText = "Initial text: <b>in bold</b> <u>underlined</u> <b><u>BOTH</b></u>";
if (isset($_GET['post']))
{
  $text=$_POST['monEditeur'];
}
else
  $text=$initialText;
?>
<html>
<head>
<?php pTools::getpTools()->loadJavascript(); ?>
</head>
<body>
<form action="index.php?post" method="post">
<table><tbody>
<tr>
  <td>Champs normal:</td>
  <td><input type="text" name="monChamps" value=""/></td>
</tr>
<tr>
  <td>Editeur:</td>
  <td><div id='editor1' name="monEditeur"><?php echo $text; ?></div></td>
</tr>
</tbody></table>
</form>
<script language="Javascript">
var editor1 = new jEditor(document.getElementById('editor1'));
editor1.start();
</script>
</body>
</html>