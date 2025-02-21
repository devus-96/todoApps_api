<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bdmanage.php';

class Calendar extends BD {
   public function selectByMonthAndWeek  () {
        $get = $this->pdo->prepare("SELECT * FROM calendar WHERE startdate BETWEEN :startdate AND :enddate");
        $get->execute([
            ":startdate" => $this->data['startdate'],
            ":enddate" => $this->data['enddate']
        ]);
        $response = $get->fetch(PDO::FETCH_ASSOC);

        return $response;
   }

   public function insertCalendar () {
        $get = $this->pdo->prepare("SELECT * FROM task WHERE start_date = :date");
        $get->execute([
            ":date" => $this->data['startdate'],
        ]);
        $response = $get->fetch(PDO::FETCH_ASSOC);

        $calendar = json_encode($response);

        $post  = $this->pdo->prepare("INSERT INTO calendar ('date', 'tasks') VALUES (':date', ':tasks')");
        $post->execute([
            ':date' => $response['date'],
            ':tasks' => $calendar
        ]);
   }
}

?>