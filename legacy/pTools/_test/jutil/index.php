<?php
/* ATTENTION, ce fichier est encode en utf-8 */
include("../../pTools/main.php");
pTools::initialize("../../pTools");
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>
<body>
<h1>Test du module "jutil", partie UTF-8 encoder/decoder</h1>
<?php
pTools::getpTools()->loadJavaScript();
?>
é
<script language="Javascript" type="text/javascript">
var str = "<?xml version=\"1.0\" encoding=\"utf-8\"?><div>méchant garçon</div>";
var parser = new ncDOMParser();
var doc = parser.parseFile("config.xml");
alert(ncNodeDump(doc));
</script>
</body>
</html>