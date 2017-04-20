<?php 
define('BOT_TOKEN', '336957371:AAGBxWDw3LbRtEWPjfCqweYbtkhVoRdi3rY');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
	
// read incoming info and grab the chatID
$content = file_get_contents("php://input");
$update = json_decode($content, true);
$chatID = $update["message"]["chat"]["id"];

//log activity
/*$myfile = fopen("log.txt", "w") or die("Unable to open file!");
date_default_timezone_set('Europe/Madrid');
$date=date('Y-m-d H:i:s');
$txt = "New message from ".$chatID." at ".$date;
fwrite($myfile,$txt);
fclose($myfile);*/
$file="log.txt";
date_default_timezone_set('Europe/Madrid');
$date=date('Y-m-d H:i:s');
$current=file_get_contents($file);
$current.="New message from ".$chatID." at ".$date."\n";
file_put_contents($file, $current);
		
// compose reply
$reply =  sendMessage();
		
// send reply
$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=".$reply;
file_get_contents($sendto);
//$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=".$update["message"]["text"];
//file_get_contents($sendto);

function sendMessage(){
$message = "Ci";
return $message;
}
?>