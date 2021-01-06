<?php

function setFirst() {
    global $mysqli;
    $sql = "UPDATE boardstatus SET next=".mt_rand(1, 2)." WHERE 1";
    $res = $mysqli->query($sql);
    if(!$res) {
        return false;
    }    
}

function gameStarted() {
    global $mysqli;

    $sql = "SELECT * FROM boardstatus";
    if($result = $mysqli->query($sql)) {
        if($row = $result->fetch_assoc()) {
            if($row["games_status"] == 1) {
                return true;
            } else {
                return false;
            }
            
        }
    }	
    else {
            echo "Error1: ".$mysqli->error();
    }
}

function setStarted($status) {
        global $mysqli;
    
        $sql = "UPDATE boardstatus SET games_status='".$status."' WHERE 1";
        $res = $mysqli->query($sql);
        if (!$res) {
            echo "(" . $mysqli->errno . ") " . $mysqli->error;
        }
}

function getReroll() {
    $zari1 = mt_rand(1, 6);
    $zari2 = mt_rand(1, 6);

    $apomenoun = 2;
    if($zari1 == $zari2) {
        $apomenoun = 4;
    }

    global $mysqli;

    $sql = "UPDATE boardstatus SET zari1=".$zari1.", zari2=".$zari2.", apomenoun=".$apomenoun." WHERE 1";
    $res = $mysqli->query($sql);
    if(!$res) {
        return false;
    }    

    return ['0'=>$zari1, '1'=>$zari2];
}

function currentRoll() {
    global $mysqli;

    $sql = "SELECT * FROM boardstatus";
    if($result = $mysqli->query($sql)) {
        if($row = $result->fetch_assoc()) {
                return ["0"=> (int)$row["zari1"], "1"=> (int)$row["zari2"]];
        }
    }	
    else {
            echo "Error1: ".$mysqli->error();
    }
}





function isPlayable() {
    global $mysqli;
    $sql = "SELECT loggedInUsers FROM boardstatus";
    if($stmt = $mysqli->prepare($sql)){
            $stmt->execute();
            $result=$stmt->get_result();
            if($row = $result->fetch_array()){
                    if($row[0] == 2) {
                            return true;
                    } else {
                            return false;
                    }
            }
            else{
                    return false;
            }
    }	
    else {
            echo "Error1: ".$mysqli->error();
    }
    
    $stmt->close();
}

function playingNow() { 
    global $mysqli;

    $sql = "SELECT * FROM boardstatus";
    if($result = $mysqli->query($sql)) {
        if($row = $result->fetch_assoc()) {
            if($row["next"] == 1) {
                return "red";
            } else {
                return "white";
            }
        }
    }	
    else {
            echo "Error1: ".$mysqli->error();
    }
}





?>