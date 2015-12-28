<?php
define('GJB_POSTCOMMAND_NAME', "gjb_command");
define('GJB_PAY_ON_PAYPAL', false);

$gjb_table_sufix_name = "gjb_participants";
$gjb_cost = "5.00";
$gjb_online_discount_percent = "0";
$gjb_normativa_link = "https://gamejambcn.com/gjb-2016/normativa/";

function gjb_strleft($s1, $s2)
{
	return substr($s1, 0, strpos($s1, $s2));
}

function gjb_fecha_atimestamp($fecha)
{
	list($day, $month, $year) = explode('/', $fecha);
	if (is_numeric($month) and is_numeric($day) and is_numeric($year))
	{   
		$timestamp=mktime(0, 0, 0, $month, $day, $year);
	}
	else $timestamp=0;   
	return $timestamp;  
}

function gjb_selfURL()
{
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$protocol = gjb_strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

function gjb_getCommand()
{
	global $gjb_postcommand_name;

	if (!isset($_POST)) return "";
	else if (!isset($_POST[GJB_POSTCOMMAND_NAME]))
	{
		if (!isset($_GET)) return "";
		else if (!isset($_GET[GJB_POSTCOMMAND_NAME])) return "";
		return $_GET[GJB_POSTCOMMAND_NAME];
	}
	return $_POST[GJB_POSTCOMMAND_NAME];
}

function gjb_ParsePost($namepost, $namecolumn, &$values)
{
	if (!isset($_POST[$namepost])) return false;

	$values[$namecolumn] = mysql_real_escape_string($_POST[$namepost]);
	$values[$namecolumn] = htmlentities($values[$namecolumn],ENT_QUOTES,"UTF-8");

	return true;
}

function gjb_fl2str($in, $format = "%01.2f")
{
	$val = $in;
	$val2 = sprintf($format, $val);
	$val = $val2;
	return $val;
}

function sprintf2($str='', $vars=array(), $char='%')
{
	if (!$str) return '';
	if (count($vars) > 0)
	{
		foreach ($vars as $k => $v)
		{
			$str = str_replace($char . $k, $v, $str);
		}
	}

	return $str;
}
?>
