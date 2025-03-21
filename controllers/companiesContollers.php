<?php 
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/company.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/required.php';

class CompanyController {
    public function search ($id) {
        try {
            $company = new BD(["id" => $id]);
            $response = $company->search('companies', "*", 'id');
            if ($response) {
                http_response(200, json_encode($response));
            } else {
                http_response(500, "we can fetch company, sorry!");
            }
        } catch(Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }
    /**
     * ici on doit recuperer tout les companies dans lequelle le user est present
     */
    public function get () {
        try {
            $array = get_user_info();
            $company = new Company();
            $companies = $company->get_user_companies($array['id']);
            if ($companies) {
                http_response(200, json_encode($companies));
            } else {
                http_response(500, "we can fetch company, sorry!");
            }
        } catch(Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function create () {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $array = get_user_info();
            // Vérifier si les données sont valides et contiennent les clés obligatoires
            required_attribute($data, ['name']);
            //create new company
            $data['author'] =  $array['email'];
            $company = new Company();
            $company->create_company($data, ["user_id" => $array['id']]);
        } catch(Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function update ($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $array = get_user_info();
            //update company
            $company = new Company();
            $company->update_company($data, ["user_id" => $array['id'], "company_id" => $id]);
        } catch (Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function delete ($id) {
        try {
            $array = get_user_info();
            //update company
            $company = new Company();
            $company->delete_company(["user_id" => $array['id'], "company_id" => $id]);
        } catch (Exception $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }
}

?>