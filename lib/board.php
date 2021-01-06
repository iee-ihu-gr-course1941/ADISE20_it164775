<?php

function initBoard() {
    global $mysqli;

    $sql = "REPLACE INTO board select * from initboard";
    $res = $mysqli->query($sql);
    if (!$res) {
        echo "(" . $mysqli->errno . ") " . $mysqli->error;
    }

    $sql = "UPDATE boardstatus SET zari1='0', zari2='0', apomenoun='0', winnner=NULL, games_status='0', paixtike='0' WHERE 1";
    $res = $mysqli->query($sql);
    if (!$res) {
        echo "(" . $mysqli->errno . ") " . $mysqli->error;
    }
}

function movesAreValid($pouli, $thesi, $xrwma) {
    $zaria = currentRoll();
    $zari1 = $zaria["0"];
    $zari2 = $zaria["1"];

    $playingNow = playingNow();
    if($playingNow != $xrwma) {
        return ["zaria" => $zaria, "playing"=> $playingNow, "kiniseis"=> getKiniseis($playingNow, $zari1, $zari2)];
    }

    if($zari1 != $zari2) {
        $zariPouPaixtike = zariPouPaixtike();
        if($zariPouPaixtike == 1) {
            $moves = getKiniseis($xrwma, 0, $zari2);
        } else if($zariPouPaixtike == 2) {
            $moves = getKiniseis($xrwma, $zari1, 0);
        } else {
            $moves = getKiniseis($xrwma, $zari1, $zari2);
        }
    } else {
        $moves = getKiniseis($xrwma, 0, $zari2);
    }


    $winner = getWinner();
    if($winner == "red" || $winner == "white" || $moves == 1) {
        initBoard();
        return $winner." won2";
    }

    if($pouli == 0 && $thesi == 0 && count($moves) < 1) {
        playingNext();
        $zaria = getReroll();
        $zari1 = $zaria["0"];
        $zari2 = $zaria["1"];
        $playingNow = playingNow();
        return ["zaria"=> $zaria,"playing"=> $playingNow, "kiniseis"=>getKiniseis(playingNow(), $zari1, $zari2)];
    }

    if(!array_key_exists($pouli, $moves)) {
        //invalid move;
        return ["error"=> "invalid move", "zaria"=> ["0"=> $zari1, "1"=> $zari2],"playing"=> $xrwma, "kiniseis"=>getKiniseis($xrwma, $zari1, $zari2)];
    }

    if(in_array($thesi, $moves[$pouli])) {
        $pouliThesi = pouliThesi($pouli);
        if($zari1 != $zari2) {
            if($xrwma == 'red') {
                $newZari1 = (($pouliThesi + $zari1) == $thesi) ? 0 : (int)$zari1;
                $newZari2 = (($pouliThesi + $zari2) == $thesi) ? 0 : (int)$zari2;
            } else {
                $newZari1 = (($pouliThesi - $zari1) == $thesi) ? 0 : (int)$zari1;
                $newZari2 = (($pouliThesi - $zari2) == $thesi) ? 0 : (int)$zari2;
            }    
        } else {
            $newZari1= 0;
            $newZari2 = (int)$zari2;
        }
            
        if(executeMove($pouli, $thesi, $pouliThesi, $xrwma)) {
            $boardStatusRes = updateBoardStatus($newZari1 == 0 ? 1 : 2);

            if($boardStatusRes == $xrwma) {
                if(getKiniseis($xrwma, $zari1, $zari2) == 1) {
                    setWinner($xrwma);
                    initBoard();
                    return $xrwma." won";
                }
                $newKiniseis = getKiniseis($xrwma, $newZari1, $newZari2);
                return ["playing"=> $xrwma, "zaria"=> ["0"=> $zari1, "1"=> $zari2] , "kiniseis"=> $newKiniseis];
            } else {
                //antipalos playing
                $playingNow = playingNow();
                $newZaria = getReroll();
                $newKiniseis = getKiniseis($playingNow, $newZaria["0"], $newZaria["1"]);
                if($newKiniseis == 1) {
                    initBoard();
                    return getWinner()." won";
                }    
                return ["playing"=> $playingNow, "zaria"=> $newZaria, "kiniseis" => $newKiniseis];
            }
        } else {
            return "F";
        }
    }

    return ["error"=> "invalid move", "zaria"=> ["0"=> $zari1, "1"=> $zari2],"playing"=> $xrwma, "kiniseis"=>getKiniseis($xrwma, $zari1, $zari2)];
}

function getKiniseis($xrwma, $zari1, $zari2) {
    global $mysqli;

    $sql = "SELECT * FROM board";
    if ($result = $mysqli->query($sql)) {
        $boardWhite = array();
        $boardRed = array();
        $aspraBlocked = array();
        $kokkinaBlocked = array();
        for ($i = 1; $i <= 24; $i++) {
            $boardRed[$i] = [];
            $boardWhite[$i] = [];
            $kokkinaBlocked[$i] = [];
            $aspraBlocked[$i] = [];   
        }

        while ($row = $result->fetch_assoc()) {
            if ($row["color"] == 'red') {
                $boardRed[$row["thesi"]][] = ["id" => $row["id"], "color" => $row["color"]];
                $kokkinaPoulia[$row["id"]] = ["thesi" => $row["thesi"], "blocked" => $row["blocked"]];
                $kokkinaBlocked[$row["thesi"]] = $row["blocked"];
            } else {
                $boardWhite[$row["thesi"]][] = ["id" => $row["id"], "color" => $row["color"]];
                $aspraPoulia[$row["id"]] =  ["thesi" => $row["thesi"], "blocked" => $row["blocked"]];
                $aspraBlocked[$row["thesi"]] = $row["blocked"];
            }
        }


        if($xrwma == "red") {
            return ypologismosKokkina($kokkinaPoulia, $kokkinaBlocked, $boardWhite, $boardRed, $zari1, $zari2);
        } else {
            return ypologismosAspra($aspraPoulia, $aspraBlocked, $boardRed, $boardWhite, $zari1, $zari2);
        }
    } else {
        echo "Error1: " . $mysqli->error();
    }

    return true;
}


function ypologismosKokkina($poulia, $blockedPoulia, $board, $boardRed, $zari1, $zari2) {
    $canGo = array();
    $zaria = array($zari1, $zari2);
    $flagMazematos = true;
    $won = true;
    foreach ($zaria as &$zari) {
        if($zari == 0) {
            continue;
        }
        foreach ($poulia as $key => $value) {
            if($value["thesi"]==0) {
                continue;
            }

            $won = false;

            if($value["thesi"] < 19) {
                $flagMazematos = false;
            }

            if($value["blocked"]) {
                continue;
            }

            $newValue = (int)$value["thesi"] + (int)$zari;
            if ($newValue > 24) {
                continue;
            }

            if ($blockedPoulia[$newValue]) {
                continue;
            }
            
            if (count($board[$newValue]) < 2) {
                $canGo[$key][] = $newValue;
            }
        }
    }

    if($won) {
        return $won;
    }

    if($flagMazematos) {
        $canGo = array();
        $max = 0;
        for ($i = 24; $i >= 19; $i--) {
            if (count($boardRed[$i]) > 0) {
                $max = $i;
            }
        }

        foreach ($zaria as &$zari) {
            if($zari == 0) {
                continue;
            }    
            foreach ($poulia as $key => $value) {    
                if($value["thesi"]==0) {
                    continue;
                }
                
                $newValue = (int)$value["thesi"] + (int)$zari;
                if ($value["blocked"]) {
                    continue;
                }

                if ($newValue == 25) {
                    $canGo[$key][] = 0;
                    continue;
                }

                if($newValue > 25 && $value["thesi"]==$max) {
                    $canGo[$key][] = 0;
                    continue;
                }

                if($newValue > 25) {
                    continue;
                }

                if ($blockedPoulia[$newValue]) {
                    continue;
                }
                
                if (count($board[$newValue]) < 2) {
                    $canGo[$key][] = $newValue;
                }
            }
        }    
    }

    return $canGo;
}

function ypologismosAspra($poulia, $blockedPoulia, $board, $boardWhite, $zari1, $zari2) {
    $canGo = array();
    $zaria = array($zari1, $zari2);
    $flagMazematos = true;
    $won = true;
    foreach ($zaria as &$zari) {
        if($zari == 0) {
            continue;
        }
        foreach ($poulia as $key => $value) {
            if($value["thesi"]==0) {
                continue;
            }

            $won = false;

            if($value["thesi"] > 6) {
                $flagMazematos = false;
            }

            if($value["blocked"]) {
                continue;
            }

            $newValue = (int)$value["thesi"] - (int)$zari;
            if ($newValue < 1) {
                continue;
            }

            if ($blockedPoulia[$newValue]) {
                continue;
            }
            if (count($board[$newValue]) < 2) {
                $canGo[$key][] = $newValue;
            }
        }
    }

    if($won) {
        return $won;
    }

    if($flagMazematos) {
        $canGo = array();
        $max = 0;
        for ($i = 1; $i <= 6; $i++) {
            if (count($boardWhite[$i]) > 0) {
                $max = $i;
            }
        }

        foreach ($zaria as &$zari) {
            if($zari == 0) {
                continue;
            }    
            foreach ($poulia as $key => $value) {    
                if($value["thesi"]==0) {
                    continue;
                }
                
                $newValue = (int)$value["thesi"] - (int)$zari;
                if ($value["blocked"]) {
                    continue;
                }

                if ($newValue == 0) {
                    $canGo[$key][] = 0;
                    continue;
                }

                if($newValue < 0 && $value["thesi"]==$max) {
                    $canGo[$key][] = 0;
                    continue;
                }

                if($newValue < 0) {
                    continue;
                }

                if ($blockedPoulia[$newValue]) {
                    continue;
                }
                
                if (count($board[$newValue]) < 2) {
                    $canGo[$key][] = $newValue;
                }
            }
        }
    }

    //$flagMazematos an einai gia mazema paramenei true
    //can go pou mporoun na pane
    return $canGo;
}

function getBoard() {
    global $mysqli;

    $sql = "SELECT * FROM board";
    if ($result = $mysqli->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $results[] = ["id" => $row["id"], "color" => $row["color"], "thesi" => $row["thesi"]];
        }
        return $results;
    } else {
        echo "Error1: " . $mysqli->error();
    }

    return true;
}
?>