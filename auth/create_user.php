<?php 
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require $_SERVER['DOCUMENT_ROOT'] . '/action/db.php';
require $_SERVER['DOCUMENT_ROOT'] . '/action/jwt.php';
require $_SERVER['DOCUMENT_ROOT'] . '/DB/user.php';

/* facon de recuperer les donnees depuis le frontent <json_decode> Récupère une chaîne encodée 
JSON et la convertit en une valeur de PHP. et retourne Retourne la valeur encodée dans le paramètre json dans le type PHP approprié
<file_get_contents> 
*/
$data = json_decode(file_get_contents('php://input'), true);

// verification des cles obligatoires elle enverra une erreur 400 si les valeurs suivantes sont manquantes
if ($data["firstName"] !== "" && $data["email"] !== "" && $data["password"] !== "") {
    $encrpt = password_hash($data["password"], PASSWORD_DEFAULT);
    $data["password"] = $encrpt;
    try {
        $user = new Users($data);
        $user->insert('users');
        
        // generation du token
        $token = generateJWT($data["email"]);

        header("HTTP/1.1 200 OK");
        echo $token;
    } catch (PDOException $e) {
        header("HTTP/1.1 400 Bad request");
        echo $e -> getMessage();
        //le code 23505 correspond a la violation d'un identifiant unique en postgresql
        if ($e->getCode() === "23505") {
            header("HTTP/1.1 400 Bad request");
            echo "the email you just entered has already been assigned !!!";
        } else {
            echo $e->getMessage();
            echo "sorry, something went wrong !!!";
        }
    }
} else {
    header("HTTP/1.1 400 Bad request3");
    echo "it looks like you forgot to fill in a field !!!";
}
?>