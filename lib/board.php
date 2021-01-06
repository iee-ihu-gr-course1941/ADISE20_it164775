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