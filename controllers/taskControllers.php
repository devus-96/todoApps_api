<?php 
//cette ligne est pour l'empecher
declare(strict_types = 1);

require $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/task.php';

class TaskController {
    public function get () {
        try { 
            $id = ["id" => $_GET['id']]; // recuperer l'id de la tache 
            $response = new BD($id);
            $task = $response->search("task", "*", "id");

            if ($task) {
                header('HTTP/1.1 200 OK');
                echo json_encode($task);
            } else {
                header('HTTP/1.1 401 Unauthorized');
                echo "task not found at this date !!!";
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function create () {
        $data = json_decode(file_get_contents('php://input'), true);

        // Vérifier si les données sont valides et contiennent les clés obligatoires
        $requiredKeys = ['name', 'creation_date', 'start_date'];
        $missingKeys = array_diff($requiredKeys, array_keys($data));

        if (!empty($missingKeys)) {
            // Renvoyer une erreur 400 si des champs obligatoires sont manquants
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                'error' => 'Missing required fields',
                'missing_fields' => array_values($missingKeys)
            ]);
            return;
        }
        try {
            // Insérer la tache dans la base de données
            $task = new BD($data);
            $response = $task->insert('task');

            if ($response) {
                header("HTTP/1.1 201 Created");
                echo "The task has been created successfully";
            } else {
                trigger_error("error", E_USER_ERROR);
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo $e->getMessage();
        }
    } 

    public function update () {
        // update la tache dans la base de données
        $data = json_decode(file_get_contents('php://input'), true); // recuperer les donnees contenus dans body
        $id = ["id" => $_GET['id']]; // recuperer l'id de la tache 
        try {
            $task = new BD($data);
            $res = $task->update('task', $id);
            if ($res) {
                header("HTTP/1.1 201 Updated");
                echo "The task has been updated successfully";
            } else {
                header("HTTP/1.1 404 Not Found");
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo $e->getMessage();
        }
        
    }

    public function delete () {
        $id = ["id" => $_GET['id']]; // recuperer l'id de la tache 
        try {
            $task = new BD($id);
            $response = $task->delete('task', $id);
            if ($response) {
                header("HTTP/1.1 201 Deleted");
                echo "The task has been deleted successfully";
            } else {
                header("HTTP/1.1 404 Not Found");
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo $e->getMessage();
        }
    }
}


?>