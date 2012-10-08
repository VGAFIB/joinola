<?php
/////////////////////////////////////////
//VARIABLES DE CONEXIÓN A LA BBDD
/////////////////////////////////////////
$usernamedb           = "ggj";
$passdb               = "penistrap";
$dbserver             = "localhost";
$dbname               = "ggj";
$config_db_connection = @mysql_connect($dbserver,$usernamedb,$passdb);

if (!$config_db_connection){
  echo "Error al intentar conectarse con el servidor MySQL";
  exit();
}

$db = @mysql_select_db($dbname);
@mysql_query("SET NAMES 'utf8'");

/////////////////////////////////////////
//CIERRE DE PÁGINA Y SEGUIMIENTO
/////////////////////////////////////////

function cierrepagina() {

  mysql_close();
}
/////////////////////////////////////////
//FUNCIONES DE FECHA
/////////////////////////////////////////
//$fecha es una fecha formateada con día/meses/año sino devuelve 0
function fecha_atimestamp($fecha)
{
  list($day, $month, $year) = explode('/', $fecha);
    if (is_numeric($month) and is_numeric($day) and is_numeric($year)){   
     $timestamp=mktime(0, 0, 0, $month, $day, $year);
    }else $timestamp=0;   
  return $timestamp;  
}
//Devuelve el timestamp de la fecha pasada en la hora 00:00:00, sino es una fecha válida devuelve 0

//$fecha es un timestamp
function timestamp_afecha($timestamp)
{
    if(is_numeric($timestamp)){ 
        $fecha=date("d/m/Y",$timestamp);  
    }else $fecha=0;    
    return $fecha;
}

?>
