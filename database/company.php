<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';

class Company extends BD {
    public function get_user_companies ($userId) {
        $get = $this->pdo->prepare("SELECT * FROM companies 
            JOIN userCompanies ON companies.id = userCompanies.company_id
            JOIN users ON userCompanies.user_id = users.id
            WHERE userCompanies.user_id = :userId
        ");
        $get->execute([
            ":userId" => $userId,
        ]);
        $response = $get->fetchAll(PDO::FETCH_CLASS);
        return $response;
    }

    public function create_company($data, $params) {
        // on cree la company
        $company = new BD($data);
        $response_company = $company->insert('companies', 'id');
        $params['company_id'] = $response_company;
        $param['role'] = "author";
        // insere le donnes de l'user qui a cree la company dans la base de donnee
        $company = new BD($params);
        $response_usercompanies = $company->insert('usercompanies', 'id');
        if ($response_usercompanies) {
            http_response(200, "company has been created succesfully");
        } else {
            http_response(500, "sorry we can't create a company, sorry!");
        }
    }

    public function update_company($data, $params) {
        $role = new BD(["user_id" => $params['id'], "company_id" => $params['company_id']]);
        $response = $role->get('usercompanies', "role");
        if ($response !== 'ownner') {
            http_response(403, "you not have permission to make this action!");
        } else {
            $task = new BD($data);
            $res = $task->update('companies', ['id' => $params['company_id']]);
            if ($res) {
                http_response(200, "company has been updated succesfully");
            } else {
                http_response(500, "sorry we can't update a company, sorry!");
            }
        }
    }

    public function delete_company($params) {
        $role = new BD(["user_id" => $params['id'], "company_id" => $params['company_id']]);
        $response = $role->get('usercompanies', "role");
        if ($response !== 'ownner') {
            http_response(403, "you not have permission to make this action!");
        } else {
            $user = new BD();
            $response = $user->delete('companies', ["id" => $params["company_id"]]);
            if ($response) {
                http_response(200, "company has been deleted succesfully");
            } else {
                http_response(500, "sorry we can't delete a company, sorry!");
            }
        }
    }
}

?>