<html>

<head>
<title>SCD på svenska - tillåten mat</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>

<body>

<table>
<tr>

<?php include('menu.html'); ?>

<td valign="top" align="left">

<h2>Tillåten mat</h2>

[Lista alfabetiskt] | [<a href="ingrkategori.php">Lista efter kategori</a>]

<?php include('ingrcommon.html'); ?>

<?php

$parser = xml_parser_create();

$name = "";
$comment = "";
$ingredients = array();

function start($parser, $element_name, $element_attrs)
{
  global $name;

  switch($element_name)
    {
    case "INGREDIENT":
      $name = (string)$element_attrs["NAME"];
      break;
    }
}

function stop($parser, $element_name)
{
  global $name;
  global $comment;
  global $ingredients;

  switch($element_name)
    {
    case "INGREDIENT":

      $index = $name[0];

      foreach(array("Å", "Ä", "Ö") as $letter)
	{
	  $namu = strstr($name, $letter);
	  if($name == $namu)
	    {
	      $index = $letter;
	      break;
	    }
	}

      $ingredients[$index][] = array("name" => $name, "comment" => $comment);
      $comment = "";
      break;
    }
}

function char($parser, $data)
{
  global $comment;

  $comment .= $data;
}

xml_set_element_handler($parser, "start", "stop");
xml_set_character_data_handler($parser, "char");

$fp = fopen("ingredienser.xml","r");

while($data=fread($fp, 4096))
  {
    xml_parse($parser, $data, feof($fp)) or die("XML Error");
  }

xml_parser_free($parser);

?>

<table><tr><td><span style="font-size:x-large">Mat</span></td>
           <td><span style="font-size:x-large">Kommentar</span></td></tr>

<?php

function cmp($a, $b)
{
  return strcmp($a["name"], $b["name"]);
}

foreach(array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O',
	      'P','Q','R','S','T','U','V','X','Y','Z','Å','Ä','Ö') as $letter)
{
  if(count($ingredients[$letter]) < 1)
    continue;

  usort($ingredients[$letter], "cmp");

  echo '<tr><td class="heading"><em>'.$letter.'</em></td><td></td></tr>';

  foreach($ingredients[$letter] as $ingredient)
    {
      echo '<tr><td>'.$ingredient["name"].'</td><td>';
      
      if(strlen($ingredient["comment"]) > 0)
	echo $ingredient["comment"];

      echo '</td></tr>';
    }
}
?>

</table>

</td>
</tr>
</table>

</body>
</html>
