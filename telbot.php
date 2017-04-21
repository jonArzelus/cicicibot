<?php 

include "db/dbOpen.php";

try {
	
	$website="https://api.telegram.org/bot".$botToken;
	// read incoming info and grab the chatID
	$content = file_get_contents("php://input");
	$update = json_decode($content, true);
	$chatID = $update["message"]["chat"]["id"];
	//$chatID=1;
	$input = $update["message"]["text"];
	//$input="/start";

	//log activity
	$file="log.txt";
	date_default_timezone_set('Europe/Madrid');
	$date=date('Y-m-d H:i:s');
	$current=file_get_contents($file);
	$current.="New message from ".$chatID." at ".$date." with message ".$input."\n";
	file_put_contents($file, $current);
	file_get_contents($website."/sendMessage?chat_id=".$chatID."&text=¡Ci! Do /stop for no more Ci");
	//check commands
	$send=true;
	$ci=1;
	if($input=="/start") {
		$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
		$emaitza=$db->query($sql);
		if($emaitza->num_rows == 0) { //first login here
			$sql="INSERT INTO login_table(chat_id, last_update, login_date, active) VALUES('$chatID', '$date', '$date', 1)";
			$emaitza=$db->query($sql);

			//log register
			$current.="New login from ".$chatID." at ".$date.". Wii\n";
			file_put_contents($file, $current);
			file_get_contents($website."/sendMessage?chat_id=".$chatID."&text=¡Ci! Do /stop for no more Ci");
		}
	}
	if($input=="/stop") {
		$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
		$emaitza=$db->query($sql);
		if($emaitza->num_rows > 0) {
			$sql="UPDATE login_table SET active=0 WHERE chat_id='$chatID'";
			$emaitza=$db->query($sql);
			file_get_contents($website."/sendMessage?chat_id=".$chatID."&text=Do /start to start again");
			$send=false;
			//log register
			$current.="New logout (stop) from ".$chatID." at ".$date.". Snif\n";
			file_put_contents($file, $current);
		} else {
			$send=false;
		}
	}
	if($input=="/ci") {
		$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
		$emaitza=$db->query($sql);
		if($emaitza->num_rows > 0) {
			$ci=3;
		} else {
			$send=false;
		}
	}
	if($input=="/info") {
		$sql="SELECT * FROM login_table WHERE chat_id='$chatID'";
		$emaitza=$db->query($sql);
		if($emaitza->num_rows == 0) {
			$nCi = $emaitza->fetch_assoc()['ci_number'];
			file_get_contents($website."/sendMessage?chat_id=".$chatID."&text=Number of Ci's until now is ".$nCi." (+1), ¡wow!");
		}
	}

	//compose reply (stupid thingS)
	$reply =  "Ci";

	//update ci number and last update
	$sql="SELECT * FROM login_table WHERE chat_id='$chatID' AND active=1";
	$emaitza=$db->query($sql);
	if($emaitza->num_rows() > 0) {
		$ciNumber = $emaitza->fetch_assoc()['ci_number'];
		$ciNumber++;
		$sql="UPDATE login_table SET ci_number='$ciNumber', last_update='$date' WHERE chat_id='$chatID'";
		$emaitza=$db->query($sql);
	} else {
		$send=false;
	}

	//send reply
	if($send) {
		$sendto =$website."/sendmessage?chat_id=".$chatID."&text=".$reply;
		for($i=0;$i<$ci;$i++) {
			file_get_contents($sendto);
		}
	}

	function sendMessage()	{
		$message = "Ci";
		return $message;
	}

} catch (Exception $e) {
	$file="log.txt";
	$current=file_get_contents($file);
    $current.="Caught exception: ".", ". $e->getMessage(). "\n";
    file_put_contents($file, $current);
}

include "db/dbClose.php";

?>