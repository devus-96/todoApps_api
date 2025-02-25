<?php
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';

class UserController {

    public function create () {
        $data = json_decode(file_get_contents('php://input'), true);
        echo $data;
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
            $response = $user->insert('users', 'id');

            if ($response) {
                //$getid = $user->search('users', 'id', 'email');
                $getprovider = $user->search('users', 'provider', 'email');
                // Générer un token JWT
                $token = generateJWT([
                    'id' => $response['id'], 
                    'name' => $data['firstname'],
                    'email' => $data['email'],
                    'provider' => $data['provider'],
                    'password' => $getprovider
                ]);
                // Renvoyer une réponse JSON avec les données de l'utilisateur et le token
                header("HTTP/1.1 201 Created");
                header("Content-Type: application/json");
                echo json_encode([
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    'token' => $token
                ]);
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
                trigger_error("something went wrong", E_USER_WARNING);
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            if ($e->getCode() === "23505") {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode([
                    'error' => 'Email already exists',
                    'message' => 'The email you just entered has already been assigned.'
                ]);
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
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
        $requiredKeys = ['email', 'password'];
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
            $response = $user->search('users', 'provider', 'email');
            if ($response['provider'] !== null) {
                header("HTTP/1.1 401 Unauthorized");
                echo "Please log in with Google, as you did when you first logged in.";
            } else  {
                $response = $user->search('users', '*', 'email');
                if ($response) {
                    $getpassword = $user->search('users', 'password', 'email');
                    $passworVerify = password_verify($data['password'], $getpassword['password']);
                    if ($passworVerify) {
                        $getid = $user->search('users', 'id', 'email');
                        $getprovider = $user->search('users', 'provider', 'email');
                        $getfirstname = $user->search('users', 'firstname', 'email');
                        $token = generateJWT([
                            'id' => $getid['id'], 
                            'name' => $getfirstname,
                            'email' => $data['email'],
                            'provider' => $getprovider,
                            'password' => $data['password']
                        ]);
                        header('HTTP/1.1 200 OK');
                        echo json_encode([
                            'firstname' => $response['firstname'],
                            'lastname' => $response['lastname'],
                            'token' => $token
                        ]);
                    } else {
                        header('HTTP/1.1 401 Unauthorized');
                        echo "the password you're just entered is wrong !!!";
                    }
                } else {
                    header('HTTP/1.1 404 Not Found');
                    echo "i don't find this email, please verify email !!!!";
                }
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update () {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $array = get_user_info();
            // Token valide, modifier les attribut du users
            $params = ['id' => $array['user']['id']];
            $user = new BD($data);
            $response = $user->update('users', $params);
            if ($response) {
                // Renvoyer une réponse JSON avec les données de l'utilisateur et le token
                header("HTTP/1.1 201 Uddated");
                echo 'datas has been updated';
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
        
    }

    public function delete () {
        try {
            $array = get_user_info();
            $params =  ['id' => $array['user']['id']];
            $user = new BD('');
            $user->delete('users', $params);
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

}

?>