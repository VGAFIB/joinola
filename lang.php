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

/*
$lang_dump = "<?php\n\$lang = ".var_export($lang, true).";\n?>";
$lang_dump = str_replace("\\'", "#$%&", $lang_dump);
$lang_dump = str_replace("'", "\"", $lang_dump);
$lang_dump = str_replace("#$%&", "'", $lang_dump);
$lang_dump = html_entity_decode($lang_dump);
file_put_contents(dirname(__FILE__)."/langs/lang.".$lang_sel.".php",$lang_dump);
*/

foreach ($lang as $key => $value)
{
	$aux = htmlentities($value,ENT_NOQUOTES,'UTF-8',false);
	$lang[$key] = str_replace(array('&lt;','&gt;'),array('<','>'), $aux);	
}
?>
