<?php 
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/task.php';
require $_SERVER['DOCUMENT_ROOT'] . '/controllers/calendarController.php';

class TaskController {
    public function get ($id) {
        try { 
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

    public function create ($dataId) {
        $data = json_decode(file_get_contents('php://input'), true);

        // Vérifier si les données sont valides et contiennent les clés obligatoires
        $requiredKeys = ['name', 'tags', 'priority', 'start_time', 'end_time', 'start_date', 'status'];
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
            $dataId['users_id'] ? $data['users_id'] = $dataId['users_id'] : null;
            $dataId['projectId'] ? $data['project_id'] = $dataId['projectId'] : null;
            // Insérer la tache dans la base de données
            $task = new BD($data);
            $response = $task->insert('task');

            if ($response) {
                //verifier si un calendier existe a cette date
                $calendar = new BD(['start_date' => $data['start_date']]);
                $response = $calendar->search('calendar', 'start_date', 'start_date');
                // si non : cree un nouveau calendier
                if (!$response) {
                    $calendar = new CalendarControllers();
                    $calendar->create(['start_date' => $data['start_date']]);
                }
                // server response
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

    public function update ($id) {
        // update la tache dans la base de données
        $data = json_decode(file_get_contents('php://input'), true); // recuperer les donnees contenus dans body
        try {
            $task = new BD($data);
            $res = $task->update('task', $id);
            if ($res) {
                header("HTTP/1.1 201 Updated");
                echo "The task has been updated successfully";
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete ($id) {
        try {
            $task = new BD($id);
            $response = $task->delete('task', $id['id']);
            if ($response) {
                header("HTTP/1.1 201 Deleted");
                echo "The task has been deleted successfully";
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo $e->getMessage();
        }
    }
}


?>