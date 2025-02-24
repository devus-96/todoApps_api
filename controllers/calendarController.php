<?php 
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/calendar.php';


class CalendarControllers {
    public function get ($params) {
        try {
            $response = new Calendar(null);
            $tasks = $response->selectByDay($params, 'calendar', '*');

            if ($tasks) {
                header('HTTP/1.1 200 OK');
                echo json_encode($tasks);
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
                echo "something went wrong";
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    //la recuperation pour les semaine e les moi
    public function getAll ($params) {
        try {
            $response = new Calendar(null);
    
            $tasks = $response->selectByMonthAndWeek($params);

            if ($tasks) {
                header('HTTP/1.1 200 OK');
                echo json_encode($tasks);
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
                echo "something went wrong";
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create ($start_date) {
        try {
            $response = new BD($start_date);
            $tasks = $response->insert('calendar');
            if ($tasks) {
                echo 'new calendar create';
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
                echo "something went wrong";
            }
        } catch (PDOException $e) {
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update ($params) {
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $calendar = new BD($data);
            $response = $calendar->update('calendar', $params);
            if ($response) {
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

    public function delete ($parans) {

    }
}

?>