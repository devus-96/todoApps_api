<?php 

require '../action/db.php';
require '../action/jwt.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


if (isset($_GET['code'])) {
    
    try {
        require_once "../action/auth_google.php";

        if (!empty($gUser)) {
            $smth = $pdo -> prepare("SELECT * FROM users WHERE email = :email");
            $smth->execute([':email' => $gUser['email']]);
            $checkResult = $smth->fetch(PDO::FETCH_ASSOC);

            if ($checkResult) {
                $pass = $pdo -> prepare("SELECT password FROM users WHERE email = :email");
                $pass->execute([':email' => $gUser['email']]);
                $check = $pass->fetch(PDO::FETCH_ASSOC);
        
                if ($check['password'] !== "") {
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