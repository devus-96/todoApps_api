<?php 
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';

class Project extends BD{

    public function create_team_project ($data, $params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        $role_response = json_decode(json_encode($response), true);
        if ($role_response[0]["role"] === "administrator" || $role_response[0]["role"] === "author") {
            $project = new BD($data);
            $response = $project->insert('projects', 'id');
            if ($response) {
                http_response(200,json_encode([
                    "message" => "The project has been created successfully", 
                    "data" => $response
                ]));
            } else {
                http_response(500,json_encode([
                    "message" => "something went wrong !!", 
                ]));
            }
        } else {
            http_response(403,json_encode([
                "message" => "you not have permission to make this action!", 
            ]));
        }
    }

    public function update_team_project ($data, $params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        $role_response = json_decode(json_encode($response), true);
        if ($role_response[0]['role'] === 'administrator' || $role_response[0]['role'] === 'author') {
            //update project info
            $task = new BD($data);
            $res = $task->update('projects', ["id"=>$params['id']]);
            if ($res) {
                http_response(200,json_encode([
                    "message" => "project has been updated succesfully", 
                ]));
            } else {
                http_response(500,json_encode([
                    "message" => "failed update project", 
                ]));
            }
        } else {
            http_response(403,json_encode([
                "message" => "you not have permission to make this action!", 
            ]));
        }
    }

    public function delete_team_project ($params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        if ($response[0]['role'] === 'administrator') {
            $user = new BD();
            $response = $user->delete('projects', ["id" => $params["id"]]);
            if ($response) {
                http_response(200,json_encode([
                    "message" => "project has been deleted succesfully", 
                ]));
            } else {
                http_response(500,json_encode([
                    "message" => "sorry we can't delete project, sorry", 
                ]));
            }
        } else {
            http_response(403,json_encode([
                "message" => "you not have permission to make this action!", 
            ]));
        }
    }
}

?>