<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/bdmanage.php';

class Calendar extends BD {
   public function selectByMonthAndWeek  ($params) {
        $get = $this->pdo->prepare("SELECT * FROM calendar WHERE startdate BETWEEN :startdate AND :enddate AND user_id = :userId");
        $get->execute([
            ":startdate" => $params['startdate'],
            ":enddate" => $params['enddate'],
            ":userId" => $params['id']
        ]);
        $response = $get->fetch(PDO::FETCH_ASSOC);

        return $response;
   }

   public function selectByDay ($params, $table, $select) {
    $get = $this->pdo->prepare("SELECT $select FROM $table WHERE start_date = :startdate AND user_id = :userId");
    $get->execute([
        ":startdate" => $params['start_date'],
        ":userId" => $params['id'],
    ]);
    $response = $get->fetch(PDO::FETCH_ASSOC);
    echo json_encode($response);
    return $response;
   }

   public function sort ($params) {
    $get = $this->pdo->prepare('SELECT * FROM tasks
                JOIN schedules ON tasks.tasks.id = schedules.tasks_id
                JOIN calendar ON schedules.calendar_id = calenadar.id
                WHERE calendar.users_id = :userId AND calendar.start_date = date'
            );
    $get->execute([
        ":date" => $params['startdate'],
        ":userId" => $params['id'],
    ]);
    $response = $get->fetch(PDO::FETCH_ASSOC);

    return $response;
   }
}

?>