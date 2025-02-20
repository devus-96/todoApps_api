<?php 
//cette ligne est pour l'empecher
declare(strict_types = 1);

require $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/calendar.php';


class CalendarControllers {
    public function get () {
        try {
            $data = [
                "startdate" => $_GET['startdate'],
                "enddate" => $_GET['enddate']
            ];
            $response = new Calendar($data);
    
            $tasks = $response->selectByMonthAndWeek();

            if ($tasks) {
                header('HTTP/1.1 200 OK');
                echo json_encode($tasks);
            } else {
                header('HTTP/1.1 401 Unauthorized');
                echo "task not found at this date !!!";
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function post () {
        try {
            $date = ["date" => $_GET['date']]; // recuperer l'id de la tache 
            $response = new BD($date);
            $tasks = $response->search("task", "*", "date");

            $_response = new Calendar($date);
            $_response->insertCalendar();
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }
}

?>