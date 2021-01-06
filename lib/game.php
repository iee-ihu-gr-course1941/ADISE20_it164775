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

function setWinner($value) {
    global $mysqli;
    $sql = "UPDATE boardstatus SET winner=".$value." WHERE 1";

    $res = $mysqli->query($sql);
    if(!$res) {
        echo "(".$mysqli->errno.") ".$mysqli->error;
    }    
}

function getWinner() {
    global $mysqli;

    $sql = "SELECT * FROM boardstatus";
    if($result = $mysqli->query($sql)) {
        if($row = $result->fetch_assoc()) {
            return $row["winnner"];
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

function playingNext() {
    global $mysqli;

    $sql = "SELECT * FROM boardstatus";
    if($result = $mysqli->query($sql)) {
        if($row = $result->fetch_assoc()) {
            setNext($row["next"]);
        }
    }	
    else {
            echo "Error1: ".$mysqli->error();
    }
}

function setNext($value) {
    global $mysqli;
    if($value == '1') {
        $sql = "UPDATE boardstatus SET next='2' WHERE 1";
    } else {
        $sql = "UPDATE boardstatus SET next='1' WHERE 1";
    }

    $res = $mysqli->query($sql);
    if(!$res) {
        echo "(".$mysqli->errno.") ".$mysqli->error;
    }    
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

function pouliThesi($pouli) {
    global $mysqli;
    $sql = "SELECT * FROM board WHERE id=".$pouli."";
    if($result = $mysqli->query($sql)) {
        if($row = $result->fetch_assoc()) {
                return $row["thesi"];
        }
    }	
    else {
            echo "Error1: ".$mysqli->error();
    }
}

function setBlocked($pouli, $blocked) {
    global $mysqli;

    $sql = "UPDATE board SET blocked=".$blocked." WHERE id=".$pouli."";
    $res = $mysqli->query($sql);
    if(!$res) {
        return false;
    }    

    return true;
}

function executeMove($pouli, $thesi, $paliaThesi, $xrwma) {
    global $mysqli;
    $blocked = getBlocked($xrwma);
    $antipaloiTheseis = $blocked["antipalesTheseis"];
    $theseis = $blocked["theseis"];

    if(count($antipaloiTheseis[$thesi]) == 1) {
        $antipaloPouli = $antipaloiTheseis[$thesi][0];
        setBlocked($antipaloPouli["id"], 1);
    }

    if(count($antipaloiTheseis[$paliaThesi]) == 1 && count($theseis[$paliaThesi]) == 1) {
        $antipaloPouli = $antipaloiTheseis[$paliaThesi][0];
        setBlocked($antipaloPouli["id"], 0);
    }

    $sql = "UPDATE board SET thesi=".$thesi." WHERE id=".$pouli."";
    $res = $mysqli->query($sql);
    if(!$res) {
        return false;
    }    
    return true;
}

function getBlocked($xrwma) {
    global $mysqli;

    $sql = "SELECT * FROM board";
    if ($result = $mysqli->query($sql)) {
        $boardWhite = array();
        $boardRed = array();
        for ($i = 1; $i <= 24; $i++) {
            $boardRed[$i] = [];
            $boardWhite[$i] = [];
        }

        while ($row = $result->fetch_assoc()) {
            if ($row["color"] == 'red') {
                $boardRed[$row["thesi"]][] = ["id" => $row["id"], "color" => $row["color"], "blocked" => $row["blocked"]];
            } else {
                $boardWhite[$row["thesi"]][] = ["id" => $row["id"], "color" => $row["color"], "blocked" => $row["blocked"]];
            }
        }

        if($xrwma == "red") {
            return ["antipalesTheseis" => $boardWhite, "theseis" => $boardRed];
        } else {
            return ["antipalesTheseis" => $boardRed, "theseis" => $boardWhite];
        }
    }
}

function getBoardStatus() {
    global $mysqli;

    $sql = "SELECT * FROM boardstatus";
    if($result = $mysqli->query($sql)) {
        if($row = $result->fetch_assoc()) {
                return ["next"=> $row["next"], "apomenoun"=>$row["apomenoun"]];
        }
    }	
    else {
            echo "Error1: ".$mysqli->error();
    }
}

function zariPouPaixtike() {
    global $mysqli;

    $sql = "SELECT * FROM boardstatus";
    if($result = $mysqli->query($sql)) {
        if($row = $result->fetch_assoc()) {
                return $row["paixtike"];
        }
    }	
    else {
            echo "Error1: ".$mysqli->error();
    }
}

function updateBoardStatus($zari) {
    global $mysqli;

    $boardStatus = getBoardStatus();
    $apomenoun = $boardStatus["apomenoun"];
    if($apomenoun > 0) {
        $apomenoun--;
    }

    $sql = "UPDATE boardstatus SET apomenoun=".$apomenoun.", paixtike='".$zari."' WHERE 1";

    $res = $mysqli->query($sql);
    if(!$res) {
        echo "(".$mysqli->errno.") ".$mysqli->error;
    }    
    
    $boardStatus = getBoardStatus();
    
    if($apomenoun == 0) {
        playingNext();
    }

    if($boardStatus["apomenoun"] == 0) {
        if($boardStatus["next"] == '1') {
            return "white";
        } else {
            return "red";
        }
    } else {
        if($boardStatus["next"] == '1') {
            return "red";
        } else {
            return "white";
        }
    }
}


?>