<?php
	
	
	class DB_Functions {
		
		protected $db;
		
		function __construct() {
			require_once 'DB_Connect.php';
			// connecting to database
			
			$_db =  new DB_Connect();
			$this->db  = $_db->con();
			
		}
		
		// destructor
		function __destruct() {
			
		}
		
		function redirect_to ($location = NULL) {
			if ($location != NULL) {
				header('Location: ' . $location);
				exit;
			}
		}
		
		
		
		/**
			* Storing new user
			* returns user details
		*/
		public function storeUser($name, $email, $password, $permission) {
			
			$name = mysqli_real_escape_string( $this->db, $name );
			$email = mysqli_real_escape_string( $this->db, $email );
			$password = mysqli_real_escape_string( $this->db, $password );
			$permission = mysqli_real_escape_string( $this->db, $permission );
			
			$uuid = uniqid('', false);
			$hash = $this->hashSSHA($password);
			$encrypted_password = $hash["encrypted"]; // encrypted password
			$salt = $hash["salt"]; // salt
			//$permission = "sermons";
			$result = mysqli_query($this->db,"INSERT INTO users (id, unique_id, name, encrypted_password, salt, email, permission, created_at) VALUES(NULL, '$uuid', '$name', '$encrypted_password', '$salt' ,'$email', '$permission', NOW())") or die(mysqli_error($this->db));
			// check for successful store
			if ($result) {
				// get user details
				$unique_id = mysqli_insert_id($this->db); // last inserted id
				$query = mysqli_query($this->db,"SELECT name, permission, unique_id FROM users WHERE unique_id = $unique_id") or die(mysqli_error($this->db));
				// return user details
				$result2 = mysqli_fetch_array($query);
				$data['permission'] = $result2['permission'];
				$data['unique_id'] = $result2['unique_id'];
				$data['name'] = $result2['name'];
				$json_content = json_encode(array("timestamp"=> date("Y-m-d h:i:s", time()),"error" => array("code"=>100, "description"=>"User registration failed!", "result"=>$result )), JSON_UNESCAPED_SLASHES);
				$fp = fopen('errors.json', 'w+');
				fwrite($fp, $json_content );
				fclose($fp);
				return $data;
				} else {
				$json_content = json_encode(array("timestamp"=> date("Y-m-d h:i:s", time()),"error" => array("code"=>100, "description"=>"User registration failed!", "result"=>$result )), JSON_UNESCAPED_SLASHES);
				$fp = fopen('errors.json', 'w+');
				fwrite($fp, $json_content );
				fclose($fp);
				return null;
				
			}
		}
		
		/**
			* Get user by email and password
		*/
		public function getUserByEmailAndPassword($email, $password) {
			$email = mysqli_real_escape_string( $this->db, $email );
			$password = mysqli_real_escape_string( $this->db, $password );
			$query = mysqli_query($this->db,"SELECT unique_id, permission, salt, encrypted_password, name  FROM users WHERE email = '$email'") or die(mysqli_error($this->db));
			// check for result
			$no_of_rows = mysqli_num_rows($query);
			if ($no_of_rows > 0) {
				$result = mysqli_fetch_array($query);
				$salt = $result['salt'];
				$data['permission'] = $result['permission'];
				$data['name'] = $result['name'];
				$data['unique_id'] = $result['unique_id'];
				$encrypted_password = $result['encrypted_password'];
				$hash = $this->checkhashSSHA($salt, $password);
				// check for password equality
				if ($encrypted_password == $hash) {
					// user authentication details are correct
					//echo $permission;
					//var_dump($result, true);
					$unique_id = $data['unique_id'];
					$result = mysqli_query($this->db, "UPDATE users SET lastlogin = NOW() WHERE unique_id = '$unique_id'")or die(mysqli_error($this->db));
					return $data;
				}
				else {
					//echo 'Wrong password combination';
					//var_dump($result);
					$json_content = json_encode(array("timestamp"=> date("Y-m-d h:i:s", time()),"error" => array("code"=>101, "description"=>"Wrong password combination!", "result"=>$result )), JSON_UNESCAPED_SLASHES);
					$fp = fopen('errors.json', 'w+');
					fwrite($fp, $json_content );
					fclose($fp);
					return null;
				}
				} else {
				
				return  null;
			}
		}
		
		/**
			* Check user is existed or not
		*/
		public function isUserExisted($email) {
			$result = mysqli_query($this->db, "SELECT email from users WHERE email = '$email'");
			$no_of_rows = mysqli_num_rows($result);
			if ($no_of_rows > 0) {
				// user existed
				return true;
				} else {
				// user not existed
				return false;
			}
		}
		
		public function isUniqueIDExisted($unique_id) {
			$result = mysqli_query($this->db, "SELECT unique_id from users WHERE unique_id = '$unique_id'");
			$no_of_rows = mysqli_num_rows($result);
			if ($no_of_rows > 0) {
				// user existed
				return true;
				} else {
				// user not existed
				return false;
			}
		}
		
		
		
		
		
		
		/**
			* Check request validity by unique_id
		*/
		public function isRequestValid($unique_id) {
			$result = mysqli_query($this->db, "SELECT unique_id from users WHERE unique_id = '$unique_id'");
			$no_of_rows = mysqli_num_rows($result);
			if ($no_of_rows > 0) {
				// user existed
				return true;
				} else {
				// user not existed
				return false;
			}
		}
		/**
			* Encrypting password
			* @param password
			* returns salt and encrypted password
		*/
		public function hashSSHA($password) {
			
			$salt = sha1(rand());
			$salt = substr($salt, 0, 10);
			$encrypted = base64_encode(sha1($password . $salt, true) . $salt);
			$hash = array("salt" => $salt, "encrypted" => $encrypted);
			return $hash;
		}
		
		/**
			* Decrypting password
			* @param salt, password
			* returns hash string
		*/
		public function checkhashSSHA($salt, $password) {
			
			$hash = base64_encode(sha1($password . $salt, true) . $salt);
			
			return $hash;
		}
		
		
		public function saveTempHumid($deviceCode, $temperature, $humidity){ 
			
			// escape variables for security
			$query = mysqli_query($this->db,"INSERT INTO readings( id, deviceCode, temperature, humidity, date) VALUES (NULL, '$deviceCode', '$temperature', '$humidity', '$date' )")or die(mysqli_error($this->db));
			
			if ($query) {
				
				return true;
				} else {
				
				return false;
			}
		}
		
		public function getAllTempHumid($startDate, $endDate) {
			
			
			$result = mysqli_query($this->db, "SELECT * from readings WHERE date >= '$startDate' AND date <= '$endDate' ORDER BY date DESC");
			
			$no_of_rows = mysqli_num_rows($result);
			if ($no_of_rows > 0) {
				
				$data = array();
				while ($row = mysqli_fetch_assoc($result)) {
					$data[] = $row;
				}
				//$result = mysqli_fetch_assoc($result);
				return $data;
				} else {
				return null;
			}
		}
		
		
		
	
		public function updateTemperature($id, $temp, $date){
			$temp = mysqli_real_escape_string( $this->db, $temp );
			$data = mysqli_real_escape_string( $this->db, $date );
			
			$result = mysqli_query($this->db, "UPDATE readings SET temp = '$temp', date = '$date',  WHERE id = '$id'")or die(mysqli_error($this->db));
						
		}
		
		
	}
	
?>