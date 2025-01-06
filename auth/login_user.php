<?php 

/*$_SERVER['DOCUMENT_ROOT'] Parce que required fonctionne avec les chemin réèl des fichier et pa avec leur position dans le serveur */

require $_SERVER['DOCUMENT_ROOT'] . '/action/db.php';
require $_SERVER['DOCUMENT_ROOT'] . '/action/jwt.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data['email'] !== '' && $data['password'] !== "") {
    try {
        $smth = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $status = $smth->execute([":email" => $data['email']]);
        $response = $smth->fetch(PDO::FETCH_ASSOC);

        if ($response) {
            $getpassword = $pdo->prepare('SELECT password FROM users WHERE email = :email');
            $getpassword->execute([":email" => $data['email']]);
            $resPassword = $getpassword->fetch(PDO::FETCH_ASSOC);

            $passworVerify = password_verify($data['password'], $resPassword['password']);

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