<?php 
declare(strict_types = 1);
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';

function required_attribute ($data, $requiredKeys) {
    $missingKeys = array_diff($requiredKeys, array_keys($data));

    foreach($requiredKeys as $value) {
        if ($data[$value] === '') {
            array_push($missingKeys, $value);
        }
    }
    if (!empty($missingKeys)) {
        // Renvoyer une erreur 400 si des champs obligatoires sont manquants
        http_response(400, json_encode(
            [
                'message' => "Missing required fields: array_values($missingKeys)"
            ]
        ));
        exit();
    } else {
        return true;
    }
}

?>