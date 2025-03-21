<?php 
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';

class TaskController {
    public function get ($priority, $status) {
        $params = ['priority' => $priority, 'status' => $status];
        $array = get_user_info();
        try { 
            $params['user_id'] = $array['id'];
            $tasks = new BD($params);
            $response = $tasks->get("tasks", "*");
            if ($response) {
                header('HTTP/1.1 200 OK');
                echo json_encode($response);
            } else {
                header('HTTP/1.1 401 Unauthorized');
                echo "task not found at this date !!!";
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function create ($project_id = null) {
        //recuperer les donnees de ls taches 
        $data = json_decode(file_get_contents('php://input'), true);
        //recuperer puis decoder le token
        $array = get_user_info();

        // Vérifier si les données sont valides et contiennent les clés obligatoires
        $requiredKeys = ['name', 'tags', 'priority', 'start_time', 'end_time', 'start_date', 'status'];
        $missingKeys = array_diff($requiredKeys, array_keys($data));

        if (!empty($missingKeys)) {
            // Renvoyer une erreur 400 si des champs obligatoires sont manquants
            http_response(400, json_encode(['error' => 'Missing required fields', 'missing_fields' => array_values($missingKeys)]));
            exit();
        }
        try {
            $data['user_id'] = $array['id'];
            $task = new BD($data);
            $response = $task->insert('tasks', 'id');

            if ($response) {
                $response_calendar = '';
                //verifier si un calendier existe a cette date
                $calendar = new BD([
                    'start_date' => $data['start_date'],
                    'user_id' => $array['id']
                ]);
                $response_calendar = $calendar->get('calendar', 'id');
                // si non : cree un nouveau calendier
                if (!$response_calendar) {
                    $calendar = new BD([
                        'start_date' => $data['start_date'],
                        'user_id' => $array['id']
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
                http_response(201, "The task has been created successfully");
            } else {
                trigger_error("error", E_USER_ERROR);
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    } 

    public function update ($id) {
        try {
            //recuperer les donnees de ls taches 
            $data = json_decode(file_get_contents('php://input'), true);
            // recuperer les infos du users
            $array = get_user_info();
            $params = ['id' => $id, 'user_id' => $array['id']];
            //database
            $task = new BD($data);
            $res = $task->update('tasks', $params);
            if ($res) {
                http_response(201, "The task has been updated successfully", "Updated");
            } else {
                http_response(500, "failed update calendar");
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function delete ($id) {
        $array = get_user_info();
        try {
            $params = ['id' => (int)$id, 'user_id' => (int)$array['id']];
            $params_schedule = ['task_id' => (int)$id];
            // recuperer la colonne que on vx supprimer
            $schedules = new BD($params_schedule);
            $response_schedules = $schedules->search('schedules', 'calendar_id', 'task_id');
            // recuperer la colonne que on vx supprimer
            $params_calendar = ['calendar_id' => $response_schedules['calendar_id']];
            $calendar = new BD($params_calendar);
            // supprime la ligne de la table schedules
            $task = new BD($id);
            $response_deleted_schedule = $task->delete('schedules', $params_schedule);
            if ($response_deleted_schedule && $response_schedules) {
                $calendars = $calendar->search('schedules', '*', 'calendar_id');
                if (!$calendars) {
                    $response_deleted_calendar = $calendar->delete('calendar', [
                        'id' => $response_schedules['calendar_id'],
                        'user_id' => $array['id']
                    ]);
                    if (!$response_deleted_calendar) {
                        http_response(500, "failed delete calendar");
                    }
                }
                $response = $task->delete('tasks', $params);
                if ($response) {
                    http_response(201, "The task has been deleted successfully", "Deleted");
                } else {
                    http_response(500, "failed delete calendar");
                }
            } else {
                http_response(500, "failed delete task");
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }
}


?>