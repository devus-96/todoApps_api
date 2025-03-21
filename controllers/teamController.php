<?php 
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/team.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';

class TeamController {
    public function get () {
        try{
            $array = get_user_info();
            $team = new Team();
            $teams = $team->search_user_team($array['id']);
            if ($teams) {
                http_response(200, json_encode($teams));
            } else {
                http_response(500, "we can fetch teams, sorry!");
            }
        } catch(Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function search ($id) {
        try{
            //recuperer puis decoder le token
            get_user_info();
            // recuperer les details du projet
            $team = new BD(["id" => $id]);
            $res_teams = $team->get('teams', "*");
            // recuperer les taches du projet en cour
            $project = new BD(["team_id" => $id]);
            $res_projects = $project->search('projects', "*", "team_id");
            //envoyer les donnees au client
            if ($res_projects) {
                http_response(200, json_encode([
                    "teams" => $res_teams,
                    "project" => $res_projects
                ]));
            } else {
                http_response(500, "we can fetch teams, sorry!");
            }
        }
         catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function create ($companyId=null) {
        try {
            //recuperer les donnnees depuis le client
            $data = json_decode(file_get_contents('php://input'), true);
            //recuperer puis decoder le token
            $array = get_user_info();
            // Vérifier si les données sont valides et contiennent les clés obligatoires
            required_attribute($data, ['name']);
            $data['creator'] = $array['email'];
            //cree la team soit pour la company soit pour le user
            if ($companyId) {
                $params = ["company_id"=>$companyId, "user_id"=>$array["id"]];
                $team = new Team();
                $response_team = $team->create_company_team($data, $params);
            } else {
                $team = new BD($data);
                $response_team = $team->insert('teams', 'id');
                if ($response_team) {
                    http_response(200, $response_team);
                } else {
                    http_response(500, "sorry we can't create a team, sorry!");
                }
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function update ($id) {
        try {
            //recuperer les donnnees depuis le client
            $data = json_decode(file_get_contents('php://input'), true);
            //recuperer puis decoder le token
            $array = get_user_info();
            $params = ["user_id"=>$array['id'], "team_id"=>$id];
            //update company
            $teams = new Team();
            $teams->update_team($data, $params);
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function delete ($id) {
        try {
            $array = get_user_info();
            //update company
            $params = ["user_id"=>$array['id'], "team_id"=>$id];
            $teams = new Team();
            $teams->delete_team($params);
        } catch (Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }
}

?>