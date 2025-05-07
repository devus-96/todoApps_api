<?php
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';

class UserController {

    public function create () {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            // Vérifier si les données sont valides et contiennent les clés obligatoires
            $requiredKeys = ['firstname', 'lastname', 'email', 'password'];
            $missingKeys = array_diff($requiredKeys, array_keys($data));
            if (!empty($missingKeys)) {
                // Renvoyer une erreur 400 si des champs obligatoires sont manquants
                http_response(400, json_encode(['error' => 'Missing required fields', 'missing_fields' => array_values($missingKeys)]));
                exit();
            }
            // Hacher le mot de passe
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT, ["cost" => 14]);
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
                http_response(201, json_encode([
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    'token' => $token
                ]));
            } else {
                trigger_error("something went wrong", E_USER_WARNING);
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            if ($e->getCode() === "23505") {
                http_response(400, json_encode(['message' => 'Email already exists',]));
            } else {
                http_response(500, json_encode(["message" => "Database error:".$e->getMessage()]));
            }
        }
    }

    public function get () {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Vérifier si les données sont valides et contiennent les clés obligatoires
            $requiredKeys = ['email', 'password'];
            $missingKeys = array_diff($requiredKeys, array_keys($data));

            if (!empty($missingKeys)) {
                // Renvoyer une erreur 400 si des champs obligatoires sont manquants
                http_response(400, json_encode(['error' => 'Missing required fields', 'missing_fields' => array_values($missingKeys)]));
                exit();
            }
            $user = new BD($data);
            $response = $user->search('users', 'provider', 'email');
            if ($response['provider'] !== null) {
                http_response(403, [
                    "message" => "Please log in with Google, as you did when you first logged in."
                ]);
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
                        http_response(201, json_encode([
                            'firstname' => $response['firstname'],
                            'lastname' => $response['lastname'],
                            'token' => $token
                        ]));
                    } else {
                        http_response(403, json_encode([
                            "message" => "the password you're just entered is wrong !!!"
                        ]));
                    }
                } else {
                    http_response(403, json_encode([
                        "message" => "i don't find this email, please verify email !!!"
                    ]));
                }
            }
        } catch (PDOException $e) {
            http_response(500, [
                "message" => "Database error:".$e->getMessage()
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
                http_response(201, json_encode(["message" => 'user datas has been updated']), 'Updated');
            } else {
                http_response(500, json_encode(["message" => 'failed to update user datas']));
            }
        } catch (PDOException $e) {
            http_response(500, [
                "message" => "Database error:".$e->getMessage()
            ]);
        }
    }

    public function delete () {
        try {
            $array = get_user_info();
            $params =  ['id' => $array['user']['id']];
            $user = new BD();
            $response = $user->delete('users', $params);
            if ($response) {
                http_response(201, json_encode(["message" => 'user datas has been deleted']), 'Deleted');
            } else {
                http_response(500, json_encode(["message" => 'failed to delete user datas']));
            }
        } catch (PDOException $e) {
            http_response(500, [
                "message" => "Database error:".$e->getMessage()
            ]);
        }
    }

}

?>