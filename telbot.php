<?php 

include "db/dbOpen.php";
	
// read incoming info and grab the chatID
$content = file_get_contents("php://input");
$update = json_decode($content, true);
$chatID = $update["message"]["chat"]["id"];
$input = $update["message"]["text"];

//log activity
$file="log.txt";
date_default_timezone_set('Europe/Madrid');
$date=date('Y-m-d H:i:s');
$current=file_get_contents($file);
$current.="New message from ".$chatID." at ".$date."\n";
file_put_contents($file, $current);

//check commands
$send=true;
$ci=1;
if($input=="/start") {
	$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
	$emaitza=$db->query($sql);
	if($emaitza->num_rows == 0) { //first login here 
		$sql="INSERT INTO start_table(chat_id, last_update, login_date, active) VALUES('$chatID', '$date', '$date', 'true')";
		$emaitza=$db->query($sql);
		file_get_contents(API_URL."/sendMessage?chat_id=".$chatID."&text=¡Ci! Do /stop for no more Ci");
	}
} else if($input=="/stop") {
	$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
	$emaitza=$db->query($sql);
	if($emaitza->num_rows == 0) {
		$sql="UPDATE start_table SET active='false' WHERE chat_id='$chatID'";
		$emaitza=$db->query($sql);
		file_get_contents(API_URL."/sendMessage?chat_id=".$chatID."&text=Do /start to start again");
		$send=false;
	}
} else if($input=="/ci") {
	$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
	$emaitza=$db->query($sql);
	if($emaitza->num_rows == 0) {
		$ci=3;
	}
} else if($input=="/info") {
	$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
	$emaitza=$db->query($sql);
	if($emaitza->num_rows == 0) {
		$nCi = $emaitza->fetch_assoc()['ci_number'];
		file_get_contents(API_URL."/sendMessage?chat_id=".$chatID."&text=Number of Ci's until now is ".$nCi." (+1), ¡wow!");
	}
}

//compose reply (stupid thingS)
$reply =  sendMessage();

//update ci number
$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
$emaitza=$db->query($sql);
$ciNumber = $emaitza->fetch_assoc()['ci_number'];
$ciNumber++;
$sql="UPDATE start_table SET ci_number='$ciNumber', last_update='$date' WHERE chat_id='$chatID'";
$emaitza=$db->query($sql);
		
//send reply
if($send) {
	$sendto =API_URL."/sendmessage?chat_id=".$chatID."&text=".$reply;
	for($i=0;$i<$ci;$i++) {
		file_get_contents($sendto);
	}
}

function sendMessage()	{
	$message = "Ci";
	return $message;
}

include "db/dbClose.php";

}
?>