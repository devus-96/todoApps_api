<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';

class AUTH extends BD {
    function authProvider ($data) {
        try {
            if (!empty($data)) {
                $user = new BD($data);
                $checkEmail = $user->search('users', '*', 'email');
                if ($checkEmail) {
                        header("HTTP/1.1 200 OK");
                } else {
                    $stmg = $this->pdo->prepare("INSERT INTO users (firstname, lastname, email, provider) VALUES (:firstName, :lastName, :email, :provider)");
                    //...executer par la fonction execute() de la valeur renvoye par prepare()
                    $stmg->execute([
                        ":firstName" => $data["firstname"],
                        ":lastName" => $data["lastname"],
                        ":email" => $data["email"],
                        ":provider" => $data["provider"]
                    ]);
                    header("HTTP/1.1 200 OK");
                }
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 400 Bad request1");
            echo json_encode($e);
        }
    }
}

?>