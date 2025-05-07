<?php 
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/task.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/required.php';

class TaskController {
    public function get_role ($userId, $teamId) {
        $role = new BD(["user_id" => $userId, "team_id" => $teamId]);
        $response = $role->get('roles', "role");
        $role_response = json_decode(json_encode($response), true);
        return $role_response;
    }
    public function get ($teamId=null) {
        try { 
            //recuperer puis decoder le token
            $array = get_user_info();
            if ($teamId) {
                $role = $this->get_role($array['id'], $teamId);
                if ($role!=='author'||$role!=='administrator'||$role!=="menber") {
                    http_response(403,json_encode([
                        "message" => "you not have permission to make this action!", 
                    ]));
                } else {
                    $tasks = new BD(["team_id" => $teamId]);
                    $response = $tasks->get("tasks", "*");
                    if ($response) {
                        http_response(200, json_encode($response));
                    } else {
                        http_response(500, "something went wrong");
                    }
                }
            } else {    
                $tasks = new BD(["user_id" => $array['id']]);
                $response = $tasks->get("tasks", "*");
                if ($response) {
                    http_response(200, json_encode($response));
                } else {
                    http_response(500, "something went wrong");
                }
            }
        } catch (PDOException $e) {
            http_response(500, json_encode([
                "message" => "something went wrong",
                "Database error" => $e->getMessage()
            ]));
        }
    }

    public function create ($teamId=null, $projectId=null) {
        //recuperer les donnees de ls taches 
        $data = json_decode(file_get_contents('php://input'), true);
        //recuperer puis decoder le token
        $array = get_user_info();
        // Vérifier si les données sont valides et contiennent les clés obligatoires
        required_attribute($data,['name', 'priority', 'start_date', 'state']);
        try {
            if ($teamId) {
                //get permission
                $role = $this->get_role($array['id'], $teamId);
                if ($role[0]["role"] === "administrator" || $role[0]["role"] === "author") {
                    $data['team_id'] = $teamId;
                    if ($projectId) {
                        $data['project_id'] = $projectId;
                    }
                } else {
                    http_response(403,json_encode([
                        "message" => "you not have permission to make this action!", 
                    ]));
                    exit();
                }
            } else {
                $data['user_id'] = $array['id'];
                if ($projectId) {
                    $data['project_id'] = $projectId;
                }
            }
            $task = new Task();
            $task->create_task($data);
        } catch (PDOException $e) {
            http_response(500, json_encode([
                "message" => "something went wrong",
                "Database error" => $e->getMessage()
            ]));
        }
    } 

    public function update ($id, $teamId=null) {
        try {
            //recuperer les donnees de ls taches 
            $data = json_decode(file_get_contents('php://input'), true);
            // recuperer les infos du users
            $array = get_user_info();
            if ($teamId) {
                $params = ["user_id"=>$array['id'], "team_id"=>$teamId, "task_id"=>$id];
                //update company
                $task = new Task();
                $task->update_task($data, $params);
            } else {
                $params = ['id' => $id, 'user_id' => $array['id']];
                $task = new BD($data);
                $res = $task->update('tasks', $params);
                if ($res) {
                    http_response(201, "The task has been updated successfully", "Updated");
                } else {
                    http_response(500, "failed update calendar");
                }
            }
        } catch (PDOException $e) {
            http_response(500, json_encode([
                "message" => "something went wrong",
                "Database error" => $e->getMessage()
            ]));
        }
    }

    public function delete ($id, $teamId=null) {
        $array = get_user_info();
        try {
            if ($teamId) {
                $role = new BD(["user_id" => $array['user_id'], "team_id" => $teamId]);
                $response = $role->get('roles', "role");

                if ($response === "administrator" || $response === "author") {
                    $task = new Task();
                    $task->delete_task_cascade($id);
                } else {
                    http_response(403,json_encode([
                        "message" => "you not have permission to make this action!", 
                    ]));
                }
            } 
            $task = new Task();
            $task->delete_task_cascade($id);
        } catch (PDOException $e) {
            http_response(500, json_encode([
                "message" => "something went wrong",
                "Database error" => $e->getMessage()
            ]));
        }
    }
}


?>