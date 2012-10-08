<?php

include_once "lang.php";
include_once "mail.php";

//COMPROBACIÓN DE QUE NO SE HA DADO A ACTUALIZAR
if($_COOKIE['rand']!=$_POST['rand'] OR !isset($_COOKIE['rand'])){
    setcookie("rand",$_POST['rand'],time()+3600*8);
    $ins=true;
}
else {
  $ins = false;
  $msg = $lang['REG_RESEND']; 
}

require "config.php";

//INSERTAR DATOS
if($ins){
  $nom          = mysql_real_escape_string($_POST['nom']);
  $nom          = htmlentities($nom,ENT_QUOTES,"UTF-8");
  $cognoms      = mysql_real_escape_string($_POST['cognoms']);
  $cognoms      = htmlentities($cognoms,ENT_QUOTES,"UTF-8");
  $dni          = mysql_real_escape_string($_POST['dni']);
  $dni          = strtoupper($dni);
  $dni          = htmlentities($dni,ENT_QUOTES,"UTF-8");
  $email        = mysql_real_escape_string($_POST['email']);
  $email        = htmlentities($email,ENT_QUOTES,"UTF-8");
  $edat         = mysql_real_escape_string($_POST['edat']);
  $edat         = htmlentities($edat,ENT_QUOTES,"UTF-8");
  $sexe         = mysql_real_escape_string($_POST['sexe']);
  $comarca      = mysql_real_escape_string($_POST['comarca']);
  $comarca      = htmlentities($comarca,ENT_QUOTES,"UTF-8");
  $coneguts     = mysql_real_escape_string($_POST['coneguts']);
  $coneguts     = htmlentities($coneguts,ENT_QUOTES,"UTF-8");
  $tipuspc      = mysql_escape_string($_POST['t_pc']);
  $especialitat = mysql_escape_string($_POST['especialitat']);
  $estudis      = mysql_escape_string($_POST['estudis']);
  $treball      = mysql_escape_string($_POST['treball']);
  $fecha        = fecha_atimestamp(date("d/m/Y"));
  if(!$_POST['FinEst']) $finestudis = 0;
  else $finestudis = 1;
  if(!$_POST['BuscFeina']) $buscafeina = 0;
  else $buscafeina = 1;
  $sql = "INSERT INTO participantes (
          `ID`     ,
          `Nom`     ,
          `Cognoms` ,
          `NIF`     ,
          `Email`   ,
          `Edat`    ,
          `Sexe`    ,
          `Comarca` ,
          `Coneguts`,
          `TipusPC` ,
          `Especialitat` ,
          `Estudis` ,
          `FinEstudis` ,
          `Treball` ,
          `BuscoFeina`,
          `Fecha` 
          )
          VALUES (NULL,'$nom','$cognoms','$dni','$email',$edat,$sexe,
                  '$comarca',$coneguts,'$tipuspc','$especialitat','$estudis',
                  $finestudis,'$treball',$buscafeina,$fecha)";
  if(mysql_query($sql)){
    $msg = $lang['REG_SUCCESS'];
    //Actualizar csv
    $filename = "inscritos/inscritos.csv";
    $handle = fopen($filename, "w+");
	$header = "ID,Nom,Cognoms,NIF,Email,Edat,Sexe,Comarca,";
	$header.= "Coneguts,TipusPC,Especialitat,Estudis,FinEstudis,";
	$header.= "Treball,BuscoFeina,Data";
    $list = explode(",",$header);
    fputcsv($handle,$list,",");
    $sql  = "SELECT * FROM participantes ORDER BY Fecha ASC";
    $dbc0 = mysql_query($sql);    
    while($dbc0_res = mysql_fetch_assoc($dbc0)){
      unset($list);
      foreach ($dbc0_res as $value){
        $list[] = html_entity_decode($value,ENT_COMPAT,"UTF-8");
      }
      end($list);
      $key = key($list);
      $list[$key] = timestamp_afecha($list[$key]);
      fputcsv($handle,$list,",");
    }
    fclose($handle);

	//Enviar emails informatorios
	$nom_     = html_entity_decode($nom);
	$cognoms_ = html_entity_decode($cognoms);

	$mailbody  = "S'acava de realitzar una nova inscripcio, les dades son:\n";
	$mailbody .= "$cognoms_, $nom_ amb DNI: $dni i email: $email. El seu idioma preferent es ".strtoupper($lang_sel);
	send_email("vgafib@gmail.com", "Nova inscripcio: $nom_ $cognoms_", $mailbody);

	$mailbody  = $lang['EMAIL_HELLO']." ".$nom_.",\n\n".$lang['EMAIL_SUCCESS_TEXT']; 
	send_email($email, $lang['EMAIL_SUCCESS_SUBJECT'], $mailbody);
  }
  else 
    $msg= $lang['REG_ERROR']." gamejam (AT) vgafib.com";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title><?php echo $lang['REG_TITLE'];?></title>
<meta name="Description" content="<?php echo $lang['PAGE_DESCRIPTION'];?>" /> 
<meta name="Keywords" content="Global Game Jam, Barcelona Game Jam, Game
Jam, Barcelona, España, Videojuegos, Desarrollo de Videojuegos, UPC, FIB,
VGAFIB" /> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="Distribution" content="Global" />
<meta name="Author" content="VGAFIB - vgafib@gmail.com" />
<LINK href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php echo $msg; ?>
</body>
</html>
<?php
cierrepagina();
?>
