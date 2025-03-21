<?php 

function http_response ($status, $response, $action = "Creating") {
    switch ($status) {
        case 200:
            header('HTTP/1.1 200 OK');
            echo $response;
            break;
        case 201: 
            header("HTTP/1.1 201 $action");
            echo $response;
            break;
        case 400: 
            header("HTTP/1.1 400 Bad Request");
            echo $response;
            break;
        case 403: 
            header("HTTP/1.1 403 Unauthorized");
            echo $response;
            break;
        case 500: 
            header("HTTP/1.1 500 SERVER ERROR");
            echo $response;
            break;
    }
}

?>