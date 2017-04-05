<?php
   	include("connect.php");
   	
   	$link=Connection();

	$temp1=$_GET["temp1"];
	$hum1=$_GET["hum1"];


	$query = "INSERT INTO `weather` (`temperature`, `humidity`,`created_at`) 
		VALUES ('".$temp1."','".$hum1."',NOW())"; 
   	
   	mysql_query($query,$link);
	echo "Successfully Saved!";
?>
