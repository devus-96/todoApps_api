<?php 
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/task.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/required.php';

class TaskController {
    //?sort=priotity-high&status-cancel&team_id-454654
    public function get ($sort) {
        try { 
            $sorts = explode('&', $sort);
            $params = [];
            foreach($sorts as $params) {
                $attribute = explode('-', $sort);
                $params[$attribute[0]] = $attribute[1];
            }
            get_user_info();
            $tasks = new BD($params);
            $response = $tasks->get("tasks", "*");
            if ($response) {
                http_response(200, json_encode($response));
            } else {
                http_response(400, "no tasks found");
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function create ($projectId=null, $teamId=null) {
        //recuperer les donnees de ls taches 
        $data = json_decode(file_get_contents('php://input'), true);
        //recuperer puis decoder le token
        $array = get_user_info();
        // Vérifier si les données sont valides et contiennent les clés obligatoires
        required_attribute($data,['name', 'tags', 'priority', 'start_time', 'end_time', 'start_date', 'status']);
        try {
            $params['start_date'] = $data['start_date'];
            if ($teamId) {
                //get permission
                $role = new BD(["user_id" => $array['id'], "team_id" => $teamId]);
                $response = $role->get('roles', "role");
                // verifier si le user a les bonnes permissions
                if ($response === "administrator" || $response === "author") {
                    $params['team_id'] = $teamId;
                    if ($projectId) {
                        $params['project_id'] = $projectId;
                    }
                } else {
                    http_response(403, "you not have permission to make this action!");
                }
            } else {
                $params['user_id'] = $array['id'];
                if ($projectId) {
                    $params['project_id'] = $projectId;
                }
            }
            $task = new Task();
            $task->create_task($data, $params);
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    } 

    public function update ($id, $teamId=null) {
        try {
            //recuperer les donnees de ls taches 
            $data = json_decode(file_get_contents('php://input'), true);
            // recuperer les infos du users
            $array = get_user_info();
            if ($teamId) {
                $params = ["user_id"=>$array['id'], "team_id"=>$id, "task_id"=>$id];
                //update company
                $task = new Task();
                $task->update_task($data, $params);
            } else {
                $params = ['id' => $id];
                $task = new BD($data);
                $res = $task->update('tasks', $params);
                if ($res) {
                    http_response(201, "The task has been updated successfully", "Updated");
                } else {
                    http_response(500, "failed update calendar");
                }
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
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
                    http_response(403, "you not have permission to make this action!");
                }
            } 
            $task = new Task();
            $task->delete_task_cascade($id);
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }
}


?>