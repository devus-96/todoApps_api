<?php 
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';

class Team extends BD {
    public function search_team_company ($companyId) {
        $get = $this->pdo->prepare("SELECT * FROM teams 
            JOIN companyTeam ON teams.id = companyTeam.team_id
            JOIN companies ON companyTeam.company_id = companies.id
            JOIN role ON teams.id = role.team_id
            JOIN users ON role.user_id = users.id
            WHERE companyTeam.team_id = :companyId
        ");
        $get->execute([
            ":companyId" => $companyId,
        ]);
        $response = $get->fetchAll(PDO::FETCH_CLASS);
        return $response;
    }

    public function search_user_team ($userId) {
        $get = $this->pdo->prepare("SELECT * FROM teams 
            JOIN roles ON teams.id = roles.team_id
            JOIN users ON roles.user_id = users.id
            WHERE roles.user_id = :userId
        ");
        $get->execute([
            ":userId" => $userId,
        ]);
        $response = $get->fetchAll(PDO::FETCH_CLASS);
        return $response;
    }

    public function create_company_team ($data, $params) {
        $role = new BD(["user_id" => $params['user_id'], "company_id" => $params['company_id']]);
        $response = $role->get('usercompanies', "role");
        if ($response !== 'ownner' || $response = 'manager') {
            http_response(403, "you not have permission to make this action!");
        } else {
            // on cree la company
            $team = new BD($data);
            $response_team = $team->insert('teams', 'id');
            $params['team_id'] = $response_team;
            $param['role'] = "author";
            // insere le donnes de l'user qui a cree la company dans la base de donnee
            $teams = new BD($params);
            $response_companyTeam = $teams->insert('companyteam', 'id');
            if ($response_companyTeam) {
                http_response(200, "team has been created succesfully");
            } else {
                http_response(500, "sorry we can't create a team, sorry!");
            }
        }
    }

    public function update_team ($data, $params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        if ($response === "administrator" || $response === "author") {
            $task = new BD($data);
            $res = $task->update('teams', ['id' => $params['team_id']]);
            if ($res) {
                http_response(200, "team has been updated succesfully");
            } else {
                http_response(500, "sorry we can't update the team, sorry!");
            }
        } else {
            http_response(403, "you not have permission to make this action!");
        }
    }

    public function delete_team ($params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        if ($response === "author") {
            $user = new BD();
            $response = $user->delete('teams', ["id" => $params["team_id"]]);
            if ($response) {
                http_response(200, "team has been deleted succesfully");
            } else {
                http_response(500, "sorry we can't delete team, sorry!");
            }
        }
    }
} 

?>