<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';

// résoudre les problème de cors ...

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


if (isset($_GET['code'])) { // $_GET recupère les paramètres envoyés via URL
    try {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/models/google.php';

        if (!empty($gUser)) {
            $user = new BD($gUser);
            $checkEmail = $user->search('users', '*', 'email');
            if ($checkEmail) {
                $checkProvider = $user->search('users', 'providre', 'email');
        
                if ($checkProvider['provider'] === "") {
                    header("HTTP/1.1 400 Bad request");
                    echo "Please register with your email, as you authenticated yourself the first time this way !!!";
                } else {
                    $token = generateJWT([
                        'email' => $gUser["email"], 
                        'firstname' => $gUser["firstname"],
                        'provider' => $gUser['provider'],
                        'id' => $gUser['id'],
                    ]);
                    header("HTTP/1.1 200 OK");
                }
            } else {
                $stmg = $pdo->prepare("INSERT INTO users (firstname, lastname, email) VALUES (:firstName, :lastName, :email)");
                //...executer par la fonction execute() de la valeur renvoye par prepare()
                $stmg->execute([
                    ":firstName" => $gUser["firstname"],
                    ":lastName" => $gUser["lastname"],
                    ":email" => $gUser["email"],
                ]);
    
                $token = generateJWT(
                    [
                        'email' => $gUser["email"], 
                        'firstname' => $gUser["firstname"],
                        'provider' => $gUser['provider'],
                        'id' => $gUser['id'],
                    ]
                );
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