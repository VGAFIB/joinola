<?php
$lang_sel = "cat";
if(isSet($_GET['lang']))
{
	$lang_sel = $_GET['lang'];
}
 
switch ($lang_sel) {
	case 'cat':
	break;
	case 'es':
	break;
	case 'en':
	break;
	default:
		$lang_sel = "cat";
}
 
include_once 'lang.'.$lang_sel.'.php';
?>
