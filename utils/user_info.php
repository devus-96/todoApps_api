<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';

function get_user_info () {
        //recupere les entetes
        $headers = getallheaders();
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = explode('Bearer', $authorizationHeader)[1];
        $provider = $headers['X-Custom-Header'];
        //decode le token
        $user_info = decodeJWT($token, $provider);

        if (is_string($user_info)) {
            header("HTTP/1.1 403 BAD REQUEST");
            //echo json_encode($token);
            exit();
        } else {
            if ($provider === 'github') {
                $user = new BD(["github_id" => $user_info['id']]);
                $response = $user->search('users', 'id', 'github_id');
                return ["id" => $response];
            } else if ($provider === 'google') {
                $user = new BD(["google_id" => $user_info['id']]);
                $response = $user->search('users', 'id', 'google_id');
                return ["id" => $response];
            }
            $array = json_decode(json_encode($token), true);
            return $array;
        }
}

?>