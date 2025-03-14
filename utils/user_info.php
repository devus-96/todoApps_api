<?php 

function get_user_info () {
        //recupere les entetes
        $headers = getallheaders();
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = explode('Bearer', $authorizationHeader)[1];
        $provider = $headers['X-Custom-Header'];
        //decode le token
        $token = decodeJWT($token, $provider);
        

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