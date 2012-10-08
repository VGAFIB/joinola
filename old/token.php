<?php
function make_seed()
{
	list($usec, $sec) = explode(' ', microtime());
	return (float) $sec + ((float) $usec * 100000);
}

mt_srand(make_seed());
function new_token()
{
	$index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$token = "";
	$randmax = strlen($index)-1;
	for ($n = 0; $n < 64; $n++)
	{
		$value = mt_rand(0, $randmax);
		$token .= substr($index, $value, 1);
	}

	return $token;
}

?>
