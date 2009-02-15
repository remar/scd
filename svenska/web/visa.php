<html>

<head>
<title>SCD på svenska - recept</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>

<body>

<table>
<tr>

<?php include('menu.html'); ?>

<td valign="top" align="left">

<?php 

$recipe_name = "";
$text = "";
$ingredients = array();
$instructions = array();
$notes = array();

$recipe_file = 'recept/'.$_GET['recept'];

$parser = xml_parser_create();

function start($parser, $element_name, $element_attrs)
{
  global $recipe_name;
  global $state;
  global $text;

  switch($element_name)
    {
    case "RECIPE":
      $recipe_name = (string)$element_attrs["NAME"];
      break;

    case "LINK":
      $text .= "<a href=\"visa.php?recept=".$element_attrs["HREF"].".xml\">".$element_attrs["NAME"]."</a>";
      break;
    }
}

function stop($parser, $element_name)
{
  global $text;
  global $ingredients;
  global $instructions;
  global $notes;

  switch($element_name)
    {
    case "INGREDIENT":
      $ingredients[] = $text;
      $text = "";
      break;

    case "INSTRUCTIONS":
      $instructions = explode("\n\n", $text);
      $text = "";
      break;

    case "NOTE":
      $notes[] = $text;
      $text = "";
      break;
    }
}

function char($parser, $data)
{
  global $text;

  $text .= $data;
}

xml_set_element_handler($parser, "start", "stop");
xml_set_character_data_handler($parser, "char");

$fp = fopen($recipe_file,"r");

if($fp == FALSE)
  {
    echo '<h2>Oj då, jag kan inte hitta det receptet!</h2>';
  }

while($data=fread($fp, 4096))
  {
    xml_parse($parser, $data, feof($fp)) or die("XML Error");
  }

xml_parser_free($parser);

?>


<?php echo '<h2>'.$recipe_name.'</h2>'; ?>
<h3>Ingredienser</h3>

<ul>
<?php 

foreach($ingredients as $ingrediens)
{
  echo '<li>'.$ingrediens.'</li>';
}

?>
</ul>

<h3>Instruktioner</h3>

<?php
foreach($instructions as $instruction)
{
  echo '<p>'.$instruction.'</p>';
}

if(count($notes) > 0)
  {
    echo '<h3>Noter</h3>';
    foreach($notes as $note)
      {
	echo '<p>'.$note.'</p>';
      }
  }
?>

</td>
</tr>
</table>
</body>
</html>
