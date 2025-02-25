<?php 
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/calendar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';


class CalendarControllers {
    public function get ($date) {
        try {
            // recuperer et decoder le token
            $array = get_user_info();

            $params = ['date' => $date, 'user_id' => $array['user']['id']];

            $calendar = new Calendar('');
            $tasks = $calendar->sort_by_day($params);

            if ($tasks) {
                header('HTTP/1.1 200 OK');
                echo json_encode($tasks);
            } else {
                header("HTTP/1.1 400 BAD REQUEST");
                echo "no tasks exist at this date";
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 SERVER ERROR");
            echo json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAll ($start_date, $end_date) {
        try {
            // recuperer et decoder le token
            $array = get_user_info();

            $params = [
                'start_date' => $start_date, 
                'end_date' => $end_date,
                'user_id' => $array['user']['id']
            ];

            $calendar = new Calendar('');
            $tasks = $calendar->sort_by_month_week($params);

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

    public function update (string $id) {
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $params = ['id' => $id];
            $calendar = new BD($data);
            $response = $calendar->update('calendar', $params);
            if ($response) {
                header("HTTP/1.1 201 Updated");
                echo "The task has been updated successfully";
            } else {
                header("HTTP/1.1 500 SERVER ERROR");
                echo "we could not update the task";
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