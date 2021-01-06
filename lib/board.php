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

function getBoard()
{
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