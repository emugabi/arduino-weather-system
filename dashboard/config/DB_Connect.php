<?php
	
	class DB_Connect {
		private $con = null;
		// constructor
		function __construct() {
			
		}
		
		// destructor
		function __destruct() {
			// $this->close();
		}
		
		// Connecting to database
		public function con()
		{
			
			$result = mysqli_connect("localhost", "root", "", "smartweather");
			if( mysqli_connect_error()) echo "Failed to connect to MySQL: " . mysqli_connect_error();
			
			return $result;
		}
		
		// Closing database connection
		public function close() {
			mysqli_close();
		}
		
	}
	
?>	