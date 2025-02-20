<?php
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';

class UserController {

    public function create () {
        $data = json_decode(file_get_contents('php://input'), true);

        // Vérifier si les données sont valides et contiennent les clés obligatoires
        $requiredKeys = ['firstname', 'lastname', 'email', 'password'];
        $missingKeys = array_diff($requiredKeys, array_keys($data));

        if (!empty($missingKeys)) {
            // Renvoyer une erreur 400 si des champs obligatoires sont manquants
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                'error' => 'Missing required fields',
                'missing_fields' => array_values($missingKeys)
            ]);
            return;
        }

        // Hacher le mot de passe
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            // Insérer l'utilisateur dans la base de données
            $user = new BD($data);
            $user->insert('users');
    
            // Générer un token JWT
            $data['token'] = generateJWT($data['email']);
    
            // Renvoyer une réponse JSON avec les données de l'utilisateur et le token
            header("HTTP/1.1 201 Created");
            header("Content-Type: application/json");
            echo json_encode([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'token' => $data['token']
            ]);
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            if ($e->getCode() === "23505") {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode([
                    'error' => 'Email already exists',
                    'message' => 'The email you just entered has already been assigned.'
                ]);
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode([
                    'error' => 'Database error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    public function get () {
        $data = json_decode(file_get_contents('php://input'), true);

        // Vérifier si les données sont valides et contiennent les clés obligatoires
        $requiredKeys = ['firstname', 'lastname', 'email', 'password'];
        $missingKeys = array_diff($requiredKeys, array_keys($data));

        if (!empty($missingKeys)) {
            // Renvoyer une erreur 400 si des champs obligatoires sont manquants
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                'error' => 'Missing required fields',
                'missing_fields' => array_values($missingKeys)
            ]);
            return;
        }

        try {
            $user = new BD($data);
            $response = $user->search('users', '*', 'email');

            if ($response) {
                $getpassword = $user->search('users', 'password', 'email');
                $passworVerify = password_verify($data['password'], $getpassword['password']);

                $response['token'] = generateJWT($data["email"]);

                if ($passworVerify) {
                    header('HTTP/1.1 200 OK');
                    echo json_encode([
                        'firstname' => $response['firstname'],
                        'lastname' => $response['lastname'],
                        'token' => $response['token']
                    ]);
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
    }

}

?>