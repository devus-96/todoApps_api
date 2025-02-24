<?php
//php essayera tant que possible de convertir une valeur de mauvais type en une type ettendu
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/calendar.php';

class ScheduleController {

    public function get ($params) {
        try {
            $schedule = new Calendar(null);
            $response = $schedule->sort($params);
            if ($response) {
                header('HTTP/1.1 200 OK');
                echo json_encode($response);
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
                echo "something went wrong";
            }
        } catch (PDOException $e) {
            echo $e;
        }
    }
    
    public function create ($params) {
        try {
            //lors de la creation d'une tache et apres verification de l'existance ou creation d'un calendier 
            //recupere les id
            $tasks = new Calendar([]);
            $tasks_response = $tasks->selectByDay($params, 'tasks', 'id');

            $calendar = new Calendar([]);
            $calenadar_response = $calendar->selectByDay($params, 'calendar', 'id');

            $ids = [
                'task_id' => $tasks_response['id'],
                'calendar_id' => $calenadar_response['id']
            ];

            echo json_encode($tasks_response);

            $schedule = new BD($ids);

            $response = $schedule->insert('schedules');

            if ($response) {
                echo 'the tasks programmed a summer informed';
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
}

?>