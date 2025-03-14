<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';

class Calendar extends BD {
   public function sort_by_day ($params) {
    $get = $this->pdo->prepare('SELECT * FROM tasks
                JOIN schedules ON tasks.id = schedules.task_id
                JOIN calendar ON schedules.calendar_id = calendar.id
                WHERE calendar.user_id = :userId AND calendar.start_date = :date'
            );
    $get->execute([
        ":date" => $params['date'],
        ":userId" => $params['user_id'],
    ]);
    $response = $get->fetchAll(PDO::FETCH_CLASS);
    return $response;
   }

   public function sort_by_month_week ($params) {
    $get = $this->pdo->prepare('SELECT * FROM tasks
                JOIN schedules ON tasks.id = schedules.task_id
                JOIN calendar ON schedules.calendar_id = calendar.id
                WHERE calendar.user_id = :userId AND calendar.start_date BETWEEN :start_date AND :end_date'
            );
    $get->execute([
        ":start_date" => $params['start_date'],
        ":end_date" => $params['end_date'],
        ":userId" => $params['user_id'],
    ]);
    $response = $get->fetchAll(PDO::FETCH_CLASS);
    return $response;
   }


}

?>