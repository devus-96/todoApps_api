<?php 

function get_user_info () {
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = explode('Bearer', $authorizationHeader)[1];
        $token = decodeJWT($token);

        if (is_string($token)) {
            header("HTTP/1.1 400 BAD REQUEST");
            echo json_encode($token);
            exit();
        } else {
            $array = json_decode(json_encode($token), true);
            return $array;
        }
}

?>