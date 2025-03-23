<?php 
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/project.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/required.php';

class ProjectController {
    public function search ($id, $teamId) {
        try {
            // recuperer les details du projet
            $project = new BD(["id" => $id, "team_id" => $teamId]);
            $res_projects = $project->get('projects', "*");
            // recuperer les taches du projet en cour
            $tasks = new BD(["project_id" => $id]);
            $res_taks = $tasks->search('tasks', "*", "project_id");
            //envoyer les donnees au client
            if ($res_taks && $res_projects) {
                http_response(200, json_encode(["project" => $res_projects,"tasks" => $res_taks]));
            } else {
                http_response(500, "something went wrong !!");
            }
        } catch (Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function get ($id=null) {
        try {
            //recuperer puis decoder le token
            $array = get_user_info();
            if ($id) {
                // recuperer les details du projet
                $project = new BD(["id" => $id, "user_id" => $array['id']]);
                $res_projects = $project->get('projects', "*");
                // recuperer les taches du projet en cour
                $tasks = new BD(["project_id" => $id]);
                $res_taks = $tasks->search('tasks', "*", "project_id");
                //envoyer les donnees au client
                if ($res_taks && $res_projects) {
                    http_response(200, json_encode(["project" => $res_projects,"tasks" => $res_taks]));
                } else {
                    http_response(500, "something went wrong !!");
                }
            } else {
                $project = new BD(['id' => $array['id']]);
                $response = $project->get('project', '*');
                if ($response) {
                    http_response(200, $response);
                } else {
                    http_response(500, "something went wrong !!");
                }
            }
        } catch (Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function create ($teamId=null) {
        try {
            //recuperer les donnnees depuis le client
            $data = json_decode(file_get_contents('php://input'), true);
            //recuperer puis decoder le token
            $array = get_user_info();
            // Vérifier si les données sont valides et contiennent les clés obligatoires
            required_attribute($data, ['name', 'tags', 'priority', 'start_time', 'end_time', 'start_date', 'status']);
            $data['creator'] = $array['email'];
            if ($teamId) {
                $data['team_id'] = $teamId;
                $params = ["team_id"=>$teamId, "user_id"=>$array['id']];
                $project = new Project();
                $project->create_team_project($data, $params);
            } else {
                $data['user_id'] = $array['id'];
                $project = new BD($data);
                $response = $project->insert('tasks', 'id');
                if ($response) {
                    http_response(200, $response);
                } else {
                    http_response(500, "something went wrong !!");
                }
            }
        } catch (Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function update ($id, $teamId=null) {
        try {
            //recuperer les donnnees depuis le client
            $data = json_decode(file_get_contents('php://input'), true);
            //recuperer puis decoder le token
            $array = get_user_info();
            if ($teamId) {
                $params = ['id' => $id, 'team_id' => $teamId, "user_id"=>$array['id']];
                //update project info
                $project = new Project();
                $project->create_team_project($data, $params);
            } else {
                $params = ['id' => $id, 'user_id' => $array['id']];
                //update project info
                $task = new BD($data);
                $res = $task->update('project', $params);
                if ($res) {
                    http_response(201, "The project has been updated successfully", "Updated");
                } else {
                    http_response(500, "failed update project");
                }
            }
        } catch(Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }

    }

    public function delete ($id, $teamId=null) {
        try {
            //recuperer puis decoder le token
            $array = get_user_info();
            if ($teamId) {
                $params = ['id' => $id, 'team_id' => $teamId, "user_id"=>$array['id']];
                //update project info
                $project = new Project();
                $project->delete_team_project($params);
            } else {
                $user = new BD();
                $response = $user->delete('projects', ["id" => $id]);
                if ($response) {
                    http_response(200, "team has been deleted succesfully");
                } else {
                    http_response(500, "sorry we can't delete team, sorry!");
                }
            }
        } catch (Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }
}

?>