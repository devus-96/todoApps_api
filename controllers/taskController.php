<?php 
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/calendarController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/scheduleController.php';

class TaskController {
    public function get ($priority, $status) {
        $params = ['priority' => $priority, 'status' => $status];
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = explode('Bearer', $authorizationHeader)[1];
        $response = decodeJWT($token);
        $array = json_decode(json_encode($response), true);
        try { 
            $params['user_id'] = $array['user']['id'];
            $tasks = new BD($params);
            $response = $tasks->get("tasks", "*");
            echo json_encode($response);
            if ($response) {
                header('HTTP/1.1 200 OK');
                echo json_encode($response);
            } else {
                header('HTTP/1.1 401 Unauthorized');
                echo "task not found at this date !!!";
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function create ($project = null) {
        //recuperer les donnees de ls taches 
        $data = json_decode(file_get_contents('php://input'), true);
        //recuperer puis decoder le token
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = explode('Bearer', $authorizationHeader)[1];
        $response = decodeJWT($token);
        $array = json_decode(json_encode($response), true);

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
            exit();
        }
        try {
            $data['user_id'] = $array['user']['id'];
            $task = new BD($data);
            $response = $task->insert('tasks', 'id');

            if ($response) {
                $response_calendar = '';
                //verifier si un calendier existe a cette date
                $calendar = new BD([
                    'start_date' => $data['start_date'],
                    'user_id' => $array['user']['id']
                ]);
                $response_calendar = $calendar->search('calendar', 'id', 'start_date');
                // si non : cree un nouveau calendier
                if (!$response_calendar) {
                    $calendar = new BD([
                        'start_date' => $data['start_date'],
                        'user_id' => $array['user']['id']
                    ]);
                    $response_calendar = $calendar->insert('calendar', 'id');
                }
                if ($response_calendar) {
                    //echo json_encode($response_calendar);
                    $schedule = new BD([
                        'calendar_id' => $response_calendar['id'], 
                        'task_id' => $response['id']
                    ]);
                    $schedule->insert('schedules', 'task_id');
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

    public function update ($id, $project_id=null) {
        //recuperer les donnees de ls taches 
        $data = json_decode(file_get_contents('php://input'), true);
        // recuperer les infos du users
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = explode('Bearer', $authorizationHeader)[1];
        $response = decodeJWT($token);
        $array = json_decode(json_encode($response), true);
        $params = ['id' => $id, 'user_id' => $array['user']['id']];
        try {
            $task = new BD($data);
            $res = $task->update('tasks', $params);
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
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = explode('Bearer', $authorizationHeader)[1];
        $response = decodeJWT($token);
        $array = json_decode(json_encode($response), true);
        try {
            $params = ['id' => $id, 'user_id' => $array['user']['id']];
            $params_schedule = ['task_id' => $id];
            $task = new BD($id);
            $response_schedule = $task->delete('schedules', $params_schedule);
            if ($response_schedule) {
                $response = $task->delete('tasks', $params);
                if ($response) {
                    header("HTTP/1.1 201 Deleted");
                    echo "The task has been deleted successfully";
                } else {
                    header("HTTP/1.1 500 SERVER ERROR");
                }
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