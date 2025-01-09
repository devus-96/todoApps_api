<?php 

require '../action/db.php';
require $_SERVER['DOCUMENT_ROOT'] . '/action/jwt.php';
require $_SERVER['DOCUMENT_ROOT'] . '/DB/user.php';

// résoudre les problème de cors ...

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


if (isset($_GET['code'])) { // $_GET recupère les paramètres envoyés via URL
    
    try {
        require_once "../action/auth_google.php";

        if (!empty($gUser)) {
            $user = new BD($gUser);
            $checkEmail = $user->select('users', '*', 'email');

            if ($checkEmail) {
                $checkPassword = $user->select('users', 'password', 'email');
        
                if ($checkPassword['password'] !== "") {
                    header("HTTP/1.1 400 Bad request");
                    echo "Please register with your email, as you authenticated yourself the first time this way !!!";
                } else {
                    $token = generateJWT($data["email"]);
                    header("HTTP/1.1 200 OK");
                }
            } else {
                echo "hello2";
                $stmg = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (:firstName, :lastName, :email, :password, :role)");
                //...executer par la fonction execute() de la valeur renvoye par prepare()
                $stmg->execute([
                    ":firstName" => $gUser["firstname"],
                    ":lastName" => $gUser["lastname"],
                    ":email" => $gUser["email"],
                    ":password" => '',
                    "role" => 'administrator',
                ]);
    
                $token = generateJWT($data["email"]);
                header("HTTP/1.1 200 OK");
            }
        }

    } catch (Exception $e) {
        header("HTTP/1.1 400 Bad request1");
        echo $checkResult;
        echo json_encode($gUser);
    }
    
}

?>