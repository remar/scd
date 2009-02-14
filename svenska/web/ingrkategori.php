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

[<a href="ingredienser.php">Lista alfabetiskt</a>] | [Lista efter kategori]

<?php include('ingrcommon.html'); ?>

<p>För att göra sidan överblickbar så står all mat som hör till en kategori på en rad. För att se eventuella kommentarer kan du hålla muspekaren över den maten. Bara den mat som är understruken har sådan extrainformation.</p>

<?php

$ingredients = array();

$categories = array("animalia" => "Animaliskt",
		    "dairy" => "Mejeriprodukter",
		    "vegetable" => "Grönsaker",
		    "roor" => "Rotfrukter",
		    "fruit" => "Frukter",
		    "nut" => "Nötter",
		    "spice" => "Kryddor/smaksättare",
		    "oil" => "Olja",
		    "product" => "Produkter",
		    "drink" => "Drickbart",
		    );

foreach(array_keys($categories) as $category)
{
  $ingredients[$category] = array();
}

$parser = xml_parser_create();

$name = "";
$category = "";
$comment = "";
$ingredients = array();

function start($parser, $element_name, $element_attrs)
{
  global $name;
  global $category;

  switch($element_name)
    {
    case "INGREDIENT":
      $name = (string)$element_attrs["NAME"];
      $category = (string)$element_attrs["CATEGORY"];
      break;
    }
}

function stop($parser, $element_name)
{
  global $name;
  global $category;
  global $comment;
  global $ingredients;

  switch($element_name)
    {
    case "INGREDIENT":

      $ingredients[$category][] = array("name" => $name,
					"comment" => trim($comment));
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

<table>

<?php

function cmp($a, $b)
{
  return strcmp($a["name"], $b["name"]);
}

foreach(array_keys($categories) as $category)
{
  if(count($ingredients[$category]) < 1)
    continue;

  usort($ingredients[$category], "cmp");

  echo '<tr><td class="heading">'.$categories[$category].'</td></tr>';

  echo '<tr><td>';

  foreach($ingredients[$category] as $ingredient)
    {
      if(strlen($ingredient["comment"]) > 0)
	{
	  echo '<span style="text-decoration: underline" title="'
	    .$ingredient["comment"].'">'.$ingredient["name"].'</span>, ';
	}
      else
	{
	  echo $ingredient["name"].', ';
	}
    }

  echo '</td></tr>';
}
?>

</table>

</td>

</body>
</html>
