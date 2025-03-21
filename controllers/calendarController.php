<?php 
//cette ligne est pour l'empecher
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/calendar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/user_info.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';


class CalendarControllers {
    public function get ($date) {
        try {
            // recuperer et decoder le token
            $array = get_user_info();

            $params = ['date' => $date, 'user_id' => $array['user']['id']];

            $calendar = new Calendar('');
            $tasks = $calendar->sort_by_day($params);

            if ($tasks) {
                http_response(200, json_encode($tasks));
            } else {
                http_response(400, "no tasks exist at this date");
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
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
                http_response(200, json_encode($tasks));
            } else {
                http_response(500, "we could not get the tasks");
            }

        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }

    public function update (string $id) {
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $params = ['id' => $id];
            $calendar = new BD($data);
            $response = $calendar->update('calendar', $params);
            if ($response) {
                http_response(201, "The task has been updated successfully", "Updated");
            } else {
                http_response(500, "we could not update the task");
            }
        } catch (PDOException $e) {
            http_response(500, "Database error:".$e->getMessage());
        }
    }
}

?>