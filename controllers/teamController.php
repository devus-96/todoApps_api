<?php 
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';

class TeamController {
    public function search ($id = null) {
        //recuperer puis decoder le token
        $array = get_user_info();
            // recuperer les details du projet
            $team = new BD(["id" => $id, "user_id" => $array['id']]);
            $res_projects = $team->get('teams', "*");
            // recuperer les taches du projet en cour
            $project = new BD(["team_id" => $id]);
            $res_taks = $project->search('tasks', "*", "project_id");
            //envoyer les donnees au client
            if ($res_taks && $res_projects) {
                header('HTTP/1.1 200 OK');
                echo json_encode([
                    "project" => $res_projects,
                    "tasks" => $res_taks
                ]);
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
                echo "";
            }
        try {
            if ($id) {

            } else {
                $teams = new BD(['id' => $array['user']['id']]);
                $response = $teams->get('teams', '*');
            }
            if ($response) {
                header('HTTP/1.1 200 ok');
                echo json_encode($response);
            } else {
                header('HTTP/1.1 500 SERVER ERROR');
                echo "something went wrong";
            }
        } catch (PDOException $e) {
            header('HTTP/1.1 500 SERVER ERROR');
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create () {
        //recuperer les donnnees depuis le client
        $data = json_decode(file_get_contents('php://input'), true);

        //recuperer puis decoder le token
        $array = get_user_info();

        // Vérifier si les données sont valides et contiennent les clés obligatoires
        $requiredKeys = ['name'];
        $missingKeys = array_diff($requiredKeys, array_keys($data));

        if (!empty($missingKeys)) {
            // Renvoyer une erreur 400 si des champs obligatoires sont manquants
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                'error' => 'Missing required fields',
                'missing_fields' => array_values($missingKeys)
            ]);
            exit();
        }

        try {
            $data['creator'] = $array['email'];
            $team = new BD($data);
            $response_team = $team->insert('teams', 'id');

            if ($response_team) {
                $data = [
                    'role' => 'administrator',
                    'user_id' => $array['user']['id'],
                    'team_id' => $response_team
                ];
                $role = new BD($data);
                $response_roles = $role->insert('roles', 'id');
                if ($response_roles) {
                    header('HTTP/1.1 201 Created');
                    echo 'created team successfuly';
                } else {
                    header('HTTP/1.1 500 SERVER ERROR');
                    echo "failed to create team";
                }
            } else {
                header('HTTP/1.1 500 SERVER ERROR');
                echo "failed to create team";
            }

        } catch (PDOException $e) {
            header('HTTP/1.1 500 SERVER ERROR');
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update ($id, $user_id=null) {
        //recuperer les donnnees depuis le client
        $data = json_decode(file_get_contents('php://input'), true);

        //recuperer puis decoder le token
        $array = get_user_info();
        try {
            $role = new BD(['user_id' => $array['user']['id']]);
            $role->search('roles', 'role', 'user_id');
            if ($role['role'] === "administrator") {
                $params = ['id' => $id];
                $team = new BD($data);
                $response = $team->update('teams', $params);
                if ($response) {
                    header('HTTP/1.1 201 Created');
                    echo 'Updated team successfuly';
                } else {
                    header('HTTP/1.1 500 SERVER ERROR');
                    echo "failed to update team";
                }
            } else {
                header('HTTP/1.1 200 BAD RESQUEST');
                echo 'make sure you have editing rights on the team';
            }
        } catch (PDOException $e) {
            header('HTTP/1.1 500 SERVER ERROR');
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete ($id) {
        //recuperer puis decoder le token
        $array = get_user_info();
        try {
            $role = new BD(['user_id' => $array['user']['id']]);
            $role->search('roles', 'role', 'user_id');
            if ($role['role'] === "administrator") {
                $params = ['id' => $id];
                $team = new BD('');
                $response = $team->update('teams', $params);
                if ($response) {
                    header('HTTP/1.1 201 Created');
                    echo 'Deleted team successfuly';
                } else {
                    header('HTTP/1.1 500 SERVER ERROR');
                    echo "failed to delete team";
                }
            } else {
                header('HTTP/1.1 200 BAD RESQUEST');
                echo 'make sure that you have deletion rights on the team';
            }
        } catch (PDOException $e) {
            header('HTTP/1.1 500 SERVER ERROR');
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }
}

?>