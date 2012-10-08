<?php

function send_email($dest, $subject, $text)
{
	$smtp_server = "smtp.gmail.com:587";
	$sender      = "vgafib@vgafib.com";
	$acount_name = "vgafib@vgafib.com";
	$acount_pass = "penetrar";

	$newfile="/tmp/".mt_rand(); 
	$file = fopen ($newfile, "w"); 
	fwrite($file, $text); 
	fclose ($file);

	exec("sendEmail -s $smtp_server -t $dest -f $sender -xu $acount_name -xp $acount_pass -o tls=yes -u \"$subject\" -o message-file=\"$newfile\" && rm $newfile &");
}

?>

