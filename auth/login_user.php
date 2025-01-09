<?php 

/*$_SERVER['DOCUMENT_ROOT'] Parce que required fonctionne avec les chemin réèl des fichier et pa avec leur position dans le serveur */

require $_SERVER['DOCUMENT_ROOT'] . '/action/jwt.php';
require $_SERVER['DOCUMENT_ROOT'] . '/DB/bdmanage.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data['email'] !== '' && $data['password'] !== "") {
    try {
        $user = new BD($data);
        $response = $user->select('users', '*', 'email');

        if ($response) {
            $getpassword = $user->select('users', 'password', 'email');
            $passworVerify = password_verify($data['password'], $getpassword['password']);

            $token = generateJWT($data["email"]);

            if ($passworVerify) {
                header('HTTP/1.1 200 OK');
                echo $token;
            } else {
                header('HTTP/1.1 401 Unauthorized');
                echo "the password you're just entered is wrong !!!";
            }
        } else {
            header('HTTP/1.1 404 Not Found');
            echo "i don't find this email, please verify email !!!!";
        }
    } catch (PDOException $e) {
        $e->getMessage();
    }
} else {
    header('HTTP/1.1 400 Bad request');
    echo "check fields";
}

?>