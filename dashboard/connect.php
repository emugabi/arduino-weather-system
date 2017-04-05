
<?php

	function Connection(){
	   	
		$connection = mysql_connect("localhost", "root","");

		if (!$connection) {
	    	die('MySQL ERROR: ' . mysql_error());
		}
		mysql_select_db("weather",$connection); 
      
		return $connection;
	}
?>
