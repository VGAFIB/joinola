<?php
require "config.php";
$msg = "";

$header = "ID,Nom,Cognoms,NIF,Email,Edat,Sexe,Comarca,";
$header.= "Coneguts,TipusPC,Especialitat,Estudis,FinEstudis,";
$header.= "Treball,BuscoFeina,Data";
$list = explode(",",$header);

$msg .= "<tr>\n";
foreach($list as $value){
	$msg .=	"<th>".$value."</th>\n";
}
$msg .= "</tr>\n";
$alt = false;

$sql  = "SELECT * FROM participantes ORDER BY Fecha ASC";
$dbc0 = mysql_query($sql);

$num = 0;
$edat = 0;
$homes = 0;
$coneguts = 0;

while($dbc0_res = mysql_fetch_assoc($dbc0)){
  unset($list);
  foreach ($dbc0_res as $value){
	$list[] = html_entity_decode($value,ENT_COMPAT,"UTF-8");
  }
  end($list);
  $key = key($list);
  $list[$key] = timestamp_afecha($list[$key]);
  
  if($alt) $msg .= "<tr class=\"alt\">\n";
  else $msg .= "<tr>\n";
  $alt = !$alt;
  foreach($list as $value){
    $msg .=	"<td>".$value."</td>\n";
  }
  $msg .= "</tr>\n";

  $num++;
  $edat += $list[5];
  $homes += $list[6];
  $coneguts += $list[8];
}

if ($num > 0)
{
	$edat /= $num;
	$coneguts /= $num;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title>Barcelona Game Jam Info</title>
<meta name="Description" content="Sitio web del Barcelona Game Jam 2011" /> 
<meta name="Keywords" content="Global Game Jam, Barcelona Game Jam, Game
Jam, Barcelona, España, Videojuegos, Desarrollo de Videojuegos, UPC, FIB,
VGAFIB" /> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="Distribution" content="Global" />
<meta name="Author" content="VGAFIB - vgafib@gmail.com" />
<LINK href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table class="inscrits">
<?php echo $msg; ?>
</table>
<br>
N&uacute;mero de participants: <?php echo $num; ?> <br>
Edat mitja: <?php echo round($edat);?> <br>
<?php
if ($num > 0)
{
	$foo = round($homes/$num*100);
	echo $foo."% homes, ".(100-$foo)."% dones";
}
?><br>
N&uacute;mero de coneguts mitja: <?php echo round($coneguts); ?>
</body>
</html>
<?php
cierrepagina();
?>
