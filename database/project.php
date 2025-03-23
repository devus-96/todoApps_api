<?php 
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';

class Project extends BD{
    public function create_team_project ($data, $params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        if ($response === "administrator" || $response === "author") {
            $project = new BD($data);
            $response = $project->insert('projects', 'id');
            if ($response) {
                http_response(200, "The project has been created successfully");
            } else {
                http_response(500, "something went wrong !!");
            }
        } else {
            http_response(403, "you not have permission to make this action!");
        }
    }

    public function update_team_project ($data, $params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        if ($response === 'administrator' || $response === 'ownner') {
            //update project info
            $task = new BD($data);
            $res = $task->update('projects', ["id"=>$params['id']]);
            if ($res) {
                http_response(200, "project has been updated succesfully");
            } else {
                http_response(500, "failed update project");
            }
        }
    }

    public function delete_team_project ($params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        if ($response === 'ownner') {
            $user = new BD();
            $response = $user->delete('projects', ["id" => $params["id"]]);
            if ($response) {
                http_response(200, "project has been deleted succesfully");
            } else {
                http_response(500, "sorry we can't delete project, sorry!");
            }
        }
    }
}

?>