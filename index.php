<?php
    require_once "dbconnect.php";
    require_once "auth.php";

    $method = $_SERVER['REQUEST_METHOD'];
    $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    $input = json_decode(file_get_contents('php://input'),true);

    switch ($r=array_shift($request)) {
        case 'login':
                handleLogin($method, $input);
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
  
?>