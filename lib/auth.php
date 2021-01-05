<?php 

//login flow
//an kanei login check to loggedInUsers apo boardStatus table 
//an einai 2 tote rixtou akyro
//an einai 0 | 1 tote dwstou xrwma pouliou 
	//vale sto boardStatus userId 1 | 2 kai to xrwma

    function login($username, $password) {
        global $mysqli;
        $sql = "SELECT * FROM users WHERE username=? AND passwd=?";
		if($stmt = $mysqli->prepare($sql))
		{
			$stmt->bind_param("ss", $username,$password);
			$stmt->execute();
			$result=$stmt->get_result();
			if($row = $result->fetch_array())
			{
				if($row[4] == 1) {
					header("HTTP/1.1 200 OK");
					header('Content-Type: application/json');
					print json_encode(['message'=>"Already logged in"]);
				} else {
					check($row[0]);

				}
			}
			else
			{
                // $_SESSION['username'] = "?";
				header("HTTP/1.1 401 Unauthorized");
			}
		}	
		else {
			echo "Error1: ".$mysqli->error();
		}
		
		$stmt->close();
	}


	function check($value) {
		global $mysqli;
		$sql = "SELECT * FROM boardstatus";
		if($result = $mysqli->query($sql)){
			if($row = $result->fetch_assoc()) {
				if($row["loggedInUsers"] == 2) {
					header("HTTP/1.1 200 OK");
					header('Content-Type: application/json');
					print json_encode(['message'=> 'Game is full']);
				} else {
					if(updatedConnected($value, 1)) {
						header("HTTP/1.1 200 OK");
						header('Content-Type: application/json');

						print json_encode(['color'=> setPlayer($row["loggedInUsers"])]);
					}
				}
			}
		}	
		else {
			echo "Error1: ".$mysqli->error();
		}
	
	}
	
	function setPlayer($value) {
		global $mysqli;
		if($value == 0) {
			$color = 'red';
			$sql = "UPDATE boardstatus SET color1='red', loggedInUsers='1' WHERE 1";
		} else {
			$color = 'white';
			$sql = "UPDATE boardstatus SET color2='white', loggedInUsers='2' WHERE 1";
		}
	
		$res = $mysqli->query($sql);
		if(!$res) {
			echo "(".$mysqli->errno.") ".$mysqli->error;
		}    
	
		return $color;
	}

	function updatedConnected($id, $connected) {
		global $mysqli;
		$sql = "UPDATE users SET connected='".$connected."' WHERE id=".$id."";
		$res = $mysqli->query($sql);
		if(!$res) {
			return false;
		}  
		return true;  	
	}

	function logout($username) {
		global $mysqli;

		$sql = "SELECT * FROM users where username='".$username."'";

		if($result = $mysqli->query($sql)) {
			if($row = $result->fetch_assoc()) {
				if($row["connected"] == 1) {
					updatedConnected($row["id"], 0);
					updateBoardLogged();
				}
			}
		}	
		else {
				echo "Error1: ".$mysqli->error();
		}	
	}

	function getLogged() {
		global $mysqli;

		$sql = "SELECT * FROM boardstatus where 1";

		if($result = $mysqli->query($sql)) {
			if($row = $result->fetch_assoc()) {
				return $row["loggedInUsers"];
			}
		} else {
			echo "Error1: ".$mysqli->error();
		}
	}


	function updateBoardLogged() {
		global $mysqli;
		$logged = getLogged();
		$logged--;
		$sql = "UPDATE boardstatus SET loggedInUsers='".$logged."' WHERE 1";
		$res = $mysqli->query($sql);
		if(!$res) {
			echo "(".$mysqli->errno.") ".$mysqli->error;
		}  
	}
?>