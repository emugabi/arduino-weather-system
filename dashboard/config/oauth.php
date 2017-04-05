<?php
	/**
		* File to handle all Login requests
		* Accepts GET and POST
		* 
		/**
		* check for POST request 
	*/
	require_once 'DB_Functions.php';
	
		$db = new DB_Functions();
	
			// Request type is check Login
			$email = $_POST['email'];
			$password = $_POST['password'];
			
			// check for user
			$user = $db->getUserByEmailAndPassword($email, $password);
			//var_dump($user);
			if ($user != null) {
				//var_dump($user);
				$userid = $user['unique_id'];
				$permission = $user['permission'];
				$name = $user['name'];
				session_start();
			
				$_SESSION['name']= $name; 
				
					$db->redirect_to('../sermons.php');
				
				} else {
				$_SESSION['errors']= "Unknown username and password";
				$db->redirect_to('../index.php');
			}
?>