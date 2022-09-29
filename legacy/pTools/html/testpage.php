<?php
/* This is a default test page to test default CSS */
include("../main.php");
pTools::initialize("..");
CHTML::prolog_XHTML();
?>
<head>
<?php
  CHTML::head_stylesheet_default();
  ?>
</head>
<body>
<h1>A title in H1</h1>
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
<br/>
<br/>
<h2>A title in H2</h2>
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
<br/>
<br/>
<h3>A title in H3</h3>
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
<br/>
<br/>
<h4>A title in H4</h4>
followed by normal text it should have only big space after but not before followed by normal text it should have only big space after but not before
<div>Also please notice this text is encapsulated in a DIV object</div><div>Also please notice this text is encapsulated in a DIV object</div>
<div>Also please notice this text is encapsulated in a DIV object</div>
<br/>
<br/>
<a href="testpage.php">This is a default link with <img src="testpicture.png" alt="testpicture" /> a default picture</a><br/>
<br/>
Now you get a table for placing objects:<br/>
<table><thead>
<tr>
  <th>Header 1</th>
  <th>Header 2</th>
</tr>
<tr>
  <td>You notice this is the first cell. Cell 1</td>
  <td>Cell 2</td>
</tr>
<tr>
  <td>Cell 3</td>
  <td>Cell 4</td>
</tr>
</tbody></table>
<br/>
Now you get a table for basic presentation:<br/>
<table class="classicTable"><thead>
<tr>
  <th>Header 1</th>
  <th>Header 2</th>
</tr>
<tr>
  <td>You notice this is the first cell. Cell 1</td>
  <td>Cell 2</td>
</tr>
<tr>
  <td>Cell 3</td>
  <td>Cell 4</td>
</tr>
</tbody></table>
<br/>
<br/>
Now you get a default formular:
<form>
<input type="text" name="theText" value="A text input"/><br/>
A select <select>
  <option>option 1</option>
  <option>option 2</option>
</select>
<br/>
<label><input type="checkbox"/>a checkbox</label>
<br/>
<fieldset>
<legend>A fieldset</legend>
<input type="button" value="ok"/><input type="button" value="cancel"/>
</fieldset>
</form>
</body>
</html>