<?php
    require_once "lib/dbconnect.php";
    require_once "lib/auth.php";
    require_once "lib/game.php";
    require_once "lib/board.php";

    $method = $_SERVER['REQUEST_METHOD'];
    $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    $input = json_decode(file_get_contents('php://input'),true);

    switch ($r=array_shift($request)) {
        case 'login':
                handleLogin($method, $input);
                break;
        case 'logout': 
                handleLogout($method, $input);
                break;
        case 'start': 
                handleStart($method);
                break;
        case 'move':
                handleMove($method, $input);
                break;        
        case 'board':
                header('Content-Type: application/json');
                print json_encode(["board"=> getBoard()]);
                break;
        default:  
                header("HTTP/1.1 404 Not Found");
                exit;
    }

    function handleLogin($method, $input) {
            if($method=='POST') {
                login($input["username"],$input["password"]);
            } else {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
    }

    function handleLogout($method, $input) {
        if($method == 'POST') {
                logout($input["username"]);
                header("HTTP/1.1 200 OK");
                exit;
        } else {
                header("HTTP/1.1 404 Not Found");
                exit;
        }
    }

    function handleStart($method) {
        if($method == 'POST') {
                if(!gameStarted()) {
                        if(isPlayable()) {
                                initBoard();
				board();
                        } else {
                                header("HTTP/1.1 200 OK");
                                header('Content-Type: application/json');
                                print json_encode(['message'=> 'Not enough players. Please Wait']);
                        }        

                } else {
                        $playingNow = playingNow();
                        $roll = currentRoll();
                        header('Content-Type: application/json');
                        print json_encode(["message"=> "Game started", "paizei" => $playingNow, "zaria" => $roll, "board" => getBoard(), "kiniseis" => getKiniseis($playingNow, $roll["0"], $roll["1"])]);        
                }
        } else {
                header("HTTP/1.1 404 Not Found");
                exit;
        }
    }

function board() {
        setStarted(1);
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');
        $playingNow = playingNow();
        $roll = getReroll();
        print json_encode(["paizei" => $playingNow, "zaria" => $roll, "board" => getBoard(), "kiniseis" => getKiniseis($playingNow, $roll["0"], $roll["1"])]);        
    }
?>